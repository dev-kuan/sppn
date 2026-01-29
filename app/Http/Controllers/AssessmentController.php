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
use App\Services\AssessmentService;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreAssessmentRequest;
use App\Http\Requests\ImportAssessmentRequest;
use App\Http\Requests\RejectAssessmentRequest;
use App\Http\Requests\UpdateObservationRequest;

class AssessmentController extends Controller
{

    protected $assessmentService;

    public function __construct(AssessmentService $assessmentService) {
        $this->assessmentService = $assessmentService;
    }

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
    public function store(StoreAssessmentRequest $request)
    {
        // $this->authorize('create-penilaian');
        try {
           $assessment = $this->assessmentService->storeAssessment($request->validated());

           return redirect()
           ->route('assessments.edit', $assessment)
           ->with('success', 'Penilaian berhasil dibuat. Silakan lanjutkan pengisian.');
        } catch (\Throwable $e) {
            Log::error('Penilaian gagal disimpan: ', [
                'error' => $e->getMessage()
            ]);

            return back()
            ->withInput()
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
    public function updateObservation(UpdateObservationRequest $request, Assessment $assessment)
    {
        // $this->authorize('edit-penilaian');
        try {
            $this->assessmentService->updateObservation($assessment, $request->validated());

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
        } catch (\Throwable $e) {
            Log::error('Observation gagal diubah: ', [
                'assessment_id' => $assessment->id,
                'error:' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data',
            ], 500);
        }
    }
    public function submit(Assessment $assessment)
    {
        // $this->authorize('submit-penilaian');

        try {
            $this->assessmentService->submitAssessment($assessment);

            return redirect()
            ->route('assessments.show', $assessment)
            ->with('success', 'Penilaian berhasil disubmit untuk persetujuan.');
        } catch (\Throwable $e) {
            Log::error('Penilaian gagal disubmit: ' , [
                'assessment_id' => $assessment->id,
                'error: ' => $e->getMessage()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat submit penilaian.');
        }
    }
    public function approve(Assessment $assessment)
    {
        // $this->authorize('approve-penilaian');

        try {
            $this->assessmentService->approveAssessment($assessment);

            return redirect()
            ->route('assessments.show', $assessment)
            ->with('success', 'Penilaian berhasil disetujui.');
        } catch (\Throwable $e) {
            Log::error('Penilaian gagal disetujui: ' , [
                'assessment_id' => $assessment->id,
                'error: ' => $e->getMessage()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menyetujui penilaian.');
        }
    }
    public function reject(RejectAssessmentRequest $request, Assessment $assessment)
    {
        // $this->authorize('approve-penilaian');
        try {
            $this->assessmentService->rejectAssessment($assessment, $request->validated);

            return redirect()
            ->route('assessments.show', $assessment)
            ->with('info', 'Penilaian ditolak. Petugas dapat memperbaiki dan submit kembali.');
        } catch (\Throwable $e) {
            Log::error('Penilaian gagal ditolak: ' , [
                'assessment_id' => $assessment->id,
                'error: ' => $e->getMessage()
            ]);

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
            Log::error('Template gagal diexport: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengunduh template.');
        }
    }

    public function import(ImportAssessmentRequest $request, Assessment $assessment)
    {
        // $this->authorize('edit-penilaian');

        try {
            $result = $this->assessmentService->importAssessment(
                $assessment,
                $request->validated()->file('file')
            );

            if (! $result->success) {
                $errorMessage = "Import selesai dengan beberapa error:\n";
                foreach ($result->errors as $error) {
                    $errorMessage .= "- {$error}\n";
                }

                return back()->with('warning', nl2br($errorMessage));
            }

            return redirect()
                ->route('assessments.edit', $assessment)
                ->with(
                    'success',
                    "Data berhasil diimport. Total {$result->successCount} observasi diproses."
                );

        } catch (\Throwable $e) {
            Log::error('Penilaian gagal diimport', [
                'assessment_id' => $assessment->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with(
                'error',
                'Terjadi kesalahan saat mengimport file.'
            );
        }
    }
}
