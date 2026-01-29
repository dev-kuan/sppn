@extends('layouts.app')

@section('title', 'Dashboard Wali')
@section('page-title', 'Dashboard - Wali Pemasyarakatan')

@section('content')
<!-- Quick Stats -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- Total Inmates -->
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
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Narapidana Aktif</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ $totalInmates }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <a href="{{ route('inmates.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                Lihat semua →
            </a>
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
                        <dt class="text-sm font-medium text-gray-500 truncate">Menunggu Persetujuan</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ $pendingApproval }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <a href="{{ route('assessments.index', ['status' => 'disubmit']) }}" class="text-sm font-medium text-yellow-600 hover:text-yellow-500">
                Review sekarang →
            </a>
        </div>
    </div>

    <!-- Monthly Stats - Total -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-blue-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Penilaian Bulan Ini</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ $monthlyStats['total'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Inmates Needing Assessment -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-red-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Belum Dinilai</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ $inmatesNeedingAssessment }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <a href="{{ route('assessments.create') }}" class="text-sm font-medium text-red-600 hover:text-red-500">
                Buat penilaian →
            </a>
        </div>
    </div>
</div>

<!-- Monthly Progress Chart -->
<div class="bg-white shadow rounded-lg mb-8">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Progress Penilaian Bulan Ini</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-4">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Total</p>
                <p class="text-3xl font-bold text-gray-900">{{ $monthlyStats['total'] }}</p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-sm text-green-600">Selesai</p>
                <p class="text-3xl font-bold text-green-600">{{ $monthlyStats['completed'] }}</p>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <p class="text-sm text-yellow-600">Pending</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $monthlyStats['pending'] }}</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Draft</p>
                <p class="text-3xl font-bold text-gray-600">{{ $monthlyStats['draft'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Average Scores This Month -->
@if($monthlyAverages)
<div class="bg-white shadow rounded-lg mb-8">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Rata-rata Skor Bulan Ini</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="text-center p-4 bg-indigo-50 rounded-lg">
                <p class="text-xs text-indigo-600 font-medium">Kepribadian</p>
                <p class="text-2xl font-bold text-indigo-900">{{ number_format($monthlyAverages->avg_kepribadian ?? 0, 1) }}</p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-xs text-green-600 font-medium">Kemandirian</p>
                <p class="text-2xl font-bold text-green-900">{{ number_format($monthlyAverages->avg_kemandirian ?? 0, 1) }}</p>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-xs text-blue-600 font-medium">Sikap</p>
                <p class="text-2xl font-bold text-blue-900">{{ number_format($monthlyAverages->avg_sikap ?? 0, 1) }}</p>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <p class="text-xs text-purple-600 font-medium">Mental</p>
                <p class="text-2xl font-bold text-purple-900">{{ number_format($monthlyAverages->avg_mental ?? 0, 1) }}</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Recent Assessments -->
<div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Penilaian Terbaru</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Narapidana</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Skor Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentAssessments as $assessment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $assessment->inmate->nama }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $assessment->tanggal_penilaian->format('F Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                        {{ number_format($assessment->skor_total ?? 0, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-status-badge :status="$assessment->status" />
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $assessment->created_at->diffForHumans() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('assessments.show', $assessment) }}" class="text-indigo-600 hover:text-indigo-900">
                            Lihat
                        </a>
                        @if($assessment->status === 'disubmit')
                        <span class="mx-2">|</span>
                        <a href="{{ route('assessments.show', $assessment) }}" class="text-green-600 hover:text-green-900">
                            Review
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                        Belum ada penilaian terbaru
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
