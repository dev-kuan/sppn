@props(['status', 'type' => 'default'])

@php
$classes = [
    'aktif' => 'bg-green-100 text-green-800',
    'dirilis' => 'bg-blue-100 text-blue-800',
    'dipindahkan' => 'bg-yellow-100 text-yellow-800',
    'draf' => 'bg-gray-100 text-gray-800',
    'disubmit' => 'bg-blue-100 text-blue-800',
    'diterima' => 'bg-green-100 text-green-800',
    'ditolak' => 'bg-red-100 text-red-800',
];

$class = $classes[$status] ?? 'bg-gray-100 text-gray-800';
@endphp

<span {{ $attributes->merge(['class' => "px-2 inline-flex text-xs leading-5 font-semibold rounded-full {$class}"]) }}>
    {{ ucfirst($status) }}
</span>
