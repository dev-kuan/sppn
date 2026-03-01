@extends('layouts.app')

@section('title', 'Pilih Narapidana')
@section('page-title', 'Pilih Narapidana untuk Penilaian')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Conditional Items Modal -->
    @if(isset($showConditionalModal) && $showConditionalModal)
        <x-conditional-items-modal
            :show="true"
            :conditionalItems="$conditionalItems ?? []"
            :tanggalPenilaian="$tanggalPenilaian ?? now()"
        />
    @endif

    <!-- Search Box -->
    <div class="mb-6">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text"
                   x-data
                   x-on:input.debounce.300ms="
                       const searchValue = $event.target.value.toLowerCase();
                       document.querySelectorAll('.inmate-card').forEach(card => {
                           const name = card.dataset.name.toLowerCase();
                           const reg = card.dataset.reg.toLowerCase();
                           card.style.display = (name.includes(searchValue) || reg.includes(searchValue)) ? 'block' : 'none';
                       });
                   "
                   class="block w-full py-3 pl-10 pr-3 leading-5 placeholder-gray-500 bg-white border border-gray-300 rounded-md focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                   placeholder="Cari narapidana berdasarkan nama atau nomor registrasi...">
        </div>
    </div>

    <!-- Info Alert -->
    <x-alert type="info" class="mb-6" :dismissible="false">
        <div class="font-medium">Pilih Narapidana</div>
        <div class="mt-1 text-sm">
            Klik pada card narapidana untuk membuat penilaian baru. Sistem akan memeriksa apakah sudah ada penilaian untuk bulan ini.
        </div>
    </x-alert>

    <!-- Inmates Grid -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($inmates as $inmate)
        <div class="overflow-hidden transition-shadow bg-white rounded-lg shadow cursor-pointer inmate-card hover:shadow-lg"
             data-name="{{ $inmate->nama }}"
             data-reg="{{ $inmate->no_registrasi }}"
             onclick="window.location='{{ route('assessments.create', ['inmate_id' => $inmate->id]) }}'">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 text-lg font-bold text-white bg-indigo-600 rounded-full">
                            {{ strtoupper(substr($inmate->nama, 0, 1)) }}
                        </div>
                    </div>
                    <div class="flex-1 w-0 ml-5">
                        <dl>
                            <dt class="text-sm font-medium text-gray-900 truncate">{{ $inmate->nama }}</dt>
                            <dd class="text-xs text-gray-500">{{ $inmate->no_registrasi }}</dd>
                        </dl>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="space-y-1 text-xs text-gray-500">
                        <div class="flex justify-between">
                            <span>Jenis Pidana:</span>
                            <span class="font-medium text-gray-900">{{ Str::limit($inmate->crimeType->nama, 20) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Umur:</span>
                            <span class="font-medium text-gray-900">{{ $inmate->umur }} tahun</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Sisa Pidana:</span>
                            <span class="font-medium text-gray-900">{{ $inmate->sisa_pidana_bulan }} bulan</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Assessment Badge -->
                @php
                    $latestAssessment = $inmate->assessments()->latest('tanggal_penilaian')->first();
                @endphp
                @if($latestAssessment)
                <div class="pt-3 mt-3 border-t border-gray-200">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">Terakhir dinilai:</span>
                        <span class="font-medium text-gray-900">{{ $latestAssessment->tanggal_penilaian->format('M Y') }}</span>
                    </div>
                </div>
                @endif
            </div>

            <div class="px-5 py-3 bg-gray-50">
                <div class="text-sm">
                    <span class="font-medium text-indigo-600 hover:text-indigo-500">
                        Buat Penilaian →
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 py-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada narapidana aktif</h3>
            <p class="mt-1 text-sm text-gray-500">Tidak ada narapidana yang dapat dinilai saat ini</p>
        </div>
        @endforelse
    </div>

    <!-- Stats Summary -->
    <div class="p-6 mt-8 bg-white rounded-lg shadow">
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-2xl font-semibold text-gray-900">{{ $inmates->count() }}</p>
                <p class="text-sm text-gray-500">Total Narapidana</p>
            </div>
            <div>
                <p class="text-2xl font-semibold text-green-600">
                    {{ $inmates->filter(fn($i) => $i->assessments()->whereMonth('tanggal_penilaian', now()->month)->exists())->count() }}
                </p>
                <p class="text-sm text-gray-500">Sudah Dinilai Bulan Ini</p>
            </div>
            <div>
                <p class="text-2xl font-semibold text-yellow-600">
                    {{ $inmates->filter(fn($i) => !$i->assessments()->whereMonth('tanggal_penilaian', now()->month)->exists())->count() }}
                </p>
                <p class="text-sm text-gray-500">Belum Dinilai</p>
            </div>
        </div>
    </div>
</div>
@endsection
