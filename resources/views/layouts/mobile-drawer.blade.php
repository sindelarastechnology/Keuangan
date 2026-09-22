@php
    $nav = \App\Services\PlanService::saringNav(require resource_path('views/layouts/nav-data.php'));
    $paket = Auth::check() ? \App\Services\PlanService::plan(Auth::user()) : 'free';
@endphp

<aside id="mobile-drawer" class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] bg-white shadow-xl transform -translate-x-full transition-transform duration-200 lg:hidden flex flex-col">
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-gray-800">
            <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'KasPro') }}"
                 class="w-8 h-8 rounded-lg bg-white object-contain p-0.5 ring-1 ring-gray-200" />
            <span class="font-bold text-lg">{{ config('app.name', 'KasPro') }}</span>
        </a>
        <button class="p-2 rounded-lg text-gray-500 hover:bg-gray-100"
                aria-label="Tutup menu"
                onclick="document.getElementById('mobile-drawer').classList.add('-translate-x-full'); document.getElementById('menu-overlay').classList.add('hidden');">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto py-3 px-3 space-y-1" aria-label="Navigasi utama">
        @foreach($nav as $group)
            @php $single = count($group['items']) === 1; @endphp

            @if($single)
                @php
                    $first = $group['items'][0];
                    $groupActive = request()->routeIs($first['route'] . '*');
                @endphp
                <a href="{{ route($first['route']) }}"
                   class="flex items-center justify-between px-4 py-3 rounded-lg text-lg font-semibold transition {{ $groupActive ? 'bg-emerald-50 text-emerald-700' : 'text-gray-900 hover:bg-gray-50' }}">
                    {{ $group['label'] }}
                </a>
            @else
                @php
                    $groupActive = collect($group['items'])->contains(function ($i) {
                        if (isset($i['children'])) {
                            return collect($i['children'])->contains(fn($c) => request()->routeIs($c['route'] . '*'));
                        }
                        return request()->routeIs($i['route'] . '*');
                    });
                @endphp
                <div x-data="{ open: {{ $groupActive ? 'true' : 'false' }} }" class="border-b border-gray-100 last:border-0">
                    <button @click="open = ! open"
                            class="w-full flex items-center justify-between px-4 py-3 text-left rounded-lg text-lg font-semibold transition {{ $groupActive ? 'bg-emerald-50 text-emerald-700' : 'text-gray-900 hover:bg-gray-50' }}">
                        {{ $group['label'] }}
                        <svg class="w-4 h-4 shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition x-cloak class="pb-2">
                        @foreach($group['items'] as $item)
                            @if(isset($item['children']))
                                @php
                                    $activeItem = collect($item['children'])->contains(fn($c) => request()->routeIs($c['route'] . '*'));
                                @endphp
                                <div x-data="{ sub: {{ $activeItem ? 'true' : 'false' }} }">
                                    <button @click="sub = ! sub"
                                            class="w-full flex items-center justify-between gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition {{ $activeItem ? 'text-emerald-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                        <span class="flex items-center gap-3">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                                            {{ $item['label'] }}
                                        </span>
                                        <svg class="w-4 h-4 shrink-0 transition-transform" :class="sub ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="sub" x-transition x-cloak class="pb-1">
                                        @foreach($item['children'] as $child)
                                            @php $activeChild = request()->routeIs($child['route'] . '*'); @endphp
                                            <a href="{{ route($child['route']) }}"
                                               class="flex items-center gap-3 pl-10 pr-4 py-2 rounded-lg text-sm transition {{ $activeChild ? 'text-emerald-700 font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span>
                                                {{ $child['label'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                @php $activeItem = request()->routeIs($item['route'] . '*'); @endphp
                                <a href="{{ route($item['route']) }}"
                                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition {{ $activeItem ? 'text-emerald-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                                    {{ $item['label'] }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </nav>

    <div class="p-4 border-t border-gray-200">
        <div class="flex items-center gap-3 px-1 mb-3">
            <span class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <div class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $paket === 'pro' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $paket === 'pro' ? 'Pro' : 'Gratis' }}</span>
                </div>
                <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
            </div>
        </div>
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Profil
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Keluar
            </button>
        </form>
    </div>
</aside>