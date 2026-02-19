@extends('layouts.app')

@section('title', 'Daftar Penilaian')
@section('page-title', 'Daftar Penilaian')

@section('content')
<!-- Filters -->
<div class="mb-6 bg-white rounded-lg shadow">
    <div class="px-6 py-4">
        <form method="GET" action="{{ route('assessments.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <!-- Inmate Filter -->
                <div>
                    <label for="inmate_id" class="block text-sm font-medium text-gray-700">Narapidana</label>
                    <select name="inmate_id"
                            id="inmate_id"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua Narapidana</option>
                        @foreach($inmates as $inmate)
                        <option value="{{ $inmate->id }}" {{ request('inmate_id') == $inmate->id ? 'selected' : '' }}>
                            {{ $inmate->nama }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status"
                            id="status"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua Status</option>
                        <option value="draf" {{ request('status') == 'draf' ? 'selected' : '' }}>Draf</option>
                        <option value="disubmit" {{ request('status') == 'disubmit' ? 'selected' : '' }}>Disubmit</option>
                        <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <!-- Month Filter -->
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700">Bulan</label>
                    <select name="month"
                            id="month"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua Bulan</option>
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                        @endfor
                    </select>
                </div>

                <!-- Year Filter -->
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700">Tahun</label>
                    <select name="year"
                            id="year"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua Tahun</option>
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex space-x-3">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filter
                    </button>

                    <a href="{{ route('assessments.index') }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Reset
                    </a>
                </div>

                @can('create-penilaian')
                <a href="{{ route('assessments.create') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Penilaian
                </a>
                @endcan
            </div>
        </form>
    </div>
</div>

<!-- Stats Summary -->
<div class="grid grid-cols-1 gap-5 mb-6 sm:grid-cols-4">
    <div class="overflow-hidden bg-white rounded-lg shadow">
        <div class="px-4 py-5 sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">Total Penilaian</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $assessments->total() }}</dd>
        </div>
    </div>

    <div class="overflow-hidden bg-white rounded-lg shadow">
        <div class="px-4 py-5 sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">Draf</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-600">{{ $assessments->where('status', 'draf')->count() }}</dd>
        </div>
    </div>

    <div class="overflow-hidden bg-white rounded-lg shadow">
        <div class="px-4 py-5 sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">Pending Approval</dt>
            <dd class="mt-1 text-3xl font-semibold text-yellow-600">{{ $assessments->where('status', 'disubmit')->count() }}</dd>
        </div>
    </div>

    <div class="overflow-hidden bg-white rounded-lg shadow">
        <div class="px-4 py-5 sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">Disetujui</dt>
            <dd class="mt-1 text-3xl font-semibold text-green-600">{{ $assessments->where('status', 'diterima')->count() }}</dd>
        </div>
    </div>
</div>

<!-- Table -->
<div class="overflow-hidden bg-white rounded-lg shadow">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        Narapidana
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        Waktu Penilaian
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        Skor Total
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        Kategori
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        Status
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        Pembuat
                    </th>
                    <th class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($assessments as $assessment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $assessment->inmate->nama }}</div>
                                <div class="text-sm text-gray-500">{{ $assessment->inmate->no_registrasi }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                        {{ $assessment->tanggal_penilaian->translatedFormat('d F Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">{{ number_format($assessment->skor_total ?? 0, 2) }}</div>
                        <div class="text-xs text-gray-500">
                            K: {{ number_format($assessment->skor_kepribadian ?? 0, 1) }} |
                            M: {{ number_format($assessment->skor_kemandirian ?? 0, 1) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                        {{ $assessment->kategori_total ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-status-badge :status="$assessment->status" />
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                        {{ $assessment->creator->name }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('assessments.show', $assessment) }}"
                               class="text-indigo-600 hover:text-indigo-900"
                               title="Lihat Detail">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>

                            @if(in_array($assessment->status, ['draf', 'ditolak']))
                                @can('edit-penilaian')
                                <a href="{{ route('assessments.edit', $assessment) }}"
                                   class="text-green-600 hover:text-green-900"
                                   title="Edit">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                @endcan
                            @endif
                            @if ($assessment->status !== 'diterima')
                            @can('edit-penilaian')
                            <form action="{{ route('assessments.destroy', $assessment) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus penilaian ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:text-red-900"
                                        title="Hapus">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @endcan
                            @endif

                            @if($assessment->status === 'disubmit')
                                @can('approve-penilaian')
                                <form action="{{ route('assessments.approve', $assessment) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Setujui penilaian ini?')">
                                    @csrf
                                    <button type="submit"
                                            class="text-green-600 hover:text-green-900"
                                            title="Setujui">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </form>
                                    <button onclick="openRejectModal()"
                                            class="text-red-600 hover:text-red-900"
                                            title="Tolak">
                                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 9-6 6m0-6 6 6m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
</svg>
                                    </button>
                                <!-- Reject Modal -->
<x-modal name="reject-modal" maxWidth="md">
    <form action="{{ route('assessments.reject', $assessment) }}" method="POST" class="p-6 text-left">
        @csrf
        <h3 class="mb-4 text-lg font-medium text-gray-900">Tolak Penilaian</h3>

        <div class="mb-4">
            <label for="catatan" class="block mb-2 text-sm font-medium text-gray-700">
                Alasan Penolakan <span class="text-red-500">*</span>
            </label>
            <textarea name="catatan"
                      id="catatan"
                      rows="4"
                      required
                      placeholder="Jelaskan alasan penolakan..."
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <button type="button"
                    @click="$dispatch('close-modal', 'reject-modal')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Batal
            </button>
            <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700">
                Tolak Penilaian
            </button>
        </div>
    </form>
</x-modal>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                        <p class="mt-1 text-sm text-gray-500">Belum ada penilaian yang terdaftar</p>
                        @can('create-penilaian')
                        <div class="mt-6">
                            <a href="{{ route('assessments.create') }}"
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
                                Buat Penilaian
                            </a>
                        </div>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($assessments->hasPages())
    <div class="px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
        {{ $assessments->links() }}
    </div>
    @endif
</div>
@endsection
@push('scripts')
<script>
function openRejectModal() {
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'reject-modal' }));
}
</script>
@endpush
