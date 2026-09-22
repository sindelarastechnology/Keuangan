@props(['title' => null, 'footer' => null])

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if($title)
        <div class="px-4 sm:px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-800">{{ $title }}</h3>
        </div>
    @endif
    <div class="{{ isset($title) ? '' : '' }}">
        {{ $slot }}
    </div>
    @if($footer)
        <div class="px-4 sm:px-6 py-3 border-t border-gray-100 bg-gray-50">
            {{ $footer }}
        </div>
    @endif
</div>
