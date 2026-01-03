<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Inmate;
use App\Models\AssessmentVariabel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssessmentExport;

class ReportController extends Controller
{
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
        $variabels = AssessmentVariabel::with(['aspects.observationItems' => function ($q) {
            $q->aktif()->ordered();
        }])->get();

        $observationData = $this->getObservationData($assessment, $variabels);

        $pdf = Pdf::loadView('reports.assessment-pdf', [
            'assessment' => $assessment,
            'variabels' => $variabels,
            'observationData' => $observationData,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = 'Laporan_Penilaian_' . $assessment->inmate->nama . '_' .
                    $assessment->tanggal_penilaian->format('Y-m') . '.pdf';

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

        $pdf = Pdf::loadView('reports.monthly-pdf', [
            'assessments' => $assessments,
            'statistics' => $statistics,
            'month' => $validated['month'],
            'year' => $validated['year'],
        ]);

        $pdf->setPaper('a4', 'landscape');

        $monthName = Carbon::createFromDate($validated['year'], $validated['month'])->format('F_Y');
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

        return Excel::download(new \App\Exports\InmateExport($inmates), $filename);
    }

    /**
     * Get observation data for report
     */
    private function getObservationData($assessment, $variabels)
    {
        $observationData = [];
        $daysInMonth = $assessment->tanggal_penilaian->daysInMonth;

        foreach ($variabels as $variabel) {
            foreach ($variabel->aspects as $aspek) {
                foreach ($aspek->observationItems as $item) {
                    $observations = $assessment->dailyObservations()
                        ->where('observation_item_id', $item->id)
                        ->get()
                        ->keyBy('hari');

                    $checkedCount = $observations->where('is_checked', true)->count();
                    $frequency = $item->calculateFrequency($daysInMonth);
                    $percentage = $frequency > 0 ? round(($checkedCount / $frequency) * 100, 2) : 0;

                    $observationData[$item->id] = [
                        'observations' => $observations,
                        'checked_count' => $checkedCount,
                        'frequency' => $frequency,
                        'percentage' => $percentage,
                    ];
                }
            }
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
}
