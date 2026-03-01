@extends('layouts.app')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan Sistem')

@section('content')
<div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert -->
            @if(session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('settings.system.update') }}" enctype="multipart/form-data"
                x-data="systemSettings()">
                @csrf
                @method('PUT')

                <!-- Informasi Lembaga -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b-2 border-indigo-500">
                            Informasi Lembaga Pemasyarakatan
                        </h3>

                        <div class="space-y-4">
                            <!-- Nama Lapas -->
                            <div>
                                <label for="institution_name" class="block font-medium text-sm text-gray-700">
                                    Nama Lembaga Pemasyarakatan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="institution_name" name="institution_name"
                                    value="{{ old('institution_name', config('institution.name')) }}"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    placeholder="Contoh: Lembaga Pemasyarakatan Kelas IIA Kupang" required>
                                @error('institution_name')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Alamat -->
                            <div>
                                <label for="institution_address" class="block font-medium text-sm text-gray-700">
                                    Alamat
                                </label>
                                <textarea id="institution_address" name="institution_address" rows="2"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    placeholder="Alamat lengkap lembaga">{{ old('institution_address', config('institution.address')) }}</textarea>
                                @error('institution_address')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Telepon -->
                                <div>
                                    <label for="institution_phone" class="block font-medium text-sm text-gray-700">
                                        Telepon
                                    </label>
                                    <input type="text" id="institution_phone" name="institution_phone"
                                        value="{{ old('institution_phone', config('institution.phone')) }}"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        placeholder="(0380) 123456">
                                    @error('institution_phone')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="institution_email" class="block font-medium text-sm text-gray-700">
                                        Email
                                    </label>
                                    <input type="email" id="institution_email" name="institution_email"
                                        value="{{ old('institution_email', config('institution.email')) }}"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        placeholder="lapas@example.com">
                                    @error('institution_email')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pejabat Penandatangan 1 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b-2 border-indigo-500">
                            Pejabat Penandatangan 1
                        </h3>

                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Nama -->
                                <div>
                                    <label for="officer1_name" class="block font-medium text-sm text-gray-700">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="officer1_name" name="officer1_name"
                                        value="{{ old('officer1_name', config('institution.officers.officer1.name')) }}"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        required>
                                    @error('officer1_name')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- NIP -->
                                <div>
                                    <label for="officer1_nip" class="block font-medium text-sm text-gray-700">
                                        NIP <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="officer1_nip" name="officer1_nip"
                                        value="{{ old('officer1_nip', config('institution.officers.officer1.nip')) }}"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        placeholder="198001012005011001" required>
                                    @error('officer1_nip')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Jabatan -->
                            <div>
                                <label for="officer1_position" class="block font-medium text-sm text-gray-700">
                                    Jabatan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="officer1_position" name="officer1_position"
                                    value="{{ old('officer1_position', config('institution.officers.officer1.position')) }}"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    placeholder="Contoh: Kepala Lembaga Pemasyarakatan" required>
                                @error('officer1_position')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanda Tangan -->
                            <div>
                                <label for="officer1_signature" class="block font-medium text-sm text-gray-700">
                                    Tanda Tangan
                                </label>

                                @if(config('institution.officers.officer1.signature'))
                                    <div class="mt-2 mb-3">
                                        <img src="{{ asset('storage/' . config('institution.officers.officer1.signature')) }}"
                                            alt="Tanda Tangan" class="h-24 border-2 border-gray-300 rounded p-2 bg-white">
                                        <p class="text-sm text-gray-500 mt-1">Tanda tangan saat ini</p>
                                    </div>
                                @endif

                                <input type="file" id="officer1_signature" name="officer1_signature" accept="image/*"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    @change="previewSignature1">
                                <p class="text-sm text-gray-500 mt-1">Format: PNG dengan background transparan (disarankan).
                                    Maksimal 2MB</p>
                                @error('officer1_signature')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror

                                <!-- Preview -->
                                <div x-show="signature1Preview" class="mt-3">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Preview tanda tangan baru:</p>
                                    <img :src="signature1Preview"
                                        class="h-24 border-2 border-indigo-300 rounded p-2 bg-white">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pejabat Penandatangan 2 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b-2 border-indigo-500">
                            Pejabat Penandatangan 2
                        </h3>

                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Nama -->
                                <div>
                                    <label for="officer2_name" class="block font-medium text-sm text-gray-700">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="officer2_name" name="officer2_name"
                                        value="{{ old('officer2_name', config('institution.officers.officer2.name')) }}"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        required>
                                    @error('officer2_name')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- NIP -->
                                <div>
                                    <label for="officer2_nip" class="block font-medium text-sm text-gray-700">
                                        NIP <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="officer2_nip" name="officer2_nip"
                                        value="{{ old('officer2_nip', config('institution.officers.officer2.nip')) }}"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        placeholder="198501012006011001" required>
                                    @error('officer2_nip')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Jabatan -->
                            <div>
                                <label for="officer2_position" class="block font-medium text-sm text-gray-700">
                                    Jabatan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="officer2_position" name="officer2_position"
                                    value="{{ old('officer2_position', config('institution.officers.officer2.position')) }}"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    placeholder="Contoh: Kepala Seksi Bimbingan Kemasyarakatan" required>
                                @error('officer2_position')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanda Tangan -->
                            <div>
                                <label for="officer2_signature" class="block font-medium text-sm text-gray-700">
                                    Tanda Tangan
                                </label>

                                @if(config('institution.officers.officer2.signature'))
                                    <div class="mt-2 mb-3">
                                        <img src="{{ asset('storage/' . config('institution.officers.officer2.signature')) }}"
                                            alt="Tanda Tangan" class="h-24 border-2 border-gray-300 rounded p-2 bg-white">
                                        <p class="text-sm text-gray-500 mt-1">Tanda tangan saat ini</p>
                                    </div>
                                @endif

                                <input type="file" id="officer2_signature" name="officer2_signature" accept="image/*"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    @change="previewSignature2">
                                <p class="text-sm text-gray-500 mt-1">Format: PNG dengan background transparan (disarankan).
                                    Maksimal 2MB</p>
                                @error('officer2_signature')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror

                                <!-- Preview -->
                                <div x-show="signature2Preview" class="mt-3">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Preview tanda tangan baru:</p>
                                    <img :src="signature2Preview"
                                        class="h-24 border-2 border-indigo-300 rounded p-2 bg-white">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
</div>
@endsection
@push('scripts')
    <script>
        function systemSettings() {
            return {
                signature1Preview: null,
                signature2Preview: null,

                previewSignature1(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.signature1Preview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },

                previewSignature2(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.signature2Preview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                }
            }
        }
    </script>
@endpush
