@extends('layouts.app')

@section('title', 'Edit Penilaian')
@section('page-title', 'Edit Penilaian Narapidana')

@section('content')
    <div x-data="assessmentForm()" x-init="init()">
        <!-- Import/Export Section -->
        <div class="mb-6 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Import / Export Data</h3>
                <p class="mt-1 text-sm text-gray-500">Download template Excel, isi data observasi, lalu upload kembali ke
                    sistem</p>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <!-- Download Template -->
                    <div
                        class="p-6 text-center transition-colors border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-500">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h4 class="mt-2 text-sm font-medium text-gray-900">Download Template Excel</h4>
                        <p class="mt-1 text-xs text-gray-500">Template sudah berisi data narapidana dan struktur observasi
                        </p>
                        <a href="{{ route('assessments.export-template', $assessment) }}"
                            class="inline-flex items-center px-4 py-2 mt-4 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download Template
                        </a>
                    </div>

                    <!-- Upload File -->
                    <div class="p-6 transition-colors border-2 border-gray-300 border-dashed rounded-lg hover:border-green-500"
                        x-data="{ uploading: false, fileName: '' }">
                        <form action="{{ route('assessments.import', $assessment) }}" method="POST"
                            enctype="multipart/form-data" @submit="uploading = true" class="text-center">
                            @csrf
                            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <h4 class="mt-2 text-sm font-medium text-gray-900">Upload File Excel</h4>
                            <p class="mt-1 text-xs text-gray-500">Pilih file template yang sudah diisi</p>

                            <div class="mt-4">
                                <input type="file" name="file" id="import-file" accept=".xlsx,.xls" required
                                    @change="fileName = $event.target.files[0]?.name || ''" class="hidden">
                                <label for="import-file"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50">
                                    <svg class="w-5 h-5 mr-2 -ml-1 text-gray-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    Pilih File
                                </label>
                            </div>

                            <div x-show="fileName" class="mt-2 text-sm text-gray-600">
                                <span class="font-medium">File:</span> <span x-text="fileName"></span>
                            </div>

                            <button type="submit" x-show="fileName" :disabled="uploading"
                                class="inline-flex items-center px-4 py-2 mt-3 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-5 h-5 mr-2 -ml-1" :class="{ 'animate-spin': uploading }" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                <span x-text="uploading ? 'Mengupload...' : 'Upload & Import'"></span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="p-4 mt-6 border-l-4 border-blue-400 bg-blue-50">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <span class="font-medium">Petunjuk:</span>
                            </p>
                            <ul class="mt-2 space-y-1 text-sm text-blue-700 list-disc list-inside">
                                <li>Download template Excel terlebih dahulu</li>
                                <li>Isi kolom hari dengan angka <strong>1</strong> jika observasi terpenuhi, kosongkan jika
                                    tidak</li>
                                <li>Jangan mengubah kolom Variabel, Aspek, Item Observasi, Bobot, dan Frekuensi</li>
                                <li>Simpan file dan upload kembali</li>
                                <li>Sistem akan otomatis memproses data dan menghitung skor</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header Info -->
        <div class="mb-6 bg-white rounded-lg shadow">
            <div class="relative px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $assessment->inmate->nama }}</h2>
                        <p class="text-sm text-gray-500">
                            No. Registrasi: {{ $assessment->inmate->no_registrasi }} |
                            Periode: {{ $assessment->tanggal_penilaian->format('F Y') }}
                        </p>
                    </div>
                    <div class="fixed z-50 flex items-center space-x-3 bottom-10 right-5">
                        <!-- Auto-save Indicator - More subtle -->
                        <div class="flex items-center p-2 space-x-2 bg-slate-50">
                            <div x-show="saving" class="flex items-center text-sm text-gray-700">
                                <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span>Menyimpan...</span>
                            </div>
                            <div x-show="saved && !saving" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
                                class="flex items-center text-sm text-green-600">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Tersimpan</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button @click="submitAssessment()"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
                        <div class="mt-1 text-2xl font-semibold text-indigo-600" x-text="scores.kepribadian.toFixed(2)">0.00
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-500">Kemandirian</div>
                        <div class="mt-1 text-2xl font-semibold text-green-600" x-text="scores.kemandirian.toFixed(2)">0.00
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-500">Sikap</div>
                        <div class="mt-1 text-2xl font-semibold text-blue-600" x-text="scores.sikap.toFixed(2)">0.00</div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-500">Mental</div>
                        <div class="mt-1 text-2xl font-semibold text-purple-600" x-text="scores.mental.toFixed(2)">0.00
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-500">Total</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900" x-text="scores.total.toFixed(2)">0.00</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Days Header (Sticky) -->
        <div class="sticky z-30 mb-4 bg-white border-b border-gray-200 shadow-sm top-16">
            <div class="flex overflow-x-auto">
                <div class="flex-shrink-0 w-64 px-4 py-3 font-medium text-gray-700 border-r border-gray-200">
                    Item Observasi
                </div>
                <div class="flex">
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        <div
                            class="flex-shrink-0 w-10 px-2 py-3 text-xs font-medium text-center text-gray-700 border-r border-gray-200">
                            {{ $day }}
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Observation Items by Variabel & Aspek -->
        @foreach($variabels as $variabel)
            <div class="mb-6 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 bg-indigo-50">
                    <h3 class="text-lg font-semibold text-indigo-900">{{ $variabel->nama }}</h3>
                </div>

                @foreach($variabel->aspect as $aspek)
                    <div class="border-b border-gray-200 last:border-b-0">
                        <!-- Aspek Header (Accordion) -->
                        <div x-data="{ open: true }" class="border-b border-gray-100">
                            <button @click="open = !open"
                                class="flex items-center justify-between w-full px-6 py-3 transition-colors bg-gray-50 hover:bg-gray-100">
                                <span class="font-medium text-gray-700">{{ $aspek->nama }}</span>
                                <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <!-- Items in Aspek -->
                            <div x-show="open" x-collapse>
                                @foreach($aspek->observationItems as $item)
                                    <div class="flex hover:bg-gray-50">
                                        <!-- Item Name -->
                                        <div class="flex-shrink-0 w-64 px-4 py-3 text-sm text-gray-700 border-r border-gray-200">
                                            <div class="font-medium">{{ $item->nama_item }}</div>
                                            <div class="mt-1 text-xs text-gray-500">
                                                Bobot: {{ $item->bobot }} |
                                                Frekuensi: {{ $item->frekuensi }} |
                                                Terisi:
                                                <span class="font-semibold text-indigo-600"
                                                    x-text="checkedCounts[{{ $item->id }}] ?? 0">
                                                </span>
                                                / {{ $item->frekuensi }}
                                            </div>

                                        </div>

                                        <!-- Days Checkboxes -->
                                        <div class="flex overflow-x-auto">
                                            @for($day = 1; $day <= $daysInMonth; $day++)
                                                @php
                                                    $observation = $observationData[$item->id][$day] ?? null;
                                                    $isChecked = $observation ? $observation->is_checked : false;
                                                @endphp
                                                <div
                                                    class="flex items-center justify-center flex-shrink-0 w-10 px-2 py-3 border-r border-gray-200">
                                                    <input type="checkbox"
    data-item-id="{{ $item->id }}"
    {{ $isChecked ? 'checked' : '' }}
    @change="saveObservation({{ $item->id }}, {{ $day }}, $event)"
    class="w-4 h-4 text-indigo-600 border-gray-300 rounded cursor-pointer">

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
        saveIndicatorTimeout: null,

        // ✅ Load observationItems dengan struktur yang benar
        observationItems: @json($observationItemsArray),

        scores: {
            kepribadian: {{ $assessment->skor_kepribadian ?? 0 }},
            kemandirian: {{ $assessment->skor_kemandirian ?? 0 }},
            sikap: {{ $assessment->skor_sikap ?? 0 }},
            mental: {{ $assessment->skor_mental ?? 0 }},
            total: {{ $assessment->skor_total ?? 0 }}
        },

        autoSaver: null,
        checkedCounts: {},

        // ✅ Load existing observations dari backend
        existingObservations: @json($checkedObservations),

        init() {
            console.log('🚀 Initializing Assessment Form');
            console.log('Observation Items:', this.observationItems);
            console.log('Existing Observations:', this.existingObservations);

            // Check dependencies
            if (typeof AutoSave === 'undefined') {
                alert('AutoSave tidak ditemukan. Pastikan file autosave.js sudah di-load.');
                return;
            }

            if (typeof ScoreCalculator === 'undefined') {
                alert('ScoreCalculator tidak ditemukan dalam autosave.js');
                return;
            }

            // Initialize checked counts
            this.initializeCheckedCounts();

            // ✅ Calculate initial scores dari data yang sudah ada
            this.recalculateScores();

            // Initialize AutoSave
            this.autoSaver = new AutoSave({
                debounceTime: 300,
                batchDelay: 50,
                saveEndpoint: '{{ route("assessments.update-observation", $assessment) }}',
                csrfToken: '{{ csrf_token() }}',

                onSuccess: (result) => {
                    console.log('✅ Save success:', result);
                    if (result.scores) {
                        this.scores = result.scores;
                        console.log('📊 Scores updated:', this.scores);
                    }
                    this.showSavedIndicator();
                },

                onError: (error) => {
                    console.error('❌ Save error:', error);
                    this.showErrorNotification(
                        error.message || 'Gagal menyimpan data'
                    );
                },

                onSaving: (isSaving) => {
                    this.saving = isSaving;
                }
            });

            // Warning sebelum leave page
            window.addEventListener('beforeunload', (e) => {
                if (this.autoSaver && this.autoSaver.hasPendingChanges()) {
                    e.preventDefault();
                    e.returnValue = 'Ada perubahan yang belum tersimpan.';
                }
            });

            console.log('✅ Assessment Form initialized successfully');
        },

        initializeCheckedCounts() {
            this.checkedCounts = {};

            // Initialize counts dari checkbox yang ada
            document.querySelectorAll('input[data-item-id]').forEach(cb => {
                const itemId = parseInt(cb.dataset.itemId);

                if (!this.checkedCounts[itemId]) {
                    this.checkedCounts[itemId] = 0;
                }

                if (cb.checked) {
                    this.checkedCounts[itemId]++;
                }
            });

            console.log('📊 Initial checked counts:', this.checkedCounts);
        },

        saveObservation(itemId, day, event) {
            const isChecked = event.target.checked;

            if (!this.checkedCounts[itemId]) {
                this.checkedCounts[itemId] = 0;
            }

            // Get frequency dari observationItems
            const item = this.observationItems.find(i => i.id === itemId);
            const frequency = item ? item.frekuensi : 0;

            if (isChecked) {
                // Check frequency limit
                if (this.checkedCounts[itemId] >= frequency) {
                    event.target.checked = false;
                    this.showErrorNotification(
                        `Jumlah penilaian sudah mencapai batas frekuensi (${frequency})`
                    );
                    return;
                }
                this.checkedCounts[itemId]++;
            } else {
                if (this.checkedCounts[itemId] > 0) {
                    this.checkedCounts[itemId]--;
                }
            }

            console.log(`📝 Item ${itemId} - Day ${day} - Checked: ${isChecked}`);
            console.log(`Count: ${this.checkedCounts[itemId]}/${frequency}`);

            // Recalculate scores
            this.recalculateScores();

            // Save to backend
            this.autoSaver.save({
                observation_item_id: itemId,
                hari: day,
                is_checked: isChecked,
                catatan: null
            });
        },

        recalculateScores() {
            try {
                const observations = this.collectObservations();

                console.log('🔄 Recalculating scores...');
                console.log('Observations:', observations);
                console.log('Items:', this.observationItems);

                const calculator = new ScoreCalculator(
                    observations,
                    this.observationItems,
                    {{ $daysInMonth }}
                );

                const newScores = calculator.calculateAll();
                this.scores = newScores;

                console.log('📊 New scores:', this.scores);
            } catch (error) {
                console.error('❌ Error calculating scores:', error);
            }
        },

        collectObservations() {
            const observations = [];

            document.querySelectorAll('input[data-item-id]').forEach(cb => {
                if (cb.checked) {
                    observations.push({
                        observation_item_id: parseInt(cb.dataset.itemId),
                        is_checked: true
                    });
                }
            });

            return observations;
        },

        showSavedIndicator() {
            this.saved = true;

            if (this.saveIndicatorTimeout) {
                clearTimeout(this.saveIndicatorTimeout);
            }

            this.saveIndicatorTimeout = setTimeout(() => {
                this.saved = false;
            }, 1500);
        },

        showErrorNotification(message) {
            Toast.show(message, 'error');
        },

        async submitAssessment() {
            if (this.autoSaver && this.autoSaver.hasPendingChanges()) {
                const confirmSave = confirm(
                    'Ada perubahan yang belum tersimpan. Tunggu sebentar?'
                );

                if (confirmSave) {
                    await this.autoSaver.saveNow();
                    await new Promise(resolve => setTimeout(resolve, 500));
                } else {
                    return;
                }
            }

            if (!confirm('Submit penilaian ini untuk approval?')) {
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
                    throw new Error('Submit gagal');
                }
            } catch (error) {
                alert('Terjadi kesalahan saat submit.');
            }
        }
    };
}
</script>
@endpush

