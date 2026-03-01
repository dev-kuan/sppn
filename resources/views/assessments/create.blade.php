@extends('layouts.app')

@section('title', 'Buat Penilaian Baru')
@section('page-title', 'Buat Penilaian Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Conditional Items Modal -->
    @if(isset($showConditionalModal) && $showConditionalModal)
        <x-conditional-items-modal
            :show="true"
            :conditionalItems="$conditionalItems ?? []"
            :tanggalPenilaian="$tanggalPenilaian ?? now()"
        />
    @endif

    <div class="bg-white rounded-lg shadow">
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
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('inmate_id') border-red-500 @enderror">
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
                    Tanggal Penilaian <span class="text-red-500">*</span>
                </label>
                <input type="date"
                       name="tanggal_penilaian"
                       id="tanggal_penilaian"
                       value="{{ old('tanggal_penilaian', $tanggalPenilaian ? $tanggalPenilaian->format('Y-m-d') : now()->format('Y-m-d')) }}"
                       required
                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('tanggal_penilaian') border-red-500 @enderror">
                @error('tanggal_penilaian')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Pilih waktu penilaian</p>
            </div>

            <!-- Info Box -->
            <div class="p-4 rounded-md bg-blue-50">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex-1 ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Informasi Penting</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="space-y-1 list-disc list-inside">
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
            <div class="flex items-center justify-end pt-6 space-x-3 border-t border-gray-200">
                <a href="{{ route('assessments.index') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Penilaian
                </button>
            </div>
        </form>
    </div>

    <!-- Recent Assessments -->
    @if(isset($inmate) && $inmate && $inmate->assessments->count() > 0)
    <div class="mt-6 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Riwayat Penilaian</h3>
        </div>
        <div class="px-6 py-4">
            <div class="space-y-3">
                @foreach($inmate->assessments()->latest('tanggal_penilaian')->limit(5)->get() as $assessment)
                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $assessment->tanggal_penilaian->format('F Y') }}</p>
                        <p class="text-xs text-gray-500">Skor: {{ number_format($assessment->skor_total, 2) }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <x-status-badge :status="$assessment->status" />
                        <a href="{{ route('assessments.show', $assessment) }}"
                           class="text-sm text-indigo-600 hover:text-indigo-900">
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
