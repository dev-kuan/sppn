@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Generate Laporan')

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <!-- Assessment Report -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Laporan Penilaian Individual</h3>
            <p class="mt-1 text-sm text-gray-500">Generate laporan detail penilaian narapidana</p>
        </div>
        <form action="{{ route('reports.assessment-pdf') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="assessment_id" class="block text-sm font-medium text-gray-700">
                    Pilih Penilaian <span class="text-red-500">*</span>
                </label>
                <select name="assessment_id"
                        id="assessment_id"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Pilih Penilaian</option>
                    @foreach($inmates as $inmate)
                        @foreach($inmate->assessments()->diterima()->get() as $assessment)
                        <option value="{{ $assessment->id }}">
                            {{ $inmate->nama }} - {{ $assessment->tanggal_penilaian->format('F Y') }}
                        </option>
                        @endforeach
                    @endforeach
                </select>
            </div>

            <button type="submit"
                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Download PDF
            </button>
        </form>
    </div>

    <!-- Monthly Report -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Laporan Bulanan</h3>
            <p class="mt-1 text-sm text-gray-500">Rekap penilaian seluruh narapidana per bulan</p>
        </div>
        <form action="{{ route('reports.monthly-pdf') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700">
                        Bulan <span class="text-red-500">*</span>
                    </label>
                    <select name="month"
                            id="month"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700">
                        Tahun <span class="text-red-500">*</span>
                    </label>
                    <select name="year"
                            id="year"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <button type="submit"
                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Download PDF
            </button>
        </form>
    </div>

    <!-- Progress Report -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Laporan Progress Narapidana</h3>
            <p class="mt-1 text-sm text-gray-500">Perkembangan penilaian narapidana dalam periode tertentu</p>
        </div>
        <form action="{{ route('reports.inmate-progress-pdf') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="inmate_id_progress" class="block text-sm font-medium text-gray-700">
                    Pilih Narapidana <span class="text-red-500">*</span>
                </label>
                <select name="inmate_id"
                        id="inmate_id_progress"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Pilih Narapidana</option>
                    @foreach($inmates as $inmate)
                    <option value="{{ $inmate->id }}">{{ $inmate->nama }} ({{ $inmate->no_registrasi }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">
                        Dari Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="start_date"
                           id="start_date"
                           required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">
                        Sampai Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="end_date"
                           id="end_date"
                           required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>

            <button type="submit"
                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Download PDF
            </button>
        </form>
    </div>

    <!-- Export Excel -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Export Data ke Excel</h3>
            <p class="mt-1 text-sm text-gray-500">Download data penilaian atau narapidana dalam format Excel</p>
        </div>
        <div class="p-6 space-y-4">
            <!-- Export Assessments -->
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">Export Penilaian</h4>
                <form action="{{ route('reports.export-assessments') }}" method="GET" class="space-y-3">
                    <div class="grid grid-cols-3 gap-3">
                        <select name="month" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Semua Bulan</option>
                            @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                            @endfor
                        </select>

                        <select name="year" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Semua Tahun</option>
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>

                        <select name="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Semua Status</option>
                            <option value="draf">Draf</option>
                            <option value="disubmit">Disubmit</option>
                            <option value="diterima">Diterima</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>

                    <button type="submit"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Penilaian
                    </button>
                </form>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Export Narapidana</h4>
                <form action="{{ route('reports.export-inmates') }}" method="GET" class="space-y-3">
                    <select name="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="dirilis">Dirilis</option>
                        <option value="dipindahkan">Dipindahkan</option>
                    </select>

                    <button type="submit"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Narapidana
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
