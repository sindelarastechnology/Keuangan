@props(['title', 'subtitle' => null])

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold text-gray-800">{{ $title }}</h2>
        @if($subtitle)
            <p class="text-sm text-gray-500 mt-0.5">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="flex items-center gap-2 flex-wrap justify-start sm:justify-end">
        {{ $slot }}
    </div>
</div>
