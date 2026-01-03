@extends('layouts.app')

@section('title', 'Detail Penilaian')
@section('page-title', 'Detail Penilaian')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('assessments.index') }}"
           class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Daftar
        </a>

        <div class="flex items-center space-x-3">
            @if($assessment->status === 'draf' || $assessment->status === 'ditolak')
                @can('edit-penilaian')
                <a href="{{ route('assessments.edit', $assessment) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Penilaian
                </a>
                @endcan
            @endif

            @if($assessment->status === 'disubmit')
                @can('approve-penilaian')
                <form action="{{ route('assessments.approve', $assessment) }}"
                      method="POST"
                      class="inline"
                      onsubmit="return confirm('Apakah Anda yakin ingin menyetujui penilaian ini?')">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Setujui
                    </button>
                </form>

                <button onclick="openRejectModal()"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Tolak
                </button>
                @endcan
            @endif

            @if($assessment->status === 'diterima')
            <button onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print PDF
            </button>
            @endif
        </div>
    </div>

    <!-- Info Card -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $assessment->inmate->nama }}</h2>
                    <p class="text-sm text-gray-500">
                        No. Registrasi: {{ $assessment->inmate->no_registrasi }} |
                        Periode: {{ $assessment->tanggal_penilaian->format('F Y') }}
                    </p>
                </div>
                <x-status-badge :status="$assessment->status" class="text-sm" />
            </div>
        </div>

        <!-- Score Summary -->
        <div class="px-6 py-6 bg-gray-50">
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-5">
                <div class="text-center p-4 bg-indigo-50 rounded-lg">
                    <p class="text-xs text-indigo-600 font-medium">Kepribadian</p>
                    <p class="text-2xl font-bold text-indigo-900">{{ number_format($assessment->skor_kepribadian, 2) }}</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-xs text-green-600 font-medium">Kemandirian</p>
                    <p class="text-2xl font-bold text-green-900">{{ number_format($assessment->skor_kemandirian, 2) }}</p>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-xs text-blue-600 font-medium">Sikap</p>
                    <p class="text-2xl font-bold text-blue-900">{{ number_format($assessment->skor_sikap, 2) }}</p>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <p class="text-xs text-purple-600 font-medium">Mental</p>
                    <p class="text-2xl font-bold text-purple-900">{{ number_format($assessment->skor_mental, 2) }}</p>
                </div>
                <div class="text-center p-4 bg-gray-100 rounded-lg">
                    <p class="text-xs text-gray-600 font-medium">Total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($assessment->skor_total, 2) }}</p>
                </div>
            </div>

            <div class="mt-4 text-center">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                    @if($assessment->skor_total >= 81) bg-green-100 text-green-800
                    @elseif($assessment->skor_total >= 61) bg-blue-100 text-blue-800
                    @elseif($assessment->skor_total >= 41) bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800
                    @endif">
                    Kategori: {{ $assessment->kategori_total }}
                </span>
            </div>
        </div>

        <!-- Metadata -->
        <div class="px-6 py-4 border-t border-gray-200">
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Dibuat Oleh</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $assessment->creator->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tanggal Dibuat</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $assessment->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                @if($assessment->approved_by)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Disetujui Oleh</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $assessment->approver->name }}</dd>
                </div>
                @endif
            </dl>

            @if($assessment->catatan)
            <div class="mt-4">
                <dt class="text-sm font-medium text-gray-500">Catatan</dt>
                <dd class="mt-1 text-sm text-gray-900 bg-yellow-50 p-3 rounded-md">{{ $assessment->catatan }}</dd>
            </div>
            @endif
        </div>
    </div>

    <!-- Observation Details -->
    @foreach($variabels as $variabel)
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 bg-indigo-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-indigo-900">{{ $variabel->nama }}</h3>
        </div>

        @foreach($variabel->aspects as $aspek)
        <div class="border-b border-gray-200 last:border-b-0">
            <div x-data="{ open: false }" class="border-b border-gray-100">
                <button @click="open = !open"
                        class="w-full px-6 py-3 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
                    <span class="font-medium text-gray-700">{{ $aspek->nama }}</span>
                    <svg class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="open" x-collapse class="px-6 py-4">
                    @foreach($aspek->observationItems as $item)
                    <div class="mb-4 last:mb-0">
                        <div class="font-medium text-sm text-gray-900 mb-2">{{ $item->nama_item }}</div>
                        <div class="flex flex-wrap gap-1">
                            @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $observation = $observationData[$item->id][$day] ?? null;
                                $isChecked = $observation ? $observation->is_checked : false;
                            @endphp
                            <div class="w-8 h-8 flex items-center justify-center rounded text-xs font-medium
                                {{ $isChecked ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-400' }}">
                                {{ $day }}
                            </div>
                            @endfor
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            Terpenuhi: {{ $observationData[$item->id]->where('is_checked', true)->count() }} /
                            {{ $item->calculateFrequency($daysInMonth) }} hari
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach

    <!-- Commitment Statements -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Pernyataan Komitmen</h3>
        </div>
        <div class="px-6 py-4">
            <div class="space-y-4">
                @foreach($assessment->commitmentStatements as $statement)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">
                            Komitmen {{ $statement->jenis === 'nkri' ? 'Setia NKRI' : 'Anti Narkoba' }}
                        </p>
                        @if($statement->is_signed)
                        <p class="text-sm text-gray-500">Ditandatangani: {{ $statement->signed_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                    <div>
                        @if($statement->is_signed)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                            Ditandatangani
                        </span>
                        @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Belum Ditandatangani
                        </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<x-modal name="reject-modal" maxWidth="md">
    <form action="{{ route('assessments.reject', $assessment) }}" method="POST" class="p-6">
        @csrf
        <h3 class="text-lg font-medium text-gray-900 mb-4">Tolak Penilaian</h3>

        <div class="mb-4">
            <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                Alasan Penolakan <span class="text-red-500">*</span>
            </label>
            <textarea name="catatan"
                      id="catatan"
                      rows="4"
                      required
                      placeholder="Jelaskan alasan penolakan..."
                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <button type="button"
                    @click="$dispatch('close-modal', 'reject-modal')"
                    class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Batal
            </button>
            <button type="submit"
                    class="px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700">
                Tolak Penilaian
            </button>
        </div>
    </form>
</x-modal>
@endsection

@push('scripts')
<script>
function openRejectModal() {
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'reject-modal' }));
}
</script>
@endpush
