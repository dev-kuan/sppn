@extends('layouts.app')

@section('title', 'Edit Narapidana')
@section('page-title', 'Edit Data Narapidana')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Form Edit Narapidana</h3>
        </div>

        <form action="{{ route('inmates.update', $inmate) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

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
                               value="{{ old('no_registrasi', $inmate->no_registrasi) }}"
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
                               value="{{ old('nama', $inmate->nama) }}"
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
                               value="{{ old('tempat_lahir', $inmate->tempat_lahir) }}"
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
                               value="{{ old('tanggal_lahir', $inmate->tanggal_lahir->format('Y-m-d')) }}"
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
                            <option value="laki-laki" {{ old('jenis_kelamin', $inmate->jenis_kelamin) == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="perempuan" {{ old('jenis_kelamin', $inmate->jenis_kelamin) == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
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
                            @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                            <option value="{{ $agama }}" {{ old('agama', $inmate->agama) == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                            @endforeach
                        </select>
                        @error('agama')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pendidikan & Pekerjaan similar to create.blade.php -->
                    <div>
                        <label for="tingkat_pendidikan" class="block text-sm font-medium text-gray-700">Tingkat Pendidikan</label>
                        <select name="tingkat_pendidikan" id="tingkat_pendidikan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Pilih Pendidikan</option>
                            @foreach(['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2'] as $pend)
                            <option value="{{ $pend }}" {{ old('tingkat_pendidikan', $inmate->tingkat_pendidikan) == $pend ? 'selected' : '' }}>{{ $pend }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="pekerjaan_terakhir" class="block text-sm font-medium text-gray-700">Pekerjaan Terakhir</label>
                        <input type="text" name="pekerjaan_terakhir" id="pekerjaan_terakhir" value="{{ old('pekerjaan_terakhir', $inmate->pekerjaan_terakhir) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <!-- Data Pidana (similar structure with old values) -->
            <div class="pt-6 border-t border-gray-200">
                <h4 class="text-base font-semibold text-gray-900 mb-4">Data Pidana</h4>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="crime_type_id" class="block text-sm font-medium text-gray-700">Jenis Tindak Pidana <span class="text-red-500">*</span></label>
                        <select name="crime_type_id" id="crime_type_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Pilih Jenis Tindak Pidana</option>
                            @foreach($crimeTypes as $type)
                            <option value="{{ $type->id }}" {{ old('crime_type_id', $inmate->crime_type_id) == $type->id ? 'selected' : '' }}>{{ $type->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="aktif" {{ old('status', $inmate->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="dirilis" {{ old('status', $inmate->status) == 'dirilis' ? 'selected' : '' }}>Dirilis</option>
                            <option value="dipindahkan" {{ old('status', $inmate->status) == 'dipindahkan' ? 'selected' : '' }}>Dipindahkan</option>
                        </select>
                    </div>

                    <!-- Continue with other fields similar to create -->
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('inmates.show', $inmate) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
