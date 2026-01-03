@extends('layouts.app')

@section('title', 'Buat Penilaian Baru')
@section('page-title', 'Buat Penilaian Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Form Penilaian Narapidana</h3>
            <p class="mt-1 text-sm text-gray-500">Pilih narapidana dan periode penilaian</p>
        </div>

        <form action="{{ route('assessments.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Select Inmate -->
            <div>
                <label for="inmate_id" class="block text-sm font-medium text-gray-700">
                    Narapidana <span class="text-red-500">*</span>
                </label>
                <select name="inmate_id"
                        id="inmate_id"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('inmate_id') border-red-500 @enderror">
                    <option value="">Pilih Narapidana</option>
                    @if($inmate)
                        <option value="{{ $inmate->id }}" selected>{{ $inmate->nama }} ({{ $inmate->no_registrasi }})</option>
                    @else
                        @foreach(\App\Models\Inmate::aktif()->orderBy('nama')->get() as $inm)
                        <option value="{{ $inm->id }}" {{ old('inmate_id') == $inm->id ? 'selected' : '' }}>
                            {{ $inm->nama }} ({{ $inm->no_registrasi }})
                        </option>
                        @endforeach
                    @endif
                </select>
                @error('inmate_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Pilih narapidana yang akan dinilai</p>
            </div>

            <!-- Assessment Period -->
            <div>
                <label for="tanggal_penilaian" class="block text-sm font-medium text-gray-700">
                    Periode Penilaian <span class="text-red-500">*</span>
                </label>
                <input type="month"
                       name="tanggal_penilaian"
                       id="tanggal_penilaian"
                       value="{{ old('tanggal_penilaian', now()->format('Y-m')) }}"
                       required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('tanggal_penilaian') border-red-500 @enderror">
                @error('tanggal_penilaian')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Pilih bulan dan tahun periode penilaian</p>
            </div>

            <!-- Info Box -->
            <div class="rounded-md bg-blue-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-blue-800">Informasi Penting</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Sistem akan membuat form penilaian untuk seluruh hari dalam bulan tersebut</li>
                                <li>Pastikan narapidana dan periode yang dipilih sudah benar</li>
                                <li>Satu narapidana hanya bisa memiliki satu penilaian per bulan</li>
                                <li>Data penilaian akan tersimpan otomatis saat Anda mengisi</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('assessments.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Penilaian
                </button>
            </div>
        </form>
    </div>

    <!-- Recent Assessments -->
    @if($inmate && $inmate->assessments->count() > 0)
    <div class="mt-6 bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Riwayat Penilaian</h3>
        </div>
        <div class="px-6 py-4">
            <div class="space-y-3">
                @foreach($inmate->assessments()->latest('tanggal_penilaian')->limit(5)->get() as $assessment)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $assessment->tanggal_penilaian->format('F Y') }}</p>
                        <p class="text-xs text-gray-500">Skor: {{ number_format($assessment->skor_total, 2) }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <x-status-badge :status="$assessment->status" />
                        <a href="{{ route('assessments.show', $assessment) }}"
                           class="text-indigo-600 hover:text-indigo-900 text-sm">
                            Lihat
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-format date input to first day of selected month
    const dateInput = document.getElementById('tanggal_penilaian');

    dateInput.addEventListener('change', function() {
        const value = this.value; // Format: YYYY-MM
        if (value) {
            // Will be sent as YYYY-MM-01 to backend
            this.setAttribute('data-formatted', value + '-01');
        }
    });

    // Override form submission to ensure correct format
    const form = dateInput.closest('form');
    form.addEventListener('submit', function(e) {
        const monthValue = dateInput.value;
        if (monthValue) {
            // Create hidden input with full date
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'tanggal_penilaian';
            hiddenInput.value = monthValue + '-01';

            // Remove name from month input to avoid conflict
            dateInput.removeAttribute('name');

            // Add hidden input to form
            form.appendChild(hiddenInput);
        }
    });
});
</script>
@endpush
