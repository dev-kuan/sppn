@extends('layouts.app')

@section('title', 'Variabel Penilaian')
@section('page-title', 'Kelola Variabel Penilaian')

@section('content')
<div class="space-y-6">
    <!-- Add Button -->
    <div class="flex justify-end">
        <button @click="$dispatch('open-modal', 'add-variabel')"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Variabel
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Variabel</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Aspek</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Item</th>
                    <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($variabels as $variabel)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $variabel->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $variabel->nama }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $variabel->aspect_count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $variabel->observation_items_count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button @click="editVariabel({{ $variabel }})"
                                class="text-indigo-600 hover:text-indigo-900">
                            Edit
                        </button>
                        <div class="flex items-center justify-end space-x-2">
                            <button @click="editVariabel({{ $variabel }})"
                                class="text-indigo-600 hover:text-indigo-900">
                            Edit
                        </button>

                            {{-- @can('delete-narapidana')
                            <form action="{{ route('inmates.destroy', $inmate) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus narapidana ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:text-red-900"
                                        title="Hapus">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @endcan --}}
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Modal -->
<x-modal name="add-variabel" maxWidth="md">
    <form action="{{ route('settings.variabels.store') }}" method="POST" class="p-6">
        @csrf
        <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Variabel Baru</h3>

        <div class="mb-4">
            <label for="nama" class="block text-sm font-medium text-gray-700">
                Nama Variabel <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   name="nama"
                   id="nama"
                   required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        <div class="flex items-center justify-end space-x-3">
            <button type="button"
                    @click="$dispatch('close-modal', 'add-variabel')"
                    class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Batal
            </button>
            <button type="submit"
                    class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                Simpan
            </button>
        </div>
    </form>
</x-modal>
@endsection
