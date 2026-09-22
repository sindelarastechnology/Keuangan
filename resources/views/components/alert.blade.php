@props(['type' => 'success', 'message'])

@php
    $styles = [
        'success' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
        'error' => 'bg-red-50 border-red-200 text-red-700',
        'warning' => 'bg-amber-50 border-amber-200 text-amber-700',
        'info' => 'bg-blue-50 border-blue-200 text-blue-700',
    ][$type] ?? 'bg-emerald-50 border-emerald-200 text-emerald-700';

    $icons = [
        'success' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'error' => 'M12 9v2m0 4h.01M20 12a8 8 0 11-16 0 8 8 0 0116 0zM10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z',
        'warning' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'info' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    ][$type] ?? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
@endphp

<div x-data="{ show: true }" x-show="show" @click="show = false" role="alert" class="mb-4" @if($type === 'success') x-init="setTimeout(() => show = false, 5000)" @endif>
    <div class="flex items-start gap-3 p-4 rounded-lg border {{ $styles }}">
        <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons }}"/>
        </svg>
        <div class="flex-1 text-sm font-medium">{{ $message }}</div>
        <button class="shrink-0 opacity-50 hover:opacity-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</div>
