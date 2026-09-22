@props([
    'title',
    'titleClass' => 'text-gray-900',
    'subtitle' => null,
    'amount' => null,
    'amountClass' => 'text-slate-700',
    'href' => null,
])

<div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            @if ($href)
                <a href="{{ $href }}" class="block font-medium truncate {{ $titleClass }}">{{ $title }}</a>
            @else
                <p class="font-medium truncate {{ $titleClass }}">{{ $title }}</p>
            @endif
            @if ($subtitle)
                <p class="text-sm text-gray-500 mt-0.5 truncate">{{ $subtitle }}</p>
            @endif
        </div>
        @isset($badge)
            <div class="shrink-0">{{ $badge }}</div>
        @endisset
    </div>
    @if ($amount)
        <p class="mt-2 text-sm font-semibold {{ $amountClass }}">{{ $amount }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-1">{{ $slot }}</div>
    @endif
    @isset($actions)
        <div class="flex flex-wrap items-center gap-2 mt-3">{{ $actions }}</div>
    @endisset
</div>