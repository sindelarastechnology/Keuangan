@props([
    'variant' => 'primary',
    'size' => 'md',
    'loadingText' => 'Menyimpan...',
])

<x-button
    :variant="$variant"
    :size="$size"
    type="submit"
    x-data="{ loading: false }"
    x-on:click="loading = true; setTimeout(() => { loading = false; }, 10000);"
>
    <span x-show="!loading" x-cloak>{{ $slot }}</span>
    <span x-show="loading" x-cloak class="inline-flex items-center gap-2">
        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-6.219-8.56"/></svg>
        {{ $loadingText }}
    </span>
</x-button>