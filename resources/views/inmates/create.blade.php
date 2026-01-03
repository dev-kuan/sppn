@extends('layouts.app')

@section('title', 'Tambah Narapidana')
@section('page-title', 'Tambah Narapidana Baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Form Data Narapidana</h3>
        </div>

        <form action="{{ route('inmates.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Data Personal -->
            <div>
                <h4 class="text-base font-semibold text-gray-900 mb-4">Data Personal</h4>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- No Registrasi -->
                    <div>
                        <label for="no_registrasi" class="block text-sm font-medium text-gray-700">
                            No. Registrasi <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="no_registrasi"
                               id="no_registrasi"
                               value="{{ old('no_registrasi') }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('no_registrasi') border-red-500 @enderror">
                        @error('no_registrasi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama -->
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="nama"
                               id="nama"
                               value="{{ old('nama') }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('nama') border-red-500 @enderror">
                        @error('nama')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tempat Lahir -->
                    <div>
                        <label for="tempat_lahir" class="block text-sm font-medium text-gray-700">
                            Tempat Lahir <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="tempat_lahir"
                               id="tempat_lahir"
                               value="{{ old('tempat_lahir') }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('tempat_lahir') border-red-500 @enderror">
                        @error('tempat_lahir')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700">
                            Tanggal Lahir <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="tanggal_lahir"
                               id="tanggal_lahir"
                               value="{{ old('tanggal_lahir') }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('tanggal_lahir') border-red-500 @enderror">
                        @error('tanggal_lahir')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700">
                            Jenis Kelamin <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_kelamin"
                                id="jenis_kelamin"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('jenis_kelamin') border-red-500 @enderror">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="laki-laki" {{ old('jenis_kelamin') == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="perempuan" {{ old('jenis_kelamin') == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Agama -->
                    <div>
                        <label for="agama" class="block text-sm font-medium text-gray-700">
                            Agama <span class="text-red-500">*</span>
                        </label>
                        <select name="agama"
                                id="agama"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('agama') border-red-500 @enderror">
                            <option value="">Pilih Agama</option>
                            <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                        @error('agama')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tingkat Pendidikan -->
                    <div>
                        <label for="tingkat_pendidikan" class="block text-sm font-medium text-gray-700">
                            Tingkat Pendidikan
                        </label>
                        <select name="tingkat_pendidikan"
                                id="tingkat_pendidikan"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Pilih Pendidikan</option>
                            <option value="SD" {{ old('tingkat_pendidikan') == 'SD' ? 'selected' : '' }}>SD</option>
                            <option value="SMP" {{ old('tingkat_pendidikan') == 'SMP' ? 'selected' : '' }}>SMP</option>
                            <option value="SMA" {{ old('tingkat_pendidikan') == 'SMA' ? 'selected' : '' }}>SMA</option>
                            <option value="D3" {{ old('tingkat_pendidikan') == 'D3' ? 'selected' : '' }}>D3</option>
                            <option value="S1" {{ old('tingkat_pendidikan') == 'S1' ? 'selected' : '' }}>S1</option>
                            <option value="S2" {{ old('tingkat_pendidikan') == 'S2' ? 'selected' : '' }}>S2</option>
                        </select>
                        @error('tingkat_pendidikan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pekerjaan Terakhir -->
                    <div>
                        <label for="pekerjaan_terakhir" class="block text-sm font-medium text-gray-700">
                            Pekerjaan Terakhir
                        </label>
                        <input type="text"
                               name="pekerjaan_terakhir"
                               id="pekerjaan_terakhir"
                               value="{{ old('pekerjaan_terakhir') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('pekerjaan_terakhir')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Data Pidana -->
            <div class="pt-6 border-t border-gray-200">
                <h4 class="text-base font-semibold text-gray-900 mb-4">Data Pidana</h4>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Jenis Tindak Pidana -->
                    <div class="sm:col-span-2">
                        <label for="crime_type_id" class="block text-sm font-medium text-gray-700">
                            Jenis Tindak Pidana <span class="text-red-500">*</span>
                        </label>
                        <select name="crime_type_id"
                                id="crime_type_id"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('crime_type_id') border-red-500 @enderror">
                            <option value="">Pilih Jenis Tindak Pidana</option>
                            @foreach($crimeTypes as $type)
                            <option value="{{ $type->id }}" {{ old('crime_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->nama }}
                            </option>
                            @endforeach
                        </select>
                        @error('crime_type_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Lama Pidana -->
                    <div>
                        <label for="lama_pidana_bulan" class="block text-sm font-medium text-gray-700">
                            Lama Pidana (Bulan) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="lama_pidana_bulan"
                               id="lama_pidana_bulan"
                               value="{{ old('lama_pidana_bulan') }}"
                               min="1"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('lama_pidana_bulan') border-red-500 @enderror">
                        @error('lama_pidana_bulan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sisa Pidana -->
                    <div>
                        <label for="sisa_pidana_bulan" class="block text-sm font-medium text-gray-700">
                            Sisa Pidana (Bulan) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="sisa_pidana_bulan"
                               id="sisa_pidana_bulan"
                               value="{{ old('sisa_pidana_bulan') }}"
                               min="0"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('sisa_pidana_bulan') border-red-500 @enderror">
                        @error('sisa_pidana_bulan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah Residivisme -->
                    <div>
                        <label for="jumlah_residivisme" class="block text-sm font-medium text-gray-700">
                            Jumlah Residivisme
                        </label>
                        <input type="number"
                               name="jumlah_residivisme"
                               id="jumlah_residivisme"
                               value="{{ old('jumlah_residivisme', 0) }}"
                               min="0"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('jumlah_residivisme')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Masuk -->
                    <div>
                        <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700">
                            Tanggal Masuk <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="tanggal_masuk"
                               id="tanggal_masuk"
                               value="{{ old('tanggal_masuk') }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('tanggal_masuk') border-red-500 @enderror">
                        @error('tanggal_masuk')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Bebas -->
                    <div>
                        <label for="tanggal_bebas" class="block text-sm font-medium text-gray-700">
                            Tanggal Bebas (Estimasi)
                        </label>
                        <input type="date"
                               name="tanggal_bebas"
                               id="tanggal_bebas"
                               value="{{ old('tanggal_bebas') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('tanggal_bebas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Data Pembinaan -->
            <div class="pt-6 border-t border-gray-200">
                <h4 class="text-base font-semibold text-gray-900 mb-4">Data Pembinaan</h4>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Pelatihan -->
                    <div>
                        <label for="pelatihan" class="block text-sm font-medium text-gray-700">
                            Pelatihan
                        </label>
                        <input type="text"
                               name="pelatihan"
                               id="pelatihan"
                               value="{{ old('pelatihan') }}"
                               placeholder="Contoh: Tata Boga, Otomotif"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('pelatihan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Program Kerja -->
                    <div>
                        <label for="program_kerja" class="block text-sm font-medium text-gray-700">
                            Program Kerja
                        </label>
                        <input type="text"
                               name="program_kerja"
                               id="program_kerja"
                               value="{{ old('program_kerja') }}"
                               placeholder="Contoh: Produksi Furniture"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('program_kerja')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catatan Kesehatan -->
                    <div class="sm:col-span-2">
                        <label for="catatan_kesehatan" class="block text-sm font-medium text-gray-700">
                            Catatan Kesehatan
                        </label>
                        <textarea name="catatan_kesehatan"
                                  id="catatan_kesehatan"
                                  rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('catatan_kesehatan') }}</textarea>
                        @error('catatan_kesehatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('inmates.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
