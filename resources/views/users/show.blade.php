@extends('layouts.app')

@section('title', 'Detail User')
@section('page-title', 'Detail User')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('users.index') }}"
           class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Daftar
        </a>

        @can('edit-users')
        <a href="{{ route('users.edit', $user) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit User
        </a>
        @endcan
    </div>

    <!-- Profile Card -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-8">
            <div class="flex items-center">
                <div class="h-24 w-24 rounded-full bg-white flex items-center justify-center text-4xl font-bold text-indigo-600">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="ml-6 text-white">
                    <h2 class="text-3xl font-bold">{{ $user->name }}</h2>
                    <p class="text-indigo-100">{{ $user->email }}</p>
                    <div class="mt-2 flex items-center space-x-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                            {{ ucwords(str_replace('_', ' ', $user->role_name)) }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $user->status_badge }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-3">INFORMASI PERSONAL</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-xs text-gray-500">Username</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $user->username }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">NIP</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $user->nip ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Jabatan</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $user->jabatan ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-3">STATISTIK AKTIVITAS</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-xs text-gray-500">Penilaian Dibuat</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $stats['assessments_created'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Penilaian Disetujui</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $stats['assessments_approved'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Rekomendasi Dibuat</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $stats['recommendations_made'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Aktivitas Terbaru</h3>
        </div>
        <div class="px-6 py-4">
            <div class="flow-root">
                <ul class="-mb-8">
                    @forelse($activities as $index => $activity)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center ring-8 ring-white">
                                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                    <div>
                                        <p class="text-sm text-gray-500">{{ $activity->description }}</p>
                                    </div>
                                    <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                        <time datetime="{{ $activity->created_at }}">{{ $activity->created_at->diffForHumans() }}</time>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="text-center py-6 text-gray-500">
                        Belum ada aktivitas
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
