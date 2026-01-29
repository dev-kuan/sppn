@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard - Admin')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- Total Narapidana -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-indigo-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Narapidana</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900">{{ $totalInmates }}</div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="{{ route('inmates.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Lihat semua
                </a>
            </div>
        </div>
    </div>

    <!-- Total Penilaian -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-green-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Penilaian</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900">{{ $totalAssessments }}</div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="{{ route('assessments.index') }}" class="font-medium text-green-600 hover:text-green-500">
                    Lihat semua
                </a>
            </div>
        </div>
    </div>

    <!-- Pending Approval -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-yellow-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Approval</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900">{{ $pendingAssessments }}</div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="{{ route('assessments.index', ['status' => 'disubmit']) }}" class="font-medium text-yellow-600 hover:text-yellow-500">
                    Review sekarang
                </a>
            </div>
        </div>
    </div>

    <!-- Approved Assessments -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-blue-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Penilaian Disetujui</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900">{{ $approvedAssessments }}</div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="{{ route('assessments.index', ['status' => 'diterima']) }}" class="font-medium text-blue-600 hover:text-blue-500">
                    Lihat detail
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 gap-5 lg:grid-cols-2 mb-8">
    <!-- Monthly Trend Chart -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Trend Penilaian Bulanan</h3>
            <div id="monthlyTrendChart"></div>
        </div>
    </div>

    <!-- Score Comparison Chart -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Rata-rata Skor Per Kategori</h3>
            <div id="scoreComparisonChart"></div>
        </div>
    </div>
</div>

<!-- Crime Distribution Chart -->
<div class="bg-white overflow-hidden shadow rounded-lg mb-8">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Distribusi Jenis Tindak Pidana</h3>
        <div id="crimeDistributionChart"></div>
    </div>
</div>

<!-- Tables Row -->
<div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
    <!-- Top Performers -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Top 5 Narapidana (Skor Tertinggi)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Skor Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topPerformers as $assessment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $assessment->inmate?->nama ?? 'Belum ada data' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($assessment->skor_total, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $assessment->kategori_total }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                            Belum ada data
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Needs Attention -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Perlu Perhatian (Skor Rendah)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Skor Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($needsAttention as $assessment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $assessment->inmate?->nama ?? 'Belum ada data' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($assessment->skor_total, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                {{ $assessment->kategori_total }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                            Belum ada data
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Trend Chart
    const monthlyTrendOptions = {
        series: [{
            name: 'Jumlah Penilaian',
            data: @json($monthlyTrend->pluck('total'))
        }],
        chart: {
            type: 'area',
            height: 300,
            toolbar: { show: false }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        xaxis: {
            categories: @json($monthlyTrend->pluck('label'))
        },
        colors: ['#4F46E5'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " penilaian"
                }
            }
        }
    };
    const monthlyTrendChart = new ApexCharts(document.querySelector("#monthlyTrendChart"), monthlyTrendOptions);
    monthlyTrendChart.render();

    // Score Comparison Chart
    const scoreComparisonOptions = {
        series: [{
            name: 'Rata-rata Skor',
            data: [
                {{ $averageScores->avg_kepribadian ?? 0 }},
                {{ $averageScores->avg_kemandirian ?? 0 }},
                {{ $averageScores->avg_sikap ?? 0 }},
                {{ $averageScores->avg_mental ?? 0 }}
            ]
        }],
        chart: {
            type: 'bar',
            height: 300,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false,
                columnWidth: '60%',
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(2)
            }
        },
        xaxis: {
            categories: ['Kepribadian', 'Kemandirian', 'Sikap', 'Mental']
        },
        colors: ['#10B981'],
        yaxis: {
            max: 100
        }
    };
    const scoreComparisonChart = new ApexCharts(document.querySelector("#scoreComparisonChart"), scoreComparisonOptions);
    scoreComparisonChart.render();

    // Crime Distribution Chart
    const crimeDistributionOptions = {
        series: @json($crimeDistribution->pluck('total')),
        chart: {
            type: 'donut',
            height: 350
        },
        labels: @json($crimeDistribution->map(fn($item) => $item->crimeType->nama)),
        colors: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#6366F1'],
        legend: {
            position: 'bottom'
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    height: 300
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };
    const crimeDistributionChart = new ApexCharts(document.querySelector("#crimeDistributionChart"), crimeDistributionOptions);
    crimeDistributionChart.render();
});
</script>
@endpush
