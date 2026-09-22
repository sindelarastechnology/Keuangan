@props(['label', 'value', 'icon', 'color' => 'emerald', 'sub' => null, 'isMoney' => true])

@php
    $colors = [
        'emerald' => 'bg-emerald-100 text-emerald-600',
        'red' => 'bg-red-100 text-red-600',
        'blue' => 'bg-blue-100 text-blue-600',
        'amber' => 'bg-amber-100 text-amber-600',
        'violet' => 'bg-violet-100 text-violet-600',
    ];
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-5 flex items-start gap-4">
    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center shrink-0 {{ $colors[$color] }}">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
        </svg>
    </div>
    <div class="min-w-0">
        <div class="text-xs sm:text-sm text-gray-500 font-medium">{{ $label }}</div>
        <div class="text-lg sm:text-xl font-bold text-gray-800 truncate">
            {{ $isMoney ? formatRupiah($value) : $value }}
        </div>
        @if($sub)
            <div class="text-xs text-gray-500 mt-0.5">{{ $sub }}</div>
        @endif
    </div>
</div>
