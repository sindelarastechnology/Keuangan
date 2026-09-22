@php
    $nav = \App\Services\PlanService::saringNav(require resource_path('views/layouts/nav-data.php'));
    $paket = Auth::check() ? \App\Services\PlanService::plan(Auth::user()) : 'free';
    $tickerWarna = config('pengumuman.mode') === 'maintenance'
        ? 'bg-rose-600 text-white'
        : 'bg-amber-400/95 text-amber-950';
    $pesanTicker = trim(implode(' · ', (array) config('pengumuman.pesan', [])));
    $jarakTicker = max(0, (int) config('pengumuman.jarak', 48));
@endphp

<div class="sticky top-0 z-30 bg-white border-b border-gray-200">
    @if($pesanTicker !== '')
        <div role="region" aria-label="Pengumuman: {{ $pesanTicker }}"
             class="flex items-center gap-2 overflow-hidden {{ $tickerWarna }} px-3 sm:px-6 py-1 text-xs sm:text-sm font-medium">
            <svg class="w-4 h-4 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div class="flex-1 min-w-0 overflow-hidden">
                <div aria-hidden="true" class="animate-marquee w-max motion-reduce:w-full motion-reduce:animate-none flex items-center whitespace-nowrap motion-reduce:whitespace-normal">
                    @foreach((array) config('pengumuman.pesan', []) as $p)
                        <span style="margin-right: {{ $jarakTicker }}px" class="flex items-center gap-2">
                            <span>{{ $p }}</span>
                            <span aria-hidden="true" class="w-2 h-2 rounded-full bg-current opacity-60 shrink-0"></span>
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Baris 1: brand, judul halaman, periode, profil --}}
    <header class="h-16">
        <div class="flex items-center justify-between gap-3 h-full px-4 sm:px-6">
            <div class="flex items-center gap-3 min-w-0">
                <button class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100"
                        aria-label="Buka menu"
                        onclick="const d=document.getElementById('mobile-drawer'); const o=document.getElementById('menu-overlay'); d.classList.toggle('-translate-x-full'); o.classList.toggle('hidden');">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <a href="{{ route('dashboard') }}" class="hidden sm:flex items-center gap-2 text-gray-800 shrink-0">
                    <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'KasPro') }}"
                         class="w-8 h-8 rounded-lg bg-white object-contain p-0.5 ring-1 ring-gray-200" />
                    <span class="font-bold text-lg">{{ config('app.name', 'KasPro') }}</span>
                </a>
                <div class="hidden md:block min-w-0 border-l border-gray-200 ps-3">
                    <h1 class="text-lg font-bold text-gray-800 truncate">{{ $title ?? 'Dashboard' }}</h1>
                    @php $periode = \App\Services\PeriodeService::aktif(); @endphp
                    @if($periode)
                        <p class="text-xs text-gray-500 leading-tight">Periode: {{ $periode->label }}</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if(Auth::check())
                    <a href="{{ str_replace(url('/'), '', route('donasi.index')) }}"
                       class="inline-flex items-center gap-1.5 rounded-full bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold px-3 sm:px-4 py-2 transition shrink-0"
                       title="Dukung KasPro">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        <span class="hidden md:inline">Dukung KasPro</span>
                    </a>
                    <div x-data="notifBell()" @click.outside="open = false" class="relative">
                        <button @click="toggle()"
                                class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition"
                                aria-label="Notifikasi">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <template x-if="total > 0">
                                <span class="absolute -top-0.5 -right-0.5 min-w-5 h-5 px-1 rounded-full bg-red-500 text-white text-xs font-bold flex items-center justify-center" x-text="total > 99 ? '99+' : total"></span>
                            </template>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             class="absolute right-0 top-full mt-2 w-80 max-w-[calc(100vw-2rem)] rounded-lg bg-white border border-gray-200 shadow-lg overflow-hidden"
                             @click="open = false"
                             x-cloak>
                            <div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-800">Notifikasi</p>
                                <form method="POST" action="{{ route('notifikasi.baca-semua') }}" x-ref="markAllForm">
                                    @csrf
                                    <button type="submit" class="text-xs font-medium text-emerald-700 hover:text-emerald-800">Tandai semua dibaca</button>
                                </form>
                            </div>
                            <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                                <template x-if="items.length === 0">
                                    <p class="px-4 py-8 text-center text-sm text-gray-400">Belum ada notifikasi.</p>
                                </template>
                                <template x-for="notif in items" :key="notif.id">
                                    <a :href="'{{ route('notifikasi.index') }}'"
                                       class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition"
                                       :class="notif.read ? '' : 'bg-emerald-50/40'">
                                        <span class="w-2 h-2 rounded-full bg-emerald-600 mt-1.5 shrink-0" x-show="! notif.read"></span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-800 truncate" x-text="notif.title"></p>
                                            <p class="text-sm text-gray-600 truncate" x-show="notif.body" x-text="notif.body"></p>
                                            <p class="text-xs text-gray-400 mt-0.5" x-text="notif.waktu"></p>
                                        </div>
                                    </a>
                                </template>
                            </div>
                            <a href="{{ route('notifikasi.index') }}" class="block text-center text-sm font-medium text-emerald-700 hover:bg-emerald-50 py-2.5 border-t border-gray-100">
                                Lihat semua
                            </a>
                        </div>
                    </div>

                    <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                        <button @click="open = ! open"
                                class="flex items-center gap-2 rounded-full p-1 hover:ring-2 hover:ring-emerald-200 transition"
                                aria-label="Menu pengguna">
                            <span class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold shadow-sm">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $paket === 'pro' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $paket === 'pro' ? 'Pro' : 'Gratis' }}</span>
                            <svg class="hidden sm:block w-4 h-4 text-gray-400" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 top-full mt-2 w-56 rounded-lg bg-white border border-gray-200 shadow-lg py-1.5"
                             @click="open = false"
                             x-cloak>
                            <div class="px-4 py-2.5 border-b border-gray-100">
                                <div class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
                                <span class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $paket === 'pro' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $paket === 'pro' ? 'Pro' : 'Gratis' }}</span>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </header>

    {{-- Baris 2: kategori menu (desktop) --}}
    <nav class="hidden lg:flex flex-wrap items-center gap-x-1 px-4 sm:px-6 border-t border-gray-100" aria-label="Navigasi utama">
        @foreach($nav as $group)
            @php
                $single = count($group['items']) === 1;
                $first = $group['items'][0];
                $groupActive = collect($group['items'])->contains(function ($i) {
                    if (isset($i['children'])) {
                        return collect($i['children'])->contains(fn($c) => request()->routeIs($c['route'] . '*'));
                    }
                    return request()->routeIs($i['route'] . '*');
                });
                $alignRight = $loop->index >= max(0, count($nav) - 2);
            @endphp

            @if($single)
                <a href="{{ route($first['route']) }}"
                   class="px-3 py-3 text-sm font-medium transition border-b-2 whitespace-nowrap {{ $groupActive ? 'border-emerald-600 text-emerald-700 font-semibold' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300' }}">
                    {{ $group['label'] }}
                </a>
            @else
                <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                    <button @click="open = ! open"
                            class="flex items-center gap-1.5 px-3 py-3 text-sm font-medium transition border-b-2 whitespace-nowrap {{ $groupActive ? 'border-emerald-600 text-emerald-700 font-semibold' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300' }}">
                        {{ $group['label'] }}
                        <svg class="w-4 h-4" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 -translate-y-1"
                         x-transition:enter-end="transform opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-75"
                         class="absolute top-full z-50 mt-1 min-w-60 max-w-[calc(100vw-2rem)] rounded-lg bg-white border border-gray-200 shadow-lg py-1.5 {{ $alignRight ? 'right-0' : 'left-0' }}"
                         @click="open = false"
                         x-cloak>
                        @foreach($group['items'] as $item)
                            @if(isset($item['children']))
                                @php
                                    $activeItem = collect($item['children'])->contains(fn($c) => request()->routeIs($c['route'] . '*'));
                                @endphp
                                <div x-data="{ sub: {{ $activeItem ? 'true' : 'false' }} }">
                                    <button type="button" @click.stop="sub = ! sub"
                                            class="w-full flex items-center justify-between gap-2 px-4 py-2 text-sm transition {{ $activeItem ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        <span class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                                            {{ $item['label'] }}
                                        </span>
                                        <svg class="w-3.5 h-3.5 shrink-0 transition-transform" :class="sub ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="sub" x-transition x-cloak class="pb-1">
                                        @foreach($item['children'] as $child)
                                            @php $activeChild = request()->routeIs($child['route'] . '*'); @endphp
                                            <a href="{{ route($child['route']) }}"
                                               class="flex items-center gap-2.5 pl-9 pr-4 py-2 text-sm transition {{ $activeChild ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span>
                                                {{ $child['label'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                @php $activeItem = request()->routeIs($item['route'] . '*'); @endphp
                                <a href="{{ route($item['route']) }}"
                                   class="flex items-center gap-2.5 px-4 py-2 text-sm transition {{ $activeItem ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
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
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('notifBell', () => ({
        open: false,
        total: 0,
        items: [],
        init() {
            this.sync();
            setInterval(() => this.sync(), 60000);
        },
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.sync();
            }
        },
        async sync() {
            try {
                const r = await fetch('{{ str_replace(url('/'), '', route('notifikasi.recent')) }}');
                const j = await r.json();
                this.total = j.unread ?? 0;
                this.items = (j.items ?? []).map((n) => ({
                    ...n,
                    waktu: this.waktuRelatif(n.created_at),
                }));
            } catch (e) {}
        },
        waktuRelatif(iso) {
            if (!iso) { return ''; }
            const t = new Date(iso);
            const d = (Date.now() - t.getTime()) / 1000;
            if (d < 60) { return 'baru saja'; }
            if (d < 3600) { return Math.floor(d / 60) + ' menit lalu'; }
            if (d < 86400) { return Math.floor(d / 3600) + ' jam lalu'; }
            return Math.floor(d / 86400) + ' hari lalu';
        },
    }));
});
</script>