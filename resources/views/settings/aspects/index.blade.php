@extends('layouts.app')

@section('title', 'Aspek Penilaian')
@section('page-title', 'Kelola Aspek Penilaian')

@section('content')
<div class="space-y-6">

    <!-- Filter by Variabel -->
    <div class="bg-white shadow rounded-lg p-4 flex justify-between items-end">
    {{-- filter --}}
    <div class="filter">
        <label for="filter_variabel" class="block text-sm font-medium text-gray-700 mb-2">Filter by Variabel</label>
        <select id="filter_variabel"
                onchange="window.location.href = '{{ route('settings.aspects') }}?variabel_id=' + this.value"
                class="block w-full max-w-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <option value="">Semua Variabel</option>
            @foreach($variabels as $variabel)
            <option value="{{ $variabel->id }}" {{ request('variabel_id') == $variabel->id ? 'selected' : '' }}>
                {{ $variabel->nama }}
            </option>
            @endforeach
        </select>
    </div>
    <!-- Add Button -->
    <div class="add-button">
        <button @click="$dispatch('open-modal', 'add-aspect')"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Aspek
        </button>
    </div>
    </div>

    <!-- Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Aspek</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Variabel</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Item</th>
                    <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($aspects as $aspect)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $aspect->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $aspect->nama }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            {{ $aspect->variabel->nama }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $aspect->observation_items_count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button @click="editAspect({{ json_encode($aspect) }})"
                                class="text-indigo-600 hover:text-indigo-900">
                            Edit
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                        Belum ada aspek penilaian
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<x-modal name="add-aspect" maxWidth="md">
    <form action="{{ route('settings.aspects.store') }}" method="POST" class="p-6">
        @csrf
        <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Aspek Baru</h3>

        <div class="space-y-4">
            <div>
                <label for="assessment_variabel_id" class="block text-sm font-medium text-gray-700">
                    Variabel <span class="text-red-500">*</span>
                </label>
                <select name="assessment_variabel_id"
                        id="assessment_variabel_id"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Pilih Variabel</option>
                    @foreach($variabels as $variabel)
                    <option value="{{ $variabel->id }}">{{ $variabel->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700">
                    Nama Aspek <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="nama"
                       id="nama"
                       required
                       placeholder="Contoh: Kesadaran Beragama"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3 mt-6">
            <button type="button"
                    @click="$dispatch('close-modal', 'add-aspect')"
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

<!-- Edit Modal -->
<div x-data="aspectManager()" x-init="init()">
    <x-modal name="edit-aspect" maxWidth="md">
        <form :action="editUrl" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Aspek</h3>

            <div class="space-y-4">
                <div>
                    <label for="edit_assessment_variabel_id" class="block text-sm font-medium text-gray-700">
                        Variabel <span class="text-red-500">*</span>
                    </label>
                    <select name="assessment_variabel_id"
                            id="edit_assessment_variabel_id"
                            x-model="editData.assessment_variabel_id"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Pilih Variabel</option>
                        @foreach($variabels as $variabel)
                        <option value="{{ $variabel->id }}">{{ $variabel->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="edit_nama" class="block text-sm font-medium text-gray-700">
                        Nama Aspek <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="nama"
                           id="edit_nama"
                           x-model="editData.nama"
                           required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-6">
                <button type="button"
                        @click="$dispatch('close-modal', 'edit-aspect')"
                        class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Update
                </button>
            </div>
        </form>
    </x-modal>
</div>
@endsection

@push('scripts')
<script>
function aspectManager() {
    return {
        editData: {
            id: null,
            nama: '',
            assessment_variabel_id: ''
        },
        editUrl: '',

        init() {
            window.editAspect = (aspect) => {
                this.editData = {
                    id: aspect.id,
                    nama: aspect.nama,
                    assessment_variabel_id: aspect.assessment_variabel_id
                };
                this.editUrl = `/settings/aspects/${aspect.id}`;
                this.$dispatch('open-modal', 'edit-aspect');
            };
        }
    }
}
</script>
@endpush
