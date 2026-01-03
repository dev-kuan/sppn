@extends('layouts.app')

@section('title', 'Activity Log')
@section('page-title', 'Activity Log Sistem')

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Riwayat Aktivitas Sistem</h3>
        <p class="mt-1 text-sm text-gray-500">Log semua aktivitas user dalam sistem</p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aktivitas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detail</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($activities as $activity)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $activity->created_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $activity->causer->name ?? 'System' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $activity->description }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $activity->subject_type ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                        Belum ada aktivitas
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($activities->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $activities->links() }}
    </div>
    @endif
</div>
@endsection
