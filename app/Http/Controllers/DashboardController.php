<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Inmate;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Common data for all users
        $data = [
            'totalInmates' => Inmate::aktif()->count(),
            'inmatesReleased' => Inmate::dirilis()->count(),
            'inmatesTransferred' => Inmate::dipindahkan()->count(),
        ];

        // Role-specific dashboard
        if ($user->isAdmin() || $user->isKepalaLapas()) {
            return $this->adminDashboard($data);
        } elseif ($user->isWaliPemasyarakatan()) {
            return $this->waliDashboard($data);
        } elseif ($user->isPetugasInput()) {
            return $this->petugasDashboard($data);
        }

        return view('dashboard.index', $data);
    }

    private function adminDashboard($data)
    {
        // Assessment statistics
        $data['totalAssessments'] = Assessment::count();
        $data['pendingAssessments'] = Assessment::disubmit()->count();
        $data['approvedAssessments'] = Assessment::diterima()->count();
        $data['rejectedAssessments'] = Assessment::ditolak()->count();

        // Monthly assessment trend
        $data['monthlyTrend'] = Assessment::select(
            DB::raw('YEAR(tanggal_penilaian) as year'),
            DB::raw('MONTH(tanggal_penilaian) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('tanggal_penilaian', '>=', Carbon::now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => Carbon::createFromDate($item->year, $item->month)->format('M Y'),
                    'total' => $item->total,
                ];
            });

        // Average scores by category
        $data['averageScores'] = Assessment::diterima()
            ->select(
                DB::raw('AVG(skor_kepribadian) as avg_kepribadian'),
                DB::raw('AVG(skor_kemandirian) as avg_kemandirian'),
                DB::raw('AVG(skor_sikap) as avg_sikap'),
                DB::raw('AVG(skor_mental) as avg_mental'),
                DB::raw('AVG(skor_total) as avg_total')
            )
            ->first();

        // Top performers
        $data['topPerformers'] = Assessment::with('inmate')
            ->diterima()
            ->orderBy('skor_total', 'desc')
            ->limit(5)
            ->get();

        // Inmates needing attention
        $data['needsAttention'] = Assessment::with('inmate')
            ->diterima()
            ->orderBy('skor_total', 'asc')
            ->limit(5)
            ->get();

        // Crime type distribution
        $data['crimeDistribution'] = Inmate::aktif()
            ->select('crime_type_id', DB::raw('COUNT(*) as total'))
            ->with('crimeType')
            ->groupBy('crime_type_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Recent activities
        $data['recentActivities'] = Activity::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.admin', $data);
    }

    private function waliDashboard($data)
    {
        // Assessments to approve
        $data['pendingApproval'] = Assessment::disubmit()->count();

        // Current month statistics
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $data['monthlyStats'] = [
            'total' => Assessment::byMonth($currentMonth, $currentYear)->count(),
            'completed' => Assessment::byMonth($currentMonth, $currentYear)->diterima()->count(),
            'pending' => Assessment::byMonth($currentMonth, $currentYear)->disubmit()->count(),
            'draft' => Assessment::byMonth($currentMonth, $currentYear)->draf()->count(),
        ];

        // Inmates requiring assessment
        $existingAssessmentInmates = Assessment::byMonth($currentMonth, $currentYear)
            ->pluck('inmate_id')
            ->toArray();

        $data['inmatesNeedingAssessment'] = Inmate::aktif()
            ->whereNotIn('id', $existingAssessmentInmates)
            ->count();

        // Recent assessments
        $data['recentAssessments'] = Assessment::with('inmate')
            ->latest('created_at')
            ->limit(10)
            ->get();

        // Average scores this month
        $data['monthlyAverages'] = Assessment::byMonth($currentMonth, $currentYear)
            ->diterima()
            ->select(
                DB::raw('AVG(skor_kepribadian) as avg_kepribadian'),
                DB::raw('AVG(skor_kemandirian) as avg_kemandirian'),
                DB::raw('AVG(skor_sikap) as avg_sikap'),
                DB::raw('AVG(skor_mental) as avg_mental')
            )
            ->first();

        return view('dashboard.wali', $data);
    }

    private function petugasDashboard($data)
    {
        $userId = auth()->id();

        // My assessments
        $data['myAssessments'] = [
            'total' => Assessment::where('created_by', $userId)->count(),
            'draft' => Assessment::where('created_by', $userId)->draf()->count(),
            'submitted' => Assessment::where('created_by', $userId)->disubmit()->count(),
            'approved' => Assessment::where('created_by', $userId)->diterima()->count(),
            'rejected' => Assessment::where('created_by', $userId)->ditolak()->count(),
        ];

        // Assessments to complete (draft)
        $data['drafts'] = Assessment::where('created_by', $userId)
            ->draf()
            ->with('inmate')
            ->latest('updated_at')
            ->limit(5)
            ->get();

        // Current month progress
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $data['monthlyProgress'] = Assessment::where('created_by', $userId)
            ->byMonth($currentMonth, $currentYear)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "draf" THEN 1 ELSE 0 END) as draft,
                SUM(CASE WHEN status = "disubmit" THEN 1 ELSE 0 END) as submitted,
                SUM(CASE WHEN status = "diterima" THEN 1 ELSE 0 END) as approved
            ')
            ->first();

        // Recent activity
        $data['myRecentAssessments'] = Assessment::where('created_by', $userId)
            ->with('inmate')
            ->latest('updated_at')
            ->limit(10)
            ->get();

        // Reminders: assessments
        $data['reminders'] = Assessment::where('created_by', $userId)
            ->where('status', 'draf')
            ->where('updated_at', '<', Carbon::now()->subDays(3))
            ->count();

        return view('dashboard.petugas', $data);
    }

    public function getChartData(Request $request)
    {
        $type = $request->get('type', 'monthly_trend');

        switch ($type) {
            case 'monthly_trend':
                return $this->getMonthlyTrendData();

            case 'score_comparison':
                return $this->getScoreComparisonData();

            case 'crime_distribution':
                return $this->getCrimeDistributionData();

            default:
                return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }

    private function getMonthlyTrendData()
    {
        $data = Assessment::select(
            DB::raw('YEAR(tanggal_penilaian) as year'),
            DB::raw('MONTH(tanggal_penilaian) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('tanggal_penilaian', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        return response()->json([
            'labels' => $data->map(fn($item) => Carbon::createFromDate($item->year, $item->month)->format('M Y')),
            'data' => $data->pluck('total'),
        ]);
    }

    private function getScoreComparisonData()
    {
        $data = Assessment::diterima()
            ->select(
                DB::raw('AVG(skor_kepribadian) as avg_kepribadian'),
                DB::raw('AVG(skor_kemandirian) as avg_kemandirian'),
                DB::raw('AVG(skor_sikap) as avg_sikap'),
                DB::raw('AVG(skor_mental) as avg_mental')
            )
            ->first();

        return response()->json([
            'labels' => ['Kepribadian', 'Kemandirian', 'Sikap', 'Mental'],
            'data' => [
                round($data->avg_kepribadian, 2),
                round($data->avg_kemandirian, 2),
                round($data->avg_sikap, 2),
                round($data->avg_mental, 2),
            ],
        ]);
    }

    private function getCrimeDistributionData()
    {
        $data = Inmate::aktif()
            ->select('crime_type_id', DB::raw('COUNT(*) as total'))
            ->with('crimeType')
            ->groupBy('crime_type_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'labels' => $data->map(fn($item) => $item->crimeType->nama),
            'data' => $data->pluck('total'),
        ]);
    }
}
