@props(['title' => 'Filter'])

<div x-data="{ open: window.innerWidth >= 768 }">
    <div class="px-4 py-2 border-b border-gray-100">
        <button type="button" @click="open = !open"
                class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700 hover:text-gray-900 rounded-lg px-2 py-2 transition focus:outline-none focus:ring-2 focus:ring-emerald-500 w-full sm:w-auto">
            <svg x-show="!open" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            <svg x-show="open" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
            {{ $title }}
        </button>
    </div>
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1">
        <div class="p-4">
            {{ $slot }}
        </div>
    </div>
</div>