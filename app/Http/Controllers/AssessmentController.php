<?php

namespace App\Http\Controllers;

use App\Exports\AssessmentTemplateExport;
use App\Http\Requests\ImportAssessmentRequest;
use App\Http\Requests\RejectAssessmentRequest;
use App\Http\Requests\StoreAssessmentRequest;
use App\Http\Requests\UpdateObservationRequest;
use App\Models\Assessment;
use App\Models\AssessmentVariabel;
use App\Models\CommitmentStatement;
use App\Models\DailyObservation;
use App\Models\Inmate;
use App\Models\ObservationItem;
use App\Services\AssessmentService;
use App\Services\ConditionalItemService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class AssessmentController extends Controller
{

    protected $assessmentService;
    protected $conditionalItemService;

    public function __construct(AssessmentService $assessmentService, ConditionalItemService $conditionalItemService)
    {
        $this->assessmentService = $assessmentService;
        $this->conditionalItemService = $conditionalItemService;
    }

    public function index(Request $request)
    {

        // $this->authorize('view-penilaian');

        $query = Assessment::with(['inmate', 'creator'])
            ->whereHas('inmate');

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

        $tanggalPenilaian = $request->has('tanggal_penilaian')
            ? Carbon::parse($request->tanggal_penilaian)
            : Carbon::now();

        // Check if need to show conditional items modal
        $showConditionalModal = $this->conditionalItemService->shouldShowModal($tanggalPenilaian);
        $conditionalItems = $this->conditionalItemService->getConditionalItems();

        // inmate not selected? show selected page
        if (!$inmate) {
            $inmates = Inmate::aktif()->orderBy('nama')->get();
            return view('assessments.select-inmate', compact(
                'inmates',
                'showConditionalModal',
                'conditionalItems',
                'tanggalPenilaian'
            ));
        }

        // Check if assessment already exists
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $existingAssessment = Assessment::where('inmate_id', $inmate->id)
            ->whereMonth('tanggal_penilaian', $currentMonth)
            ->whereYear('tanggal_penilaian', $currentYear)
            ->withoutTrashed()
            ->first();


        if ($existingAssessment) {
            return redirect()->route('assessments.edit', $existingAssessment)
                ->with('info', 'Penilaian untuk bulan ini sudah ada. Anda dapat melanjutkan mengisinya.');
        }

        return view('assessments.create', compact(
            'inmate',
            'showConditionalModal',
            'conditionalItems',
            'tanggalPenilaian'
        ));
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


        $variabelMapping = [
            'Pembinaan Kepribadian' => 'kepribadian',
            'Pembinaan Kemandirian' => 'kemandirian',
            'Penilaian Sikap' => 'sikap',
            'Penilaian Kondisi Mental' => 'mental',
            'Pernyataan Komitmen' => 'komitmen'
        ];

        // Format data observationItems dengan struktur yang benar
        $observationItemsArray = [];

        foreach ($variabels as $variabel) {
            $variabelNamaLower = strtolower($variabel->nama);
            $variabelKey = $variabelMapping[$variabelNamaLower] ?? $variabelNamaLower;

            foreach ($variabel->aspect as $aspek) {
                foreach ($aspek->observationItems as $item) {
                    $frequency = $item->getFrequencyForMonth($assessment->tanggal_penilaian);
                    $observationItemsArray[] = [
                        'id' => $item->id,
                        'bobot' => (float) $item->bobot,
                        'frekuensi' => (int) $frequency,
                        'variabel_id' => $variabel->id,
                        'variabel_nama' => $variabelNamaLower,
                        'variabel_key' => $variabelKey,
                        'aspek_id' => $aspek->id,
                        'aspek_nama' => $aspek->nama
                    ];
                }
            }
        }

        // Prepare existing observations data
        $observationData = [];
        foreach ($assessment->dailyObservations as $obs) {
            $observationData[$obs->observation_item_id][$obs->hari] = $obs;
        }

        $observationSummary = [];

        foreach ($observationData as $itemId => $days) {
            $observationSummary[$itemId] = collect($days)
                ->where('is_checked', true)
                ->count();
        }

        // Prepare checked observations untuk inisialisasi frontend
        $checkedObservations = $assessment->dailyObservations()
            ->where('is_checked', true)
            ->get()
            ->map(function ($obs) {
                return [
                    'observation_item_id' => $obs->observation_item_id,
                    'hari' => $obs->hari,
                    'is_checked' => true
                ];
            })
            ->toArray();

        return view('assessments.show', compact(
            'assessment',
            'variabels',
            'observationData',
            'observationSummary',
            'daysInMonth',
            'checkedObservations'
        ));
    }
    public function edit(Assessment $assessment)
    {
        // only draft & reject can be edited
        if (!in_array($assessment->status, ['draf', 'ditolak'])) {
            return redirect()->route('assessments.show', $assessment)
                ->with('error', 'Penilaian yang sudah disubmit tidak dapat diedit.');
        }

        $assessment->load('inmate', 'dailyObservations');

        // Get observation structure
        $variabels = AssessmentVariabel::with(['aspect.observationItems' => function ($q) {
            $q->aktif()->ordered();
        }])->get();

        $daysInMonth = $assessment->tanggal_penilaian->daysInMonth();

        // ✅ Mapping nama variabel ke key yang pendek
        $variabelMapping = [
            'Pembinaan Kepribadian' => 'kepribadian',
            'Pembinaan Kemandirian' => 'kemandirian',
            'Penilaian Sikap' => 'sikap',
            'Penilaian Kondisi Mental' => 'mental',
            'Pernyataan Komitmen' => 'komitmen'
        ];

        // Format data observationItems dengan struktur yang benar
        $observationItemsArray = [];

        foreach ($variabels as $variabel) {
            $variabelNamaLower = strtolower($variabel->nama);
            $variabelKey = $variabelMapping[$variabelNamaLower] ?? $variabelNamaLower;

            foreach ($variabel->aspect as $aspek) {
                foreach ($aspek->observationItems as $item) {
                    $frequency = $item->getFrequencyForMonth($assessment->tanggal_penilaian);

                    $observationItemsArray[] = [
                        'id' => $item->id,
                        'bobot' => (float) $item->bobot,
                        'frekuensi' => (int) $frequency,
                        'variabel_id' => $variabel->id,
                        'variabel_nama' => $variabelNamaLower,
                        'variabel_key' => $variabelKey,
                        'aspek_id' => $aspek->id,
                        'aspek_nama' => $aspek->nama
                    ];
                }
            }
        }

        // Prepare existing observations data
        $observationData = [];
        foreach ($assessment->dailyObservations as $obs) {
            $observationData[$obs->observation_item_id][$obs->hari] = $obs;
        }

        // Prepare checked observations untuk inisialisasi frontend
        $checkedObservations = $assessment->dailyObservations()
            ->where('is_checked', true)
            ->get()
            ->map(function ($obs) {
                return [
                    'observation_item_id' => $obs->observation_item_id,
                    'hari' => $obs->hari,
                    'is_checked' => true
                ];
            })
            ->toArray();

        return view('assessments.edit', compact(
            'assessment',
            'variabels',
            'daysInMonth',
            'observationItemsArray',
            'observationData',
            'checkedObservations'
        ));
    }
    public function destroy(Assessment $assessment)
    {
        // $this->authorize('delete-narapidana');

        try {
            // Soft delete
            $assessment = $this->assessmentService->deleteAssessment($assessment);

            return redirect()
                ->route('assessments.index')
                ->with('success', 'Data penilaian dihapus.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function updateObservation(UpdateObservationRequest $request, Assessment $assessment)
    {
        // $this->authorize('edit-penilaian');
        try {
            $updatedAssessment = $this->assessmentService
                ->updateObservation($assessment, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'scores' => [
                    'kepribadian' => $updatedAssessment->skor_kepribadian,
                    'kemandirian' => $updatedAssessment->skor_kemandirian,
                    'sikap' => $updatedAssessment->skor_sikap,
                    'mental' => $updatedAssessment->skor_mental,
                    'komitmen' => $updatedAssessment->skor_komitmen,
                    'total' => $updatedAssessment->skor_total,
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

    public function updateAspectNote(Request $request, Assessment $assessment)
    {
        $validated = $request->validate([
            'aspect_id' => 'required|exists:assessment_aspects,id',
            'catatan' => 'required|string|max:1000',
        ]);

        try {
            $assessmentScore = $this->assessmentService->updateAspectScoreCatatan(
                $assessment,
                $validated['aspect_id'],
                $validated['catatan']
            );

            return response()->json([
                'success' => true,
                'message' => 'Catatan aspek berhasil disimpan',
                'data' => $assessmentScore,
            ]);
        } catch (\Throwable $e) {
            Log::error('Catatan aspek gagal disimpan: ', [
                'assessment_id' => $assessment->id,
                'aspect_id' => $validated['aspect_id'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan catatan',
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
            Log::error('Penilaian gagal disubmit: ', [
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
            Log::error('Penilaian gagal disetujui: ', [
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
            $this->assessmentService->rejectAssessment($assessment, $request->validated());

            return redirect()
                ->route('assessments.show', $assessment)
                ->with('info', 'Penilaian ditolak. Petugas dapat memperbaiki dan submit kembali.');
        } catch (\Throwable $e) {
            Log::error('Penilaian gagal ditolak: ', [
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
                $assessment->tanggal_penilaian->format('d-m-Y') . '.xlsx';

            return Excel::download(
                new AssessmentTemplateExport($assessment),
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
                $request->file('file')
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

    public function setConditionalItems(Request $request)
    {
        $request->validate([
            'selections' => 'required|array',
            'tanggal_penilaian' => 'required|date',
            'dont_show_again' => 'boolean',
        ]);

        try {
            $tanggalPenilaian = Carbon::parse($request->tanggal_penilaian);

            $result = $this->conditionalItemService->updateConditionalBobots(
                $request->selections,
                $tanggalPenilaian
            );

            // Handle "don't show again" option
            if ($request->boolean('dont_show_again')) {
                $this->conditionalItemService->skipModalThisMonth($tanggalPenilaian);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan kegiatan berhasil disimpan',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to set conditional items', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Skip conditional items modal for current month
     */
    public function skipConditionalModal(Request $request)
    {
        $request->validate([
            'tanggal_penilaian' => 'required|date',
        ]);

        try {
            $tanggalPenilaian = Carbon::parse($request->tanggal_penilaian);
            $this->conditionalItemService->skipModalThisMonth($tanggalPenilaian);

            return response()->json([
                'success' => true,
                'message' => 'Modal tidak akan ditampilkan lagi bulan ini',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
            ], 500);
        }
    }
}
