@extends('layouts.app')

@section('title', 'Detail Narapidana')
@section('page-title', 'Detail Narapidana')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('inmates.index') }}"
           class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Daftar
        </a>

        <div class="flex items-center space-x-3">
            @can('create-penilaian')
            <a href="{{ route('assessments.create', ['inmate_id' => $inmate->id]) }}"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Buat Penilaian
            </a>
            @endcan

            @can('edit-narapidana')
            <a href="{{ route('inmates.edit', $inmate) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Data
            </a>
            @endcan
        </div>
    </div>

    <!-- Profile Card -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-8">
            <div class="flex items-center">
                <div class="h-24 w-24 rounded-full bg-white flex items-center justify-center text-4xl font-bold text-indigo-600">
                    {{ strtoupper(substr($inmate->nama, 0, 1)) }}
                </div>
                <div class="ml-6 text-white">
                    <h2 class="text-3xl font-bold">{{ $inmate->nama }}</h2>
                    <p class="text-indigo-100">No. Registrasi: {{ $inmate->no_registrasi }}</p>
                    <div class="mt-2">
                        <x-status-badge :status="$inmate->status" class="text-sm" />
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Data Personal -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-3">DATA PERSONAL</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-xs text-gray-500">Tempat, Tanggal Lahir</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $inmate->tempat_lahir }}, {{ $inmate->tanggal_lahir->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Umur</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $inmate->umur }} tahun</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Jenis Kelamin</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ ucfirst($inmate->jenis_kelamin) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Agama</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $inmate->agama }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Pendidikan</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $inmate->tingkat_pendidikan ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Pekerjaan Terakhir</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $inmate->pekerjaan_terakhir ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Data Pidana -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-3">DATA PIDANA</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-xs text-gray-500">Jenis Tindak Pidana</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $inmate->crimeType->nama }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Lama Pidana</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $inmate->lama_pidana_bulan }} bulan</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Sisa Pidana</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $inmate->sisa_pidana_bulan }} bulan</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Residivisme</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $inmate->jumlah_residivisme }}x</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Tanggal Masuk</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $inmate->tanggal_masuk->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Tanggal Bebas</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $inmate->tanggal_bebas ? $inmate->tanggal_bebas->format('d/m/Y') : '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Data Pembinaan -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-3">DATA PEMBINAAN</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-xs text-gray-500">Pelatihan</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $inmate->pelatihan ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Program Kerja</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $inmate->program_kerja ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Catatan Kesehatan</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $inmate->catatan_kesehatan ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Assessment -->
    @if($latestAssessment)
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Penilaian Terakhir</h3>
        </div>
        <div class="px-6 py-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm text-gray-500">Periode</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $latestAssessment->tanggal_penilaian->format('F Y') }}</p>
                </div>
                <a href="{{ route('assessments.show', $latestAssessment) }}"
                   class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                    Lihat Detail →
                </a>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-5">
                <div class="text-center p-4 bg-indigo-50 rounded-lg">
                    <p class="text-xs text-indigo-600 font-medium">Kepribadian</p>
                    <p class="text-2xl font-bold text-indigo-900">{{ number_format($latestAssessment->skor_kepribadian, 1) }}</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-xs text-green-600 font-medium">Kemandirian</p>
                    <p class="text-2xl font-bold text-green-900">{{ number_format($latestAssessment->skor_kemandirian, 1) }}</p>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-xs text-blue-600 font-medium">Sikap</p>
                    <p class="text-2xl font-bold text-blue-900">{{ number_format($latestAssessment->skor_sikap, 1) }}</p>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <p class="text-xs text-purple-600 font-medium">Mental</p>
                    <p class="text-2xl font-bold text-purple-900">{{ number_format($latestAssessment->skor_mental, 1) }}</p>
                </div>
                <div class="text-center p-4 bg-gray-100 rounded-lg">
                    <p class="text-xs text-gray-600 font-medium">Total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($latestAssessment->skor_total, 1) }}</p>
                </div>
            </div>

            <div class="mt-4 text-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($latestAssessment->skor_total >= 81) bg-green-100 text-green-800
                    @elseif($latestAssessment->skor_total >= 61) bg-blue-100 text-blue-800
                    @elseif($latestAssessment->skor_total >= 41) bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ $latestAssessment->kategori_total }}
                </span>
            </div>
        </div>
    </div>
    @endif

    <!-- Assessment History -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Riwayat Penilaian</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Skor Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($inmate->assessments as $assessment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $assessment->tanggal_penilaian->format('F Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ number_format($assessment->skor_total, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $assessment->kategori_total }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-status-badge :status="$assessment->status" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('assessments.show', $assessment) }}"
                               class="text-indigo-600 hover:text-indigo-900">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                            Belum ada riwayat penilaian
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
