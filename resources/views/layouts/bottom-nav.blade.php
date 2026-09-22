<nav class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-white border-t border-gray-200">
    <div class="grid grid-cols-5 h-16">
        @php
            $menus = [
                ['label' => 'Dashboard', 'icon' => 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10', 'route' => 'dashboard', 'active' => request()->routeIs('dashboard')],
                ['label' => 'Kas Masuk', 'icon' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12', 'route' => 'kas-masuk.index', 'active' => request()->routeIs('kas-masuk*')],
                ['label' => 'Menu', 'icon' => 'M4 6h16M4 12h16M4 18h16', 'route' => null, 'active' => false, 'center' => true],
                ['label' => 'Kas Keluar', 'icon' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12', 'route' => 'kas-keluar.index', 'active' => request()->routeIs('kas-keluar*')],
                ['label' => 'Profil', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'route' => 'profile.edit', 'active' => request()->routeIs('profile*')],
            ];
        @endphp

        @foreach($menus as $menu)
            @if(!empty($menu['center']))
                <button class="flex flex-col items-center justify-center"
                        onclick="const d=document.getElementById('mobile-drawer'); const o=document.getElementById('menu-overlay'); d.classList.toggle('-translate-x-full'); o.classList.toggle('hidden');">
                    <div class="w-12 h-12 -mt-6 rounded-full bg-emerald-600 text-white flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu['icon'] }}"/></svg>
                    </div>
                    <span class="text-[10px] text-gray-600">{{ $menu['label'] }}</span>
                </button>
            @else
                <a href="{{ $menu['route'] === 'dashboard' ? route('dashboard') : route($menu['route']) }}"
                   class="flex flex-col items-center justify-center gap-0.5 {{ $menu['active'] ? 'text-emerald-600' : 'text-gray-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu['icon'] }}"/></svg>
                    <span class="text-[10px] font-medium">{{ $menu['label'] }}</span>
                </a>
            @endif
        @endforeach
    </div>
</nav>
