@props([
    'show' => false,
    'conditionalItems' => [],
    'tanggalPenilaian' => null,
])

<div x-data="{
    show: @js($show),
    selections: {},
    dontShowAgain: false,
    submitting: false,

    init() {
        // Initialize all items as not selected (false)
        @foreach($conditionalItems as $key => $name)
            this.selections['{{ $key }}'] = false;
        @endforeach

        // Auto-show modal if needed
        if (this.show) {
            this.openModal();
        }
    },

    openModal() {
        this.show = true;
        document.body.style.overflow = 'hidden';
    },

    closeModal() {
        if (this.dontShowAgain) {
            this.skipModal();
        }
        this.show = false;
        document.body.style.overflow = '';
    },

    async submitSelections() {
        this.submitting = true;

        try {
            const response = await fetch('{{ route('assessments.set-conditional-items') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    selections: this.selections,
                    tanggal_penilaian: '{{ $tanggalPenilaian ? $tanggalPenilaian->format('Y-m-d') : now()->format('Y-m-d') }}',
                    dont_show_again: this.dontShowAgain
                })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                // Show success message
                this.showToast('Pengaturan kegiatan berhasil disimpan', 'success');
                this.closeModal();
            } else {
                throw new Error(result.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            console.error('Error submitting conditional items:', error);
            this.showToast('Gagal menyimpan pengaturan: ' + error.message, 'error');
        } finally {
            this.submitting = false;
        }
    },

    async skipModal() {
        try {
            await fetch('{{ route('assessments.skip-conditional-modal') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    tanggal_penilaian: '{{ $tanggalPenilaian ? $tanggalPenilaian->format('Y-m-d') : now()->format('Y-m-d') }}'
                })
            });
        } catch (error) {
            console.error('Error skipping modal:', error);
        }
    },

    showToast(message, type = 'success') {
        if (typeof Toast !== 'undefined') {
            Toast.show(message, type);
        } else {
            alert(message);
        }
    }
}"
x-show="show"
x-cloak
@keydown.escape.window="closeModal()"
class="fixed inset-0 z-50 overflow-y-auto"
style="display: none;">

    <!-- Backdrop -->
    <div x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
         @click="closeModal()">
    </div>

    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Center alignment trick -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

            <!-- Header -->
            <div class="px-6 py-4 bg-indigo-600">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <h3 class="text-lg font-medium text-white">
                            Pengaturan Kegiatan Kondisional
                        </h3>
                    </div>
                    <button @click="closeModal()"
                            type="button"
                            class="text-white hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
                <!-- Info Alert -->
                <div class="p-4 mb-4 border-l-4 border-blue-400 bg-blue-50">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong class="font-semibold">Penting:</strong> Pilih kegiatan yang <strong>diselenggarakan</strong> dalam bulan
                                <strong>{{ $tanggalPenilaian ? $tanggalPenilaian->translatedFormat('F Y') : now()->translatedFormat('F Y') }}</strong>.
                                Kegiatan yang tidak dicentang akan memiliki bobot 0 (tidak dinilai).
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Conditional Items List -->
                <div class="space-y-3">
                    <p class="text-sm font-medium text-gray-700">
                        Apakah kegiatan-kegiatan di bawah ini diselenggarakan dalam bulan ini?
                    </p>

                    <div class="space-y-2">
                        @foreach($conditionalItems as $key => $name)
                        <label class="flex items-start p-3 transition-colors border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="checkbox"
                                   x-model="selections.{{ $key }}"
                                   class="w-5 h-5 mt-0.5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <span class="ml-3 text-sm text-gray-700">{{ $name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Don't show again option -->
                <div class="p-3 mt-4 rounded-lg bg-gray-50">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox"
                               x-model="dontShowAgain"
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-600">
                            Jangan tampilkan dialog ini lagi untuk bulan ini
                        </span>
                    </label>
                    <p class="mt-1 ml-6 text-xs text-gray-500">
                        Pengaturan ini hanya berlaku untuk bulan berjalan. Bulan berikutnya akan muncul kembali.
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50">
                <div class="flex items-center justify-between">
                    <button @click="closeModal()"
                            type="button"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Lewati
                    </button>

                    <button @click="submitSelections()"
                            :disabled="submitting"
                            type="button"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="submitting" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="submitting ? 'Menyimpan...' : 'Simpan Pengaturan'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
