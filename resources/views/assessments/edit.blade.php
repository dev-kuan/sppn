@extends('layouts.app')

@section('title', 'Edit Penilaian')
@section('page-title', 'Edit Penilaian Narapidana')

@section('content')
<div x-data="assessmentForm()" x-init="init()">
    <!-- Header Info -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $assessment->inmate->nama }}</h2>
                    <p class="text-sm text-gray-500">
                        No. Registrasi: {{ $assessment->inmate->no_registrasi }} |
                        Periode: {{ $assessment->tanggal_penilaian->format('F Y') }}
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Auto-save Indicator -->
                    <div x-show="saving" class="flex items-center text-sm text-blue-600">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </div>
                    <div x-show="saved && !saving" class="flex items-center text-sm text-green-600">
                        <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                        Tersimpan
                    </div>

                    <!-- Submit Button -->
                    <button @click="submitAssessment()"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        Submit untuk Approval
                    </button>
                </div>
            </div>
        </div>

        <!-- Score Summary -->
        <div class="px-6 py-4 bg-gray-50">
            <div class="grid grid-cols-5 gap-4">
                <div class="text-center">
                    <div class="text-sm font-medium text-gray-500">Kepribadian</div>
                    <div class="mt-1 text-2xl font-semibold text-indigo-600" x-text="scores.kepribadian.toFixed(2)">0.00</div>
                </div>
                <div class="text-center">
                    <div class="text-sm font-medium text-gray-500">Kemandirian</div>
                    <div class="mt-1 text-2xl font-semibold text-green-600" x-text="scores.kemandirian.toFixed(2)">0.00</div>
                </div>
                <div class="text-center">
                    <div class="text-sm font-medium text-gray-500">Sikap</div>
                    <div class="mt-1 text-2xl font-semibold text-blue-600" x-text="scores.sikap.toFixed(2)">0.00</div>
                </div>
                <div class="text-center">
                    <div class="text-sm font-medium text-gray-500">Mental</div>
                    <div class="mt-1 text-2xl font-semibold text-purple-600" x-text="scores.mental.toFixed(2)">0.00</div>
                </div>
                <div class="text-center">
                    <div class="text-sm font-medium text-gray-500">Total</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900" x-text="scores.total.toFixed(2)">0.00</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Days Header (Sticky) -->
    <div class="sticky top-16 z-30 bg-white border-b border-gray-200 shadow-sm mb-4">
        <div class="flex overflow-x-auto">
            <div class="flex-shrink-0 w-64 px-4 py-3 font-medium text-gray-700 border-r border-gray-200">
                Item Observasi
            </div>
            <div class="flex">
                @for($day = 1; $day <= $daysInMonth; $day++)
                <div class="flex-shrink-0 w-10 px-2 py-3 text-center text-xs font-medium text-gray-700 border-r border-gray-200">
                    {{ $day }}
                </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Observation Items by Variabel & Aspek -->
    @foreach($variabels as $variabel)
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 bg-indigo-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-indigo-900">{{ $variabel->nama }}</h3>
        </div>

        @foreach($variabel->aspect as $aspek)
        <div class="border-b border-gray-200 last:border-b-0">
            <!-- Aspek Header (Accordion) -->
            <div x-data="{ open: true }" class="border-b border-gray-100">
                <button @click="open = !open"
                        class="w-full px-6 py-3 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
                    <span class="font-medium text-gray-700">{{ $aspek->nama }}</span>
                    <svg class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>

                <!-- Items in Aspek -->
                <div x-show="open" x-collapse>
                    @foreach($aspek->observationItems as $item)
                    <div class="flex hover:bg-gray-50">
                        <!-- Item Name -->
                        <div class="flex-shrink-0 w-64 px-4 py-3 text-sm text-gray-700 border-r border-gray-200">
                            <div class="font-medium">{{ $item->nama_item }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                Bobot: {{ $item->bobot }} |
                                Frekuensi: {{ $item->calculateFrequency($daysInMonth) }}
                            </div>
                        </div>

                        <!-- Days Checkboxes -->
                        <div class="flex overflow-x-auto">
                            @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $observation = $observationData[$item->id][$day] ?? null;
                                $isChecked = $observation ? $observation->is_checked : false;
                            @endphp
                            <div class="flex-shrink-0 w-10 px-2 py-3 flex items-center justify-center border-r border-gray-200">
                                <input type="checkbox"
                                       :disabled="saving"
                                       @change="saveObservation({{ $item->id }}, {{ $day }}, $event.target.checked)"
                                       {{ $isChecked ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                            </div>
                            @endfor
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        </div>
    @endforeach
</div>

@endsection

@push('scripts')
<script>
function assessmentForm() {
    return {
        saving: false,
        saved: false,
        scores: {
            kepribadian: {{ $assessment->skor_kepribadian ?? 0 }},
            kemandirian: {{ $assessment->skor_kemandirian ?? 0 }},
            sikap: {{ $assessment->skor_sikap ?? 0 }},
            mental: {{ $assessment->skor_mental ?? 0 }},
            total: {{ $assessment->skor_total ?? 0 }}
        },
        saveTimeout: null,

        init() {
            console.log('Assessment form initialized');
        },

        async saveObservation(itemId, day, isChecked) {
            this.saving = true;
            this.saved = false;

            // Clear previous timeout
            if (this.saveTimeout) {
                clearTimeout(this.saveTimeout);
            }

            try {
                const response = await fetch('{{ route("assessments.update-observation", $assessment) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        observation_item_id: itemId,
                        hari: day,
                        is_checked: isChecked,
                        catatan: null
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Update scores
                    this.scores = data.scores;
                    this.saving = false;
                    this.saved = true;

                    // Hide saved indicator after 2 seconds
                    this.saveTimeout = setTimeout(() => {
                        this.saved = false;
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Failed to save');
                }
            } catch (error) {
                console.error('Error saving observation:', error);
                this.saving = false;
                alert('Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
            }
        },

        async submitAssessment() {
            if (!confirm('Apakah Anda yakin ingin submit penilaian ini untuk approval?')) {
                return;
            }

            try {
                const response = await fetch('{{ route("assessments.submit", $assessment) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    window.location.href = '{{ route("assessments.show", $assessment) }}';
                } else {
                    throw new Error('Failed to submit');
                }
            } catch (error) {
                console.error('Error submitting assessment:', error);
                alert('Terjadi kesalahan saat submit penilaian. Silakan coba lagi.');
            }
        }
    }
}
</script>
@endpush
