@props(['title', 'value', 'icon', 'color' => 'indigo', 'link' => null])

<div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="p-5">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="rounded-md bg-{{ $color }}-500 p-3">
                    {{ $icon }}
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">{{ $title }}</dt>
                    <dd class="flex items-baseline">
                        <div class="text-2xl font-semibold text-gray-900">{{ $value }}</div>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    @if($link)
    <div class="bg-gray-50 px-5 py-3">
        <div class="text-sm">
            <a href="{{ $link }}" class="font-medium text-{{ $color }}-600 hover:text-{{ $color }}-500">
                Lihat detail
            </a>
        </div>
    </div>
    @endif
</div>
