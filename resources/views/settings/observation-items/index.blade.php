@extends('layouts.app')

@section('title', 'Item Observasi')
@section('page-title', 'Kelola Item Observasi')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div class="flex space-x-3">
            <!-- Filter Variabel -->
            <select onchange="filterByVariabel(this.value)"
                    class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">Semua Variabel</option>
                @foreach($variabels as $variabel)
                <option value="{{ $variabel->id }}" {{ request('variabel_id') == $variabel->id ? 'selected' : '' }}>
                    {{ $variabel->nama }}
                </option>
                @endforeach
            </select>

            <!-- Filter Aspek -->
            <select onchange="filterByAspek(this.value)"
                    class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">Semua Aspek</option>
                @foreach($aspects as $aspect)
                <option value="{{ $aspect->id }}" {{ request('aspek_id') == $aspect->id ? 'selected' : '' }}>
                    {{ $aspect->nama }}
                </option>
                @endforeach
            </select>

            <!-- Filter Status -->
            <select onchange="filterByStatus(this.value)"
                    class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">Semua Status</option>
                <option value="1" {{ request('aktif') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('aktif') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <button @click="$dispatch('open-modal', 'add-item')"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Item
        </button>
    </div>

    <!-- Table -->
    <div class="overflow-hidden bg-white rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-20 px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Nama Item</th>
                        <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Variabel</th>
                        <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Aspek</th>
                        <th class="w-20 px-6 py-3 text-xs font-medium text-center text-gray-500 uppercase">Bobot</th>
                        <th class="w-24 px-6 py-3 text-xs font-medium text-center text-gray-500 uppercase">Frekuensi</th>
                        <th class="w-20 px-6 py-3 text-xs font-medium text-center text-gray-500 uppercase">Urutan</th>
                        <th class="w-20 px-6 py-3 text-xs font-medium text-center text-gray-500 uppercase">Status</th>
                        <th class="relative w-32 px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono text-sm text-gray-900 whitespace-nowrap">{{ $item->kode }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="max-w-md">{{ $item->nama_item }}</div>
                            {{-- @if($item->use_dynamic_frequency)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                Dinamis: {{ $item->frequencyRule->nama_aturan }}
                            </span>
                            @endif --}}
                            @if($item->is_conditional_weight)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                Bobot Kondisional
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $item->variabel->nama }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $item->aspect->nama }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-center text-gray-900 whitespace-nowrap">
                            {{ $item->bobot }}
                        </td>
                        <td class="px-6 py-4 text-sm text-center text-gray-900 whitespace-nowrap">
                            {{ $item->frekuensi }}
                        </td>
                        <td class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                            {{ $item->sort_order }}
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <form action="{{ route('settings.observation-items.toggle', $item) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 {{ $item->aktif ? 'bg-indigo-600' : 'bg-gray-200' }}">
                                    <span class="translate-x-0 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $item->aktif ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                            <button @click="editItem({{ json_encode($item) }})"
                                    class="text-indigo-600 hover:text-indigo-900">
                                Edit
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-sm text-center text-gray-500">
                            Tidak ada item observasi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Add Modal -->
<x-modal name="add-item" maxWidth="2xl">
    <form action="{{ route('settings.observation-items.store') }}" method="POST" class="p-6">
        @csrf
        <h3 class="mb-4 text-lg font-medium text-gray-900">Tambah Item Observasi</h3>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Kode <span class="text-red-500">*</span>
                </label>
                <input type="text" name="kode" required placeholder="PK-KB-01"
                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Urutan <span class="text-red-500">*</span>
                </label>
                <input type="number" name="sort_order" required value="0" min="0"
                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Variabel <span class="text-red-500">*</span>
                </label>
                <select name="variabel_id" required onchange="loadAspects(this.value)"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Pilih Variabel</option>
                    @foreach($variabels as $variabel)
                    <option value="{{ $variabel->id }}">{{ $variabel->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Aspek <span class="text-red-500">*</span>
                </label>
                <select name="aspect_id" required id="aspect_select"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Pilih Aspek</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">
                    Nama Item <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama_item" required
                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Bobot <span class="text-red-500">*</span>
                </label>
                <input type="number" name="bobot" required value="1.00" min="0" max="10" step="0.01"
                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Frekuensi/Bulan <span class="text-red-500">*</span>
                </label>
                <input type="number" name="frekuensi_bulan" required value="1" min="0" max="31"
                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Aturan Frekuensi
                </label>
                {{-- <select name="frequency_rule_id"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Tidak Ada</option>
                    @foreach($frequencyRules as $rule)
                    <option value="{{ $rule->id }}">{{ $rule->nama_aturan }}</option>
                    @endforeach
                </select> --}}
            </div>

            <div class="flex items-center space-x-4">
                <label class="flex items-center">
                    <input type="checkbox" name="use_dynamic_frequency" value="1"
                           class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Frekuensi Dinamis</span>
                </label>

                <label class="flex items-center">
                    <input type="checkbox" name="is_conditional_weight" value="1"
                           class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Bobot Kondisional</span>
                </label>

                <label class="flex items-center">
                    <input type="checkbox" name="aktif" value="1" checked
                           class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Aktif</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end pt-6 mt-6 space-x-3 border-t border-gray-200">
            <button type="button" @click="$dispatch('close-modal', 'add-item')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Batal
            </button>
            <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700">
                Simpan
            </button>
        </div>
    </form>
</x-modal>

<!-- Edit Modal (Similar structure) -->
<div x-data="itemManager()" x-init="init()">
    <x-modal name="edit-item" maxWidth="2xl">
        <form :action="editUrl" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <h3 class="mb-4 text-lg font-medium text-gray-900">Edit Item Observasi</h3>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Similar fields as Add Modal but with x-model bindings -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kode</label>
                    <input type="text" name="kode" x-model="editData.kode" required
                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Urutan</label>
                    <input type="number" name="sort_order" x-model="editData.sort_order" required
                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <!-- Add all other fields similar to Add Modal -->
            </div>

            <div class="flex items-center justify-end pt-6 mt-6 space-x-3 border-t border-gray-200">
                <button type="button" @click="$dispatch('close-modal', 'edit-item')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700">
                    Update
                </button>
            </div>
        </form>
    </x-modal>
</div>
@endsection

@push('scripts')
<script>
function filterByVariabel(id) {
    const url = new URL(window.location);
    if (id) url.searchParams.set('variabel_id', id);
    else url.searchParams.delete('variabel_id');
    window.location = url;
}

function filterByAspek(id) {
    const url = new URL(window.location);
    if (id) url.searchParams.set('aspek_id', id);
    else url.searchParams.delete('aspek_id');
    window.location = url;
}

function filterByStatus(status) {
    const url = new URL(window.location);
    if (status !== '') url.searchParams.set('aktif', status);
    else url.searchParams.delete('aktif');
    window.location = url;
}

function itemManager() {
    return {
        editData: {},
        editUrl: '',
        init() {
            window.editItem = (item) => {
                this.editData = item;
                this.editUrl = `/settings/observation-items/${item.id}`;
                this.$dispatch('open-modal', 'edit-item');
            };
        }
    }
}
</script>
@endpush
