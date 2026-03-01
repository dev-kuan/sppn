<?php

namespace App\Http\Controllers;

use App\Exports\AssessmentExport;
use App\Exports\InmateExport;
use App\Models\Assessment;
use App\Models\AssessmentVariabel;
use App\Models\Inmate;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{

private $configPath;

    public function __construct()
    {
        $this->configPath = config_path('institution.php');
    }
    /**
     * Show report generation page
     */
    public function index()
    {

        $inmates = Inmate::aktif()->orderBy('nama')->get();

        return view('reports.index', compact('inmates'));
    }

    /**
     * Generate assessment report (PDF)
     */
    public function generateAssessmentReport(Request $request)
    {

        $validated = $request->validate([
            'assessment_id' => 'required|exists:assessments,id',
        ]);

        $assessment = Assessment::with([
            'inmate.crimeType',
            'creator',
            'approver',
            'assessmentScores.variabel',
            'assessmentScores.aspect',
            'commitmentStatements',
            'commitmentRecommendations.recommender',
            'commitmentSignatures.user'
        ])->findOrFail($validated['assessment_id']);

        // Get observation data
        $variabels = AssessmentVariabel::with(['aspect.observationItems' => function ($q) {
            $q->aktif()->ordered();
        }])->get();

         // Load institution data
        $institution = $this->getInstitutionData();
        $observationData = $this->getObservationData($assessment, $variabels);

        $pdf = Pdf::loadView('reports.assessment-pdf', [
            'assessment' => $assessment,
            'observationData' => $observationData,
            'institution' => $institution,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = 'Laporan_Penilaian_' . $assessment->inmate->nama . '_' .
                    $assessment->tanggal_penilaian->format('d-m-Y') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate monthly report (PDF)
     */
    public function generateMonthlyReport(Request $request)
    {

        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
        ]);

        $assessments = Assessment::with(['inmate', 'creator'])
            ->byMonth($validated['month'], $validated['year'])
            ->diterima()
            ->get();

        $statistics = $this->calculateMonthlyStatistics($assessments);
        $monthName = Carbon::createFromDate($validated['year'], $validated['month'])->format('F_Y');

        $pdf = Pdf::loadView('reports.monthly-pdf', [
            'assessments' => $assessments,
            'statistics' => $statistics,
            'monthName' => $monthName,
            'month' => $validated['month'],
            'year' => $validated['year'],
        ]);

        $pdf->setPaper('a4', 'landscape');

        $filename = 'Laporan_Bulanan_' . $monthName . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate inmate progress report (PDF)
     */
    public function generateInmateProgressReport(Request $request)
    {

        $validated = $request->validate([
            'inmate_id' => 'required|exists:inmates,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $inmate = Inmate::with('crimeType')->findOrFail($validated['inmate_id']);

        $assessments = Assessment::where('inmate_id', $inmate->id)
            ->whereBetween('tanggal_penilaian', [$validated['start_date'], $validated['end_date']])
            ->diterima()
            ->orderBy('tanggal_penilaian', 'asc')
            ->get();

        if ($assessments->isEmpty()) {
            return back()->with('error', 'Tidak ada data penilaian pada periode tersebut.');
        }

        $progressData = $this->calculateProgressData($assessments);

        $pdf = Pdf::loadView('reports.inmate-progress-pdf', [
            'inmate' => $inmate,
            'assessments' => $assessments,
            'progressData' => $progressData,
            'startDate' => Carbon::parse($validated['start_date']),
            'endDate' => Carbon::parse($validated['end_date']),
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = 'Laporan_Progress_' . $inmate->nama . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export assessments to Excel
     */
    public function exportAssessments(Request $request)
    {

        $validated = $request->validate([
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2020',
            'status' => 'nullable|in:draf,disubmit,diterima,ditolak',
        ]);

        $query = Assessment::with(['inmate', 'creator']);

        if (isset($validated['month'])) {
            $query->whereMonth('tanggal_penilaian', $validated['month']);
        }

        if (isset($validated['year'])) {
            $query->whereYear('tanggal_penilaian', $validated['year']);
        }

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $assessments = $query->get();

        $filename = 'Export_Penilaian_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new AssessmentExport($assessments), $filename);
    }

    /**
     * Export inmates to Excel
     */
    public function exportInmates(Request $request)
    {

        $validated = $request->validate([
            'status' => 'nullable|in:aktif,dirilis,dipindahkan',
        ]);

        $query = Inmate::with('crimeType');

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $inmates = $query->get();

        $filename = 'Export_Narapidana_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new InmateExport($inmates), $filename);
    }

    /**
     * Get observation data for report
     */
private function getObservationData($assessment, $variabels)
{
    $observationData = [];

    foreach ($variabels as $variabel) {
        $variabelData = [
            'nama'          => $variabel->nama,
            'aspects'       => [],
            'skor_variabel' => null,
        ];

        $skorVariabelMap = [
            'kepribadian' => 'skor_kepribadian',
            'kemandirian' => 'skor_kemandirian',
            'sikap' => 'skor_sikap',
            'mental' => 'skor_mental',
        ];
        $variabelData['skor_variabel'] = $assessment->assessmentScores
            ->where('variabel_id', $variabel->id)
            ->whereNull('aspect_id')
            ->first()?->skor ?? '-';

        $totalSkorAspek    = 0;
        $jumlahAspek       = count($variabel->aspect);

        foreach ($variabel->aspect as $aspek) {
            $jumlahItemDalamAspek = count($aspek->observationItems);
            $totalItemScore       = 0;

            $aspectData = [
                'nama'       => $aspek->nama,
                'items'      => [],
                'skor_aspek' => 0,
            ];

            foreach ($aspek->observationItems as $item) {
                $observations = $assessment->dailyObservations()
                    ->where('observation_item_id', $item->id)
                    ->get();

                $checkedCount = $observations->where('is_checked', true)->count();
                $frekuensi    = $item->frekuensi;
                $bobot        = $item->bobot ?? 1; // sesuaikan nama kolom bobot

                // Hitung item score
                // itemScore = ((checklist / frekuensi) * bobot) * (100 / jumlah item dalam aspek)
                $itemScore = 0;
                if ($frekuensi > 0 && $jumlahItemDalamAspek > 0) {
                    $itemScore = (($checkedCount / $frekuensi) * $bobot) * (100 / $jumlahItemDalamAspek);
                }

                $percentage = $frekuensi > 0
                    ? round(($checkedCount / $frekuensi) * 100, 2)
                    : 0;

                $totalItemScore += $itemScore;

                $aspectData['items'][] = [
                    'nama_item'     => $item->nama_item,
                    'frekuensi'     => $frekuensi,
                    'bobot'         => $bobot,
                    'checked_count' => $checkedCount,
                    'percentage'    => $percentage,
                    'item_score'    => round($itemScore, 2),
                ];
            }

            // aspectScore = jumlah semua item score dalam aspek
            $aspectData['skor_aspek'] = round($totalItemScore, 2);
            $totalSkorAspek += $totalItemScore;

            $variabelData['aspects'][] = $aspectData;
        }

        // variabelScore = jumlah skor aspek / jumlah aspek
        // (sudah tersimpan di assessment, tapi kita hitung juga sebagai pembanding)
        // Ambil dari assessment scores jika ada, fallback ke perhitungan
        $skorVariabelDB = $assessment->assessmentScores
            ->where('variabel_id', $variabel->id)
            ->whereNull('aspect_id')
            ->first()?->skor;

        $variabelData['skor_variabel'] = $skorVariabelDB
            ?? ($jumlahAspek > 0 ? round($totalSkorAspek / $jumlahAspek, 2) : 0);

        $observationData[] = $variabelData;
    }

    return $observationData;
}

    /**
     * Calculate monthly statistics
     */
    private function calculateMonthlyStatistics($assessments)
    {
        return [
            'total' => $assessments->count(),
            'avg_kepribadian' => round($assessments->avg('skor_kepribadian'), 2),
            'avg_kemandirian' => round($assessments->avg('skor_kemandirian'), 2),
            'avg_sikap' => round($assessments->avg('skor_sikap'), 2),
            'avg_mental' => round($assessments->avg('skor_mental'), 2),
            'avg_total' => round($assessments->avg('skor_total'), 2),
            'highest_score' => $assessments->max('skor_total'),
            'lowest_score' => $assessments->min('skor_total'),
        ];
    }

    /**
     * Calculate progress data for inmate
     */
    private function calculateProgressData($assessments)
    {
        $labels = [];
        $kepribadian = [];
        $kemandirian = [];
        $sikap = [];
        $mental = [];
        $total = [];

        foreach ($assessments as $assessment) {
            $labels[] = $assessment->tanggal_penilaian->format('M Y');
            $kepribadian[] = (float) $assessment->skor_kepribadian;
            $kemandirian[] = (float) $assessment->skor_kemandirian;
            $sikap[] = (float) $assessment->skor_sikap;
            $mental[] = (float) $assessment->skor_mental;
            $total[] = (float) $assessment->skor_total;
        }

        return [
            'labels' => $labels,
            'kepribadian' => $kepribadian,
            'kemandirian' => $kemandirian,
            'sikap' => $sikap,
            'mental' => $mental,
            'total' => $total,
            'trend' => $this->calculateTrend($total),
        ];
    }

    /**
     * Calculate trend (naik, turun, stabil)
     */
    private function calculateTrend($data)
    {
        if (count($data) < 2) {
            return 'stabil';
        }

        $first = $data[0];
        $last = end($data);
        $diff = $last - $first;

        if ($diff > 5) {
            return 'naik';
        } elseif ($diff < -5) {
            return 'turun';
        }

        return 'stabil';
    }

    private function getInstitutionData()
    {
        return [
            'name' => config('institution.name', 'Lembaga Pemasyarakatan'),
            'address' => config('institution.address', ''),
            'phone' => config('institution.phone', ''),
            'email' => config('institution.email', ''),
            'officers' => [
                'officer1' => [
                    'name' => config('institution.officers.officer1.name', ''),
                    'nip' => config('institution.officers.officer1.nip', ''),
                    'position' => config('institution.officers.officer1.position', ''),
                    'signature' => $this->getSignaturePath('officer1'),
                ],
                'officer2' => [
                    'name' => config('institution.officers.officer2.name', ''),
                    'nip' => config('institution.officers.officer2.nip', ''),
                    'position' => config('institution.officers.officer2.position', ''),
                    'signature' => $this->getSignaturePath('officer2'),
                ],
            ],
        ];
    }

    /**
     * Get signature file path for PDF
     *
     * @param string $officer
     * @return string|null
     */
    private function getSignaturePath($officer)
    {
        $signaturePath = config("institution.officers.{$officer}.signature");

        if ($signaturePath) {
            // Return absolute path untuk DomPDF
            return storage_path('app/public/' . $signaturePath);
        }

        return null;
    }
}
