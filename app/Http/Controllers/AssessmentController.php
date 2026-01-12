<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Inmate;
use App\Models\Assessment;
use Illuminate\Http\Request;
use App\Models\ObservationItem;
use App\Models\DailyObservation;
use App\Models\AssessmentVariabel;
use Illuminate\Support\Facades\DB;
use App\Models\CommitmentStatement;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class AssessmentController extends Controller
{

    public function index(Request $request)
    {

        // $this->authorize('view-penilaian');

        $query = Assessment::with(['inmate', 'creator']);

        // filter by inmate
        if ($request->has('inmate_id') && $request->inmate_id != '') {
            $query->where('inmate_id', $request->inmate_id);
        };

        // filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // filter by month/year
        if ($request->has(key: 'month') && $request->month != '') {
            $query->whereMonth('tanggal_penilaian', $request->month);
        }

        if ($request->has(key: 'year') && $request->year != '') {
            $query->whereYear('tanggal_penilaian', $request->year);
        }

        // created by petugas
        if (auth()->user()->isPetugasInput()) {
            $query->where('created_by', auth()->id());
        }

        $assessments = $query->latest('tanggal_penilaian')
            ->paginate(15)
            ->withQueryString();

        //  get inmate aktif
        $inmates = Inmate::aktif()->orderBy('nama')->get();

        return view('assessments.index', compact('assessments', 'inmates'));
    }
    public function create(Request $request)
    {
        // $this->authorize('create-penilaian');

        $inmateId = $request->get('inmate_id');
        $inmate = $inmateId ? Inmate::findOrFail($inmateId) : null;

        // inmate not selected? show selected page
        if (!$inmate) {
            $inmates = Inmate::aktif()->orderBy('nama')->get();
            return view('assessments.select-inmate', compact('inmates'));
        }

        // Check if assessment already exists
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $existingAssessment = Assessment::where('inmate_id', $inmate->id)
            ->whereMonth('tanggal_penilaian', $currentMonth)
            ->whereYear('tanggal_penilaian', $currentYear)
            ->first();

        if ($existingAssessment) {
            return redirect()->route('assessments.edit', $existingAssessment)
                ->with('info', 'Penilaian untuk bulan ini sudah ada. Anda dapat melanjutkan mengisinya.');
        }

        return view('assessments.create', compact('inmate'));
    }
    public function store(Request $request)
    {
        // $this->authorize('create-penilaian');

        $validated = $request->validate([
            'inmate_id' => 'required|exists:inmates,id',
            'tanggal_penilaian' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            // Check duplicate
            $exists = Assessment::where('inmate_id', $validated['inmate_id'])
                ->whereMonth('tanggal_penilaian', Carbon::parse($validated['tanggal_penilaian'])->month)
                ->whereYear('tanggal_penilaian', Carbon::parse($validated['tanggal_penilaian'])->year)
                ->exists();

            if ($exists) {
                return back()->with('error', 'Penilaian untuk bulan tersebut sudah ada.');
            }

            // Create assessment
            $assessment = Assessment::create([
                'inmate_id' => $validated['inmate_id'],
                'tanggal_penilaian' => $validated['tanggal_penilaian'],
                'status' => 'draf',
                'created_by' => auth()->id(),
            ]);

            // Initialize daily observations
            $this->initializeDailyObservations($assessment);

            // Initialize commitment statements
            CommitmentStatement::create([
                'assessment_id' => $assessment->id,
                'jenis' => 'nkri',
                'is_signed' => false,
            ]);

            CommitmentStatement::create([
                'assessment_id' => $assessment->id,
                'jenis' => 'narkoba',
                'is_signed' => false,
            ]);

            //  monitoring user activity
            activity()
                ->performedOn($assessment)
                ->causedBy(auth()->user())
                ->log('Penilaian baru dibuat untuk: ' . $assessment->inmate->nama);

            DB::commit();

            return redirect()->route('assessments.edit', $assessment)
                ->with('success', 'Penilaian berhasil dibuat. Silakan lanjutkan pengisian.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating assessment: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat membuat penilaian.');
        }
    }
    public function show(Assessment $assessment)
    {
        // $this->authorize('view-penilaian');

        $assessment->load([
            'inmate.crimeType',
            'creator',
            'approver',
            'assessmentScores.variabel',
            'assessmentScores.aspect',
            'commitmentStatements',
            'commitmentRecommendations.recommender',
            'commitmentSignatures.user'
        ]);

        // Get observation data
        $variabels = AssessmentVariabel::with(['aspect.observationItems' => function ($q) {
            $q->aktif()->ordered();
        }])->get();

        $observationData = [];
        $daysInMonth = $assessment->tanggal_penilaian->daysInMonth;

        foreach ($variabels as $variabel) {
            foreach ($variabel->aspect as $aspek) {
                foreach ($aspek->observationItems as $item) {
                    $observations = DailyObservation::where('assessment_id', $assessment->id)
                        ->where('observation_item_id', $item->id)
                        ->get()
                        ->keyBy('hari');

                    $observationData[$item->id] = $observations;
                }
            }
        }

        return view('assessments.show', compact('assessment', 'variabels', 'observationData', 'daysInMonth'));
    }
    public function edit(Assessment $assessment)
    {
        // $this->authorize('edit-penilaian');

        // only draft & reject can be edited
        if (!in_array($assessment->status, ['draf', 'ditolak'])) {
            return redirect()->route('assessments.show', $assessment)
                ->with('error', 'Penilaian yang sudah disubmit tidak dapat diedit.');
        }

        $assessment->load('inmate');

        // Get observation structure
        $variabels = AssessmentVariabel::with(['aspect.observationItems' => function ($q) {
            $q->aktif()->ordered();
        }])->get();

        $daysInMonth = $assessment->tanggal_penilaian->daysInMonth;

        // Get existing observations
        $observationData = [];
        foreach ($variabels as $variabel) {
            foreach ($variabel->aspect as $aspek) {
                foreach ($aspek->observationItems as $item) {
                    $observations = DailyObservation::where('assessment_id', $assessment->id)
                        ->where('observation_item_id', $item->id)
                        ->get()
                        ->keyBy('hari');

                    $observationData[$item->id] = $observations;
                }
            }
        }

        return view('assessments.edit', compact('assessment', 'variabels', 'observationData', 'daysInMonth'));
    }
    public function updateObservation(Request $request, Assessment $assessment)
    {
        // $this->authorize('edit-penilaian');

        $validated = $request->validate([
            'observation_item_id' => 'required|exists:observation_items,id',
            'hari' => 'required|integer|min:1|max:31',
            'is_checked' => 'required|boolean',
            'catatan' => 'nullable|string',
        ]);

        try {
            $observation = DailyObservation::updateOrCreate(
                [
                    'assessment_id' => $assessment->id,
                    'observation_item_id' => $validated['observation_item_id'],
                    'hari' => $validated['hari'],
                ],
                [
                    'is_checked' => $validated['is_checked'],
                    'catatan' => $validated['catatan'],
                ]
            );

            // Recalculate scores
            $assessment->calculateScores();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'scores' => [
                    'kepribadian' => $assessment->skor_kepribadian,
                    'kemandirian' => $assessment->skor_kemandirian,
                    'sikap' => $assessment->skor_sikap,
                    'mental' => $assessment->skor_mental,
                    'total' => $assessment->skor_total,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating observation: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data',
            ], 500);
        }
    }
    public function submit(Assessment $assessment)
    {
        // $this->authorize('submit-penilaian');

        // only draft can be submited
        if ($assessment->status !== 'draf') {
            return back()->with('error', 'Hanya penilaian dengan status draf yang dapat disubmit.');
        }

        DB::beginTransaction();
        try {
            $assessment->update([
                'status' => 'disubmit',
                'submitted_at' => now(),
            ]);

            //  monitoring user activity
            activity()
                ->performedOn($assessment)
                ->causedBy(auth()->user())
                ->log('Penilaian disubmit untuk: ' . $assessment->inmate->nama);

            DB::commit();

            return redirect()->route('assessments.show', $assessment)
                ->with('success', 'Penilaian berhasil disubmit untuk persetujuan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error submitting assessment: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat submit penilaian.');
        }
    }
    public function approve(Assessment $assessment)
    {
        // $this->authorize('approve-penilaian');

        // only submited can be approved
        if ($assessment->status !== 'disubmit') {
            return back()->with('error', 'Hanya penilaian yang sudah disubmit yang dapat disetujui.');
        }

        DB::beginTransaction();
        try {
            $assessment->update([
                'status' => 'diterima',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            //  monitoring user activity
            activity()
                ->performedOn($assessment)
                ->causedBy(auth()->user())
                ->log('Penilaian disetujui untuk: ' . $assessment->inmate->nama);

            DB::commit();

            return redirect()->route('assessments.show', $assessment)
                ->with('success', 'Penilaian berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving assessment: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menyetujui penilaian.');
        }
    }
    public function reject(Request $request, Assessment $assessment)
    {
        // $this->authorize('approve-penilaian');

        $validated = $request->validate([
            'catatan' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $assessment->update([
                'status' => 'ditolak',
                'catatan' => $validated['catatan'],
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            //  monitoring user activity
            activity()
                ->performedOn($assessment)
                ->causedBy(auth()->user())
                ->log('Penilaian ditolak untuk: ' . $assessment->inmate->nama);

            DB::commit();

            return redirect()->route('assessments.show', $assessment)
                ->with('info', 'Penilaian ditolak. Petugas dapat memperbaiki dan submit kembali.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting assessment: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menolak penilaian.');
        }
    }

    public function exportTemplate(Assessment $assessment)
    {
        // $this->authorize('edit-penilaian');

        try {
            $fileName = 'Template_Penilaian_' .
                        $assessment->inmate->no_registrasi . '_' .
                        $assessment->tanggal_penilaian->format('Y-m') . '.xlsx';

            return Excel::download(
                new \App\Exports\AssessmentTemplateExport($assessment),
                $fileName
            );
        } catch (\Exception $e) {
            Log::error('Error exporting template: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengunduh template.');
        }
    }

    public function import(Request $request, Assessment $assessment)
    {
        // $this->authorize('edit-penilaian');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // Max 10MB
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('file');

            $import = new \App\Imports\AssessmentImport($assessment);
            Excel::import($import, $file);

            if ($import->hasErrors()) {
                DB::rollBack();

                $errorMessage = 'Import selesai dengan beberapa error:<br>';
                foreach ($import->getErrors() as $error) {
                    $errorMessage .= '- ' . $error . '<br>';
                }

                return back()->with('warning', $errorMessage);
            }

            //  monitoring user activity
            activity()
                ->performedOn($assessment)
                ->causedBy(auth()->user())
                ->log('Import data penilaian untuk: ' . $assessment->inmate->nama);

            DB::commit();

            return redirect()->route('assessments.edit', $assessment)
                ->with('success', 'Data berhasil diimport. Total ' . $import->getSuccessCount() . ' observasi diproses.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error importing assessment: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat mengimport file: ' . $e->getMessage());
        }
    }
    private function initializeDailyObservations(Assessment $assessment)
    {
        $daysInMonth = $assessment->tanggal_penilaian->daysInMonth;
        $observationItems = ObservationItem::aktif()->get();

        foreach ($observationItems as $item) {
            for ($day = 1; $day <= $daysInMonth; $day++) {
                DailyObservation::create([
                    'assessment_id' => $assessment->id,
                    'observation_item_id' => $item->id,
                    'hari' => $day,
                    'is_checked' => false,
                ]);
            }
        }
    }
}
