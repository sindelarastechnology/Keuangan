<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    @php
        $pctChange = function(float $now, float $lalu): ?float {
            if ($lalu == 0) return null;
            return (($now - $lalu) / $lalu) * 100;
        };
        $trenMax = collect($tren)->flatMap(fn($x) => [$x['penjualan'], $x['pembelian'], $x['masuk'], $x['keluar']])->max();
        $trenMax = $trenMax > 0 ? $trenMax : 1;
    @endphp

    {{-- ── Header & Filter ───────────────────────────────────────────── --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-end justify-between gap-4"
         x-data="dashboardFilter()">

        <div>
            <h2 class="text-lg font-bold text-gray-900">Selamat datang, {{ auth()->user()->name }} 👋</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                @if($periodeStatus['periode_aktif'])
                    Periode aktif: <span class="font-medium text-emerald-700">{{ $periodeStatus['label_aktif'] }}</span>
                    @if($periodeStatus['terkunci'])
                        <span class="ml-1 inline-flex items-center gap-1 text-amber-600 text-xs font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Dikunci
                        </span>
                    @endif
                @else
                    <span class="text-amber-600 font-medium">⚠ Tidak ada periode aktif</span>
                    — <a href="{{ route('periode.index') }}" class="underline text-emerald-600 hover:text-emerald-700">Buka periode</a>
                @endif
            </p>
        </div>

        {{-- Filter Preset + Rentang Tanggal --}}
        <form method="GET" class="flex flex-wrap items-end gap-2" @submit.prevent="submitFilter">
            <div class="flex rounded-lg border border-gray-300 overflow-hidden text-xs font-medium">
                @foreach([
                    'bulan_ini' => 'Bln Ini',
                    'bulan_lalu' => 'Bln Lalu',
                    'kuartal_ini' => 'Kuartal',
                    'tahun_ini' => 'Tahun Ini',
                ] as $key => $label)
                    <button type="submit" name="preset" value="{{ $key }}"
                        class="px-3 py-2 border-r border-gray-200 last:border-0 transition
                            {{ $preset === $key ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <div class="flex items-end gap-2">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Dari</label>
                    <input type="date" name="dari" value="{{ $dari }}" class="border-gray-300 rounded-lg text-sm h-9 focus:ring-emerald-500 focus:border-emerald-500 px-3">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Sampai</label>
                    <input type="date" name="sampai" value="{{ $sampai }}" class="border-gray-300 rounded-lg text-sm h-9 focus:ring-emerald-500 focus:border-emerald-500 px-3">
                </div>
                <button type="submit" class="h-9 px-4 bg-gray-700 hover:bg-gray-800 text-white text-sm rounded-lg transition">
                    Tampilkan
                </button>
            </div>
        </form>
    </div>

    {{-- ── Action Items (jika ada) ────────────────────────────────────── --}}
    @if($pendingApproval || $stokHabis > 0 || ! $periodeStatus['periode_aktif'])
        <div class="mb-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

            @if(! $periodeStatus['periode_aktif'])
                <a href="{{ route('periode.index') }}"
                   class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl p-4 hover:bg-amber-100 transition group">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center shrink-0 group-hover:bg-amber-200 transition">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-amber-800">Tidak ada periode aktif</div>
                        <div class="text-xs text-amber-600 mt-0.5">Buka periode akuntansi untuk memulai pencatatan</div>
                    </div>
                </a>
            @endif

            @if($stokHabis > 0)
                <a href="{{ route('barang.index', ['stok' => 'habis']) }}"
                   class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl p-4 hover:bg-red-100 transition group">
                    <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center shrink-0 group-hover:bg-red-200 transition">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-red-800">{{ $stokHabis }} barang stok habis</div>
                        <div class="text-xs text-red-600 mt-0.5">Segera lakukan pengadaan</div>
                    </div>
                </a>
            @endif

            @foreach($pendingApproval as $item)
                @php
                    $colors = [
                        'emerald' => ['bg-emerald-50', 'border-emerald-200', 'bg-emerald-100', 'text-emerald-600', 'text-emerald-800', 'text-emerald-600', 'hover:bg-emerald-100'],
                        'red' => ['bg-red-50', 'border-red-200', 'bg-red-100', 'text-red-600', 'text-red-800', 'text-red-600', 'hover:bg-red-100'],
                        'blue' => ['bg-blue-50', 'border-blue-200', 'bg-blue-100', 'text-blue-600', 'text-blue-800', 'text-blue-600', 'hover:bg-blue-100'],
                        'amber' => ['bg-amber-50', 'border-amber-200', 'bg-amber-100', 'text-amber-600', 'text-amber-800', 'text-amber-600', 'hover:bg-amber-100'],
                        'violet' => ['bg-violet-50', 'border-violet-200', 'bg-violet-100', 'text-violet-600', 'text-violet-800', 'text-violet-600', 'hover:bg-violet-100'],
                    ];
                    $c = $colors[$item['color']] ?? $colors['blue'];
                @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-start gap-3 {{ $c[0] }} border {{ $c[1] }} rounded-xl p-4 {{ $c[6] }} transition group">
                    <div class="w-9 h-9 rounded-lg {{ $c[2] }} flex items-center justify-center shrink-0 transition">
                        <svg class="w-5 h-5 {{ $c[3] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold {{ $c[4] }}">{{ $item['label'] }}</div>
                        <div class="text-xs {{ $c[5] }} mt-0.5">
                            <span class="font-bold">{{ $item['count'] }}</span> transaksi perlu ditinjau
                        </div>
                    </div>
                </a>
            @endforeach

        </div>
    @endif

    {{-- ── KPI Cards ───────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-3 mb-5">

        {{-- Kas & Bank --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-xs text-gray-500 font-medium">Kas & Bank</div>
                <div class="text-lg font-bold text-gray-900 truncate">{{ formatRupiah($saldoRekening) }}</div>
                <div class="text-xs text-gray-400 mt-0.5">Saldo seluruh rekening</div>
            </div>
        </div>

        {{-- Penjualan --}}
        @php $pctPenjualan = $pctChange($totalPenjualan, $totalPenjualanLalu); @endphp
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-xs text-gray-500 font-medium">Penjualan</div>
                <div class="text-lg font-bold text-gray-900 truncate">{{ formatRupiah($totalPenjualan) }}</div>
                @if($pctPenjualan !== null)
                    <div class="text-xs mt-0.5 {{ $pctPenjualan >= 0 ? 'text-emerald-600' : 'text-red-500' }} flex items-center gap-0.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($pctPenjualan >= 0)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            @endif
                        </svg>
                        {{ number_format(abs($pctPenjualan), 1) }}% vs bln lalu
                    </div>
                @else
                    <div class="text-xs text-gray-400 mt-0.5">Bln lalu: {{ formatRupiah($totalPenjualanLalu) }}</div>
                @endif
                @if($draftPenjualan['count'] > 0)
                    <a href="{{ route('penjualan.index') }}" class="mt-1 inline-flex items-center gap-1 text-[11px] font-medium text-amber-600 hover:text-amber-700">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        {{ $draftPenjualan['count'] }} draft penjualan · {{ formatRupiah($draftPenjualan['total']) }}
                    </a>
                @endif
            </div>
        </div>

        {{-- Pembelian --}}
        @php $pctPembelian = $pctChange($totalPembelian, $totalPembelianLalu); @endphp
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-xs text-gray-500 font-medium">Pembelian</div>
                <div class="text-lg font-bold text-gray-900 truncate">{{ formatRupiah($totalPembelian) }}</div>
                @if($pctPembelian !== null)
                    <div class="text-xs mt-0.5 {{ $pctPembelian <= 0 ? 'text-emerald-600' : 'text-red-500' }} flex items-center gap-0.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($pctPembelian <= 0)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            @endif
                        </svg>
                        {{ number_format(abs($pctPembelian), 1) }}% vs bln lalu
                    </div>
                @else
                    <div class="text-xs text-gray-400 mt-0.5">Bln lalu: {{ formatRupiah($totalPembelianLalu) }}</div>
                @endif
                @if($draftPembelian['count'] > 0)
                    <a href="{{ route('pembelian.index') }}" class="mt-1 inline-flex items-center gap-1 text-[11px] font-medium text-amber-600 hover:text-amber-700">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        {{ $draftPembelian['count'] }} draft pembelian · {{ formatRupiah($draftPembelian['total']) }}
                    </a>
                @endif
            </div>
        </div>

        {{-- Piutang --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-violet-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-xs text-gray-500 font-medium">Total Piutang</div>
                <div class="text-lg font-bold text-gray-900 truncate">{{ formatRupiah($totalPiutang) }}</div>
                <div class="text-xs text-gray-400 mt-0.5">
                    <a href="{{ route('bb-piutang.index') }}" class="hover:text-violet-600 transition">Lihat detail →</a>
                </div>
            </div>
        </div>

        {{-- Hutang --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-xs text-gray-500 font-medium">Total Hutang</div>
                <div class="text-lg font-bold text-gray-900 truncate">{{ formatRupiah($totalHutang) }}</div>
                <div class="text-xs text-gray-400 mt-0.5">
                    <a href="{{ route('bb-hutang.index') }}" class="hover:text-amber-600 transition">Lihat detail →</a>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Baris 2: Tren + Laba Rugi ──────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

        {{-- Grafik Tren 6 Bulan --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm"
             x-data="{ metrik: 'penjualan_pembelian' }">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
                <h3 class="text-sm font-semibold text-gray-800">Tren 6 Bulan Terakhir</h3>
                <div class="flex rounded-lg border border-gray-200 overflow-hidden text-xs">
                    <button type="button" @click="metrik = 'penjualan_pembelian'"
                        :class="metrik === 'penjualan_pembelian' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                        class="px-3 py-1.5 border-r border-gray-200 transition">Penjualan vs Pembelian</button>
                    <button type="button" @click="metrik = 'kas'"
                        :class="metrik === 'kas' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                        class="px-3 py-1.5 transition">Kas Masuk vs Keluar</button>
                </div>
            </div>
            <div class="p-5">
                @php
                    $trenJson = json_encode($tren);
                    $trenMaxVal = $trenMax;
                @endphp
                <div x-data="trenGrafik(@js($tren), {{ $trenMaxVal }})">
                    {{-- Legenda --}}
                    <div class="flex gap-4 mb-4 text-xs text-gray-500">
                        <template x-if="metrik === 'penjualan_pembelian'">
                            <div class="flex gap-4">
                                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-blue-500"></span>Penjualan</span>
                                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-orange-400"></span>Pembelian</span>
                            </div>
                        </template>
                        <template x-if="metrik === 'kas'">
                            <div class="flex gap-4">
                                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-500"></span>Kas Masuk</span>
                                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-400"></span>Kas Keluar</span>
                            </div>
                        </template>
                    </div>
                    {{-- Bars --}}
                    <div class="flex items-end justify-between gap-2" style="height: 160px;">
                        <template x-for="(item, i) in data" :key="i">
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <div class="w-full flex justify-center items-end gap-0.5" style="height:140px">
                                    {{-- Bar 1 --}}
                                    <div class="w-5 sm:w-6 rounded-t transition-all duration-500 cursor-pointer relative group"
                                         :class="metrik === 'penjualan_pembelian' ? 'bg-blue-500 hover:bg-blue-600' : 'bg-emerald-500 hover:bg-emerald-600'"
                                         :style="'height:' + barHeight(metrik === 'penjualan_pembelian' ? item.penjualan : item.masuk) + 'px'">
                                        <div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs rounded px-2 py-1 whitespace-nowrap opacity-0 group-hover:opacity-100 transition pointer-events-none z-10"
                                             x-text="formatRp(metrik === 'penjualan_pembelian' ? item.penjualan : item.masuk)"></div>
                                    </div>
                                    {{-- Bar 2 --}}
                                    <div class="w-5 sm:w-6 rounded-t transition-all duration-500 cursor-pointer relative group"
                                         :class="metrik === 'penjualan_pembelian' ? 'bg-orange-400 hover:bg-orange-500' : 'bg-red-400 hover:bg-red-500'"
                                         :style="'height:' + barHeight(metrik === 'penjualan_pembelian' ? item.pembelian : item.keluar) + 'px'">
                                        <div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs rounded px-2 py-1 whitespace-nowrap opacity-0 group-hover:opacity-100 transition pointer-events-none z-10"
                                             x-text="formatRp(metrik === 'penjualan_pembelian' ? item.pembelian : item.keluar)"></div>
                                    </div>
                                </div>
                                <span class="text-[10px] text-gray-400" x-text="item.bulan"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- Laba Rugi --}}
        @if($labaRugi)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-800">Laba / Rugi</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($dari)->format('d M') }} – {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</p>
                </div>
                <div class="p-5 space-y-3">
                    {{-- Pendapatan bar --}}
                    @php
                        $totalPend = $labaRugi['total_pendapatan'];
                        $totalBeban = $labaRugi['total_beban'];
                        $laba = $labaRugi['laba_rugi'];
                        $barMax = max($totalPend, $totalBeban, 1);
                    @endphp
                    <div>
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Pendapatan</span>
                            <span class="font-semibold text-emerald-700">{{ formatRupiah($totalPend) }}</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all duration-700"
                                 style="width: {{ min(100, ($totalPend / $barMax) * 100) }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Beban</span>
                            <span class="font-semibold text-red-600">{{ formatRupiah($totalBeban) }}</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-red-400 rounded-full transition-all duration-700"
                                 style="width: {{ min(100, ($totalBeban / $barMax) * 100) }}%"></div>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-700">Laba Bersih</span>
                            <span class="text-base font-bold {{ $laba >= 0 ? 'text-emerald-700' : 'text-red-600' }}">
                                {{ formatRupiah($laba) }}
                            </span>
                        </div>
                        @if($totalPend > 0)
                            <div class="text-xs text-gray-400 mt-1">
                                Margin: {{ number_format(($laba / $totalPend) * 100, 1) }}%
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('laporan.laba-rugi') }}" class="block text-center text-xs text-emerald-600 hover:text-emerald-700 font-medium py-1">
                        Lihat Laporan Lengkap →
                    </a>
                </div>
            </div>
        @else
            {{-- Kas Masuk vs Keluar ringkasan --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-800">Kas Periode Ini</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-8 rounded-full bg-emerald-500"></div>
                            <div>
                                <div class="text-xs text-gray-500">Kas Masuk</div>
                                <div class="text-sm font-bold text-gray-800">{{ formatRupiah($kasMasukTotal) }}</div>
                            </div>
                        </div>
                        @php $pctMasuk = $pctChange($kasMasukTotal, $kasMasukLalu); @endphp
                        @if($pctMasuk !== null)
                            <span class="text-xs {{ $pctMasuk >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $pctMasuk >= 0 ? '+' : '' }}{{ number_format($pctMasuk, 1) }}%
                            </span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-8 rounded-full bg-red-400"></div>
                            <div>
                                <div class="text-xs text-gray-500">Kas Keluar</div>
                                <div class="text-sm font-bold text-gray-800">{{ formatRupiah($kasKeluarTotal) }}</div>
                            </div>
                        </div>
                        @php $pctKeluar = $pctChange($kasKeluarTotal, $kasKeluarLalu); @endphp
                        @if($pctKeluar !== null)
                            <span class="text-xs {{ $pctKeluar <= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $pctKeluar >= 0 ? '+' : '' }}{{ number_format($pctKeluar, 1) }}%
                            </span>
                        @endif
                    </div>
                    @php $netKas = $kasMasukTotal - $kasKeluarTotal; @endphp
                    <div class="pt-3 border-t border-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-700">Net Kas</span>
                            <span class="text-base font-bold {{ $netKas >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ formatRupiah($netKas) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- ── Baris 3: Top Barang + Stok Kritis + Piutang/Hutang ─────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

        {{-- Top 5 Barang Terlaku --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800">Top 5 Barang Terlaku</h3>
                <span class="text-xs text-gray-400">Periode ini</span>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($topBarang as $i => $b)
                    @php $maxQty = $topBarang->first()->total_qty ?: 1; @endphp
                    <div class="px-5 py-3">
                        <div class="flex items-start justify-between gap-2 mb-1.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-5 h-5 rounded-full bg-gray-100 text-gray-500 text-[10px] font-bold flex items-center justify-center shrink-0">{{ $i + 1 }}</span>
                                <span class="text-sm text-gray-800 font-medium truncate">{{ $b->nama }}</span>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-xs font-semibold text-gray-700">{{ formatKuantitas($b->total_qty) }} <span class="text-gray-400 font-normal">{{ $b->satuan }}</span></div>
                                <div class="text-[10px] text-gray-400">{{ formatRupiah($b->total_nilai) }}</div>
                            </div>
                        </div>
                        <div class="h-1 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-400 rounded-full"
                                 style="width: {{ min(100, ($b->total_qty / $maxQty) * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-400">Belum ada data penjualan pada periode ini.</div>
                @endforelse
            </div>
        </div>

        {{-- Stok Kritis --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800">Stok Kritis</h3>
                @if($stokHabis > 0)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-100 text-red-700 text-xs rounded-full font-medium">
                        {{ $stokHabis }} habis
                    </span>
                @endif
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($stokKritis as $b)
                    @php
                        $pct = $b->min_stok > 0 ? min(100, ($b->stok / $b->min_stok) * 100) : 0;
                        $warnColor = $b->stok <= 0 ? ['bg-red-100', 'text-red-700', 'bg-red-500'] : ($pct < 50 ? ['bg-orange-100', 'text-orange-700', 'bg-orange-400'] : ['bg-amber-100', 'text-amber-700', 'bg-amber-400']);
                    @endphp
                    <div class="px-5 py-3">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-2 h-2 rounded-full {{ $warnColor[2] }} shrink-0"></span>
                                <span class="text-sm text-gray-800 font-medium truncate">{{ $b->nama }}</span>
                            </div>
                            <span class="text-xs font-semibold {{ $warnColor[1] }} shrink-0">
                                {{ formatKuantitas($b->stok) }}/{{ formatKuantitas($b->min_stok) }} {{ $b->satuan }}
                            </span>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full {{ $warnColor[2] }} rounded-full transition-all"
                                 style="width: {{ max(2, $pct) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Semua stok aman.
                    </div>
                @endforelse
                @if($stokKritis->count() > 0)
                    <div class="px-5 py-3">
                        <a href="{{ route('barang.index', ['stok' => 'rendah']) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">
                            Lihat semua barang stok rendah →
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Piutang & Hutang Terbesar --}}
        <div class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm"
             x-data="{ tab: 'piutang' }">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-2">
                <h3 class="text-sm font-semibold text-gray-800">Piutang & Hutang</h3>
                <div class="flex rounded-lg border border-gray-200 overflow-hidden text-xs">
                    <button type="button" @click="tab = 'piutang'"
                        :class="tab === 'piutang' ? 'bg-violet-600 text-white' : 'bg-white text-gray-600'"
                        class="px-3 py-1.5 border-r border-gray-200 transition">Piutang</button>
                    <button type="button" @click="tab = 'hutang'"
                        :class="tab === 'hutang' ? 'bg-amber-500 text-white' : 'bg-white text-gray-600'"
                        class="px-3 py-1.5 transition">Hutang</button>
                </div>
            </div>
            {{-- Piutang --}}
            <div x-show="tab === 'piutang'" class="divide-y divide-gray-50">
                @forelse($topPiutang as $p)
                    @php $maxPiutang = $topPiutang->first()->saldo ?: 1; @endphp
                    <div class="px-5 py-3">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <span class="text-sm text-gray-800 font-medium truncate">{{ $p->nama }}</span>
                            <span class="text-xs font-semibold text-violet-700 shrink-0">{{ formatRupiah($p->saldo) }}</span>
                        </div>
                        <div class="h-1 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-violet-400 rounded-full"
                                 style="width: {{ min(100, ($p->saldo / $maxPiutang) * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-400">Tidak ada piutang aktif.</div>
                @endforelse
                @if($topPiutang->count())
                    <div class="px-5 py-3">
                        <a href="{{ route('bb-piutang.index') }}" class="text-xs text-violet-600 hover:text-violet-700 font-medium">Lihat semua →</a>
                    </div>
                @endif
            </div>
            {{-- Hutang --}}
            <div x-show="tab === 'hutang'" x-cloak class="divide-y divide-gray-50">
                @forelse($topHutang as $h)
                    @php $maxHutang = $topHutang->first()->saldo ?: 1; @endphp
                    <div class="px-5 py-3">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <span class="text-sm text-gray-800 font-medium truncate">{{ $h->nama }}</span>
                            <span class="text-xs font-semibold text-amber-700 shrink-0">{{ formatRupiah($h->saldo) }}</span>
                        </div>
                        <div class="h-1 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-400 rounded-full"
                                 style="width: {{ min(100, ($h->saldo / $maxHutang) * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-400">Tidak ada hutang aktif.</div>
                @endforelse
                @if($topHutang->count())
                    <div class="px-5 py-3">
                        <a href="{{ route('bb-hutang.index') }}" class="text-xs text-amber-600 hover:text-amber-700 font-medium">Lihat semua →</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kredit Jatuh Tempo --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800">Kredit Jatuh Tempo</h3>
                <a href="{{ route('tagihan.hutang') }}" class="text-xs text-amber-600 hover:text-amber-700 font-medium">Bayar →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($pembelianJatuhTempo as $k)
                    @php $maxK = $pembelianJatuhTempo->first()->saldo ?: 1; @endphp
                    <div class="px-5 py-3">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <span class="text-sm text-gray-800 font-medium truncate">{{ $k->nomor }} · {{ $k->supplier_nama }}</span>
                            <span class="text-xs font-semibold text-amber-700 shrink-0">{{ formatRupiah($k->saldo) }}</span>
                        </div>
                        <div class="h-1 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-400 rounded-full"
                                 style="width: {{ min(100, ($k->saldo / $maxK) * 100) }}%"></div>
                        </div>
                        <div class="text-[10px] text-gray-400 mt-1">Jatuh tempo {{ \Carbon\Carbon::parse($k->tanggal)->format('d M Y') }} · {{ formatRupiah($k->total) }}</div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-400">Tidak ada kredit jatuh tempo.</div>
                @endforelse
            </div>
        </div>
        </div>

    </div>

    {{-- ── Baris 4: Transaksi Terakhir + Info Aset/Akuntansi ─────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Penjualan Terakhir --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800">Penjualan Terakhir</h3>
                <a href="{{ route('penjualan.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Semua →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($penjualanTerakhir as $p)
                    <a href="{{ route('penjualan.show', $p) }}" class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition gap-3">
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-blue-600 truncate">{{ $p->nomor }}</div>
                            <div class="text-xs text-gray-400">{{ $p->customer?->nama ?? '-' }} · {{ \Carbon\Carbon::parse($p->tanggal)->format('d M') }}</div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-sm font-semibold text-gray-800">{{ formatRupiah($p->total) }}</div>
                            <span class="text-[10px] {{ $p->metode_bayar === 'tunai' ? 'text-emerald-600' : 'text-blue-500' }}">
                                {{ $p->metode_bayar === 'tunai' ? 'Tunai' : 'Kredit' }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-400">Belum ada penjualan.</div>
                @endforelse
            </div>
        </div>

        {{-- Pembelian Terakhir --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800">Pembelian Terakhir</h3>
                <a href="{{ route('pembelian.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Semua →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($pembelianTerakhir as $p)
                    <a href="{{ route('pembelian.show', $p) }}" class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition gap-3">
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-orange-600 truncate">{{ $p->nomor }}</div>
                            <div class="text-xs text-gray-400">{{ $p->supplier?->nama ?? '-' }} · {{ \Carbon\Carbon::parse($p->tanggal)->format('d M') }}</div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-sm font-semibold text-gray-800">{{ formatRupiah($p->total) }}</div>
                            <span class="text-[10px] {{ $p->metode_bayar === 'tunai' ? 'text-emerald-600' : 'text-orange-500' }}">
                                {{ $p->metode_bayar === 'tunai' ? 'Tunai' : 'Kredit' }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-400">Belum ada pembelian.</div>
                @endforelse
            </div>
        </div>

        {{-- Info Keuangan: Aset + Periode + Jurnal --}}
        <div class="space-y-4">

            {{-- Aset Tetap Summary --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <h3 class="text-sm font-semibold text-gray-800">Aset Tetap</h3>
                        <a href="{{ route('aset.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700">Kelola →</a>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="text-xs text-gray-500">Aset Aktif</div>
                            <div class="text-lg font-bold text-gray-800 mt-0.5">{{ $jumlahAsetAktif }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="text-xs text-gray-500">Penyusutan Bln Ini</div>
                            <div class="text-base font-bold text-gray-800 mt-0.5">{{ formatRupiah($penyusutanBulanIni) }}</div>
                        </div>
                    </div>
                    @if($penyusutanBulanIni == 0 && $jumlahAsetAktif > 0)
                        <div class="mt-3 flex items-center gap-2 text-xs text-amber-600 bg-amber-50 rounded-lg px-3 py-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Penyusutan bulan ini belum diproses
                            <a href="{{ route('penyusutan.index') }}" class="ml-auto font-medium underline">Proses</a>
                        </div>
                    @endif
                </div>

            {{-- Jurnal Draft --}}
            @if($jurnalDraft > 0)
                <a href="{{ route('jurnal.index') }}"
                   class="flex items-center gap-3 bg-white rounded-xl border border-amber-200 shadow-sm p-4 hover:bg-amber-50 transition">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-amber-800">{{ $jurnalDraft }} Jurnal Draft</div>
                        <div class="text-xs text-amber-600">Belum diposting — klik untuk tinjau</div>
                    </div>
                </a>
            @endif

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Aksi Cepat</h3>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('penjualan.create') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-medium transition text-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Penjualan Baru
                    </a>
                    <a href="{{ route('pembelian.create') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg bg-orange-50 hover:bg-orange-100 text-orange-700 text-xs font-medium transition text-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Pembelian Baru
                    </a>
                    <a href="{{ route('kas-masuk.create') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-medium transition text-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Kas Masuk
                    </a>
                    <a href="{{ route('kas-keluar.create') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium transition text-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Kas Keluar
                    </a>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function dashboardFilter() {
            return {
                submitFilter() {
                    this.$el.submit();
                }
            };
        }

        function trenGrafik(data, maxVal) {
            return {
                data: data,
                maxVal: maxVal,
                barHeight(val) {
                    if (this.maxVal <= 0) return 2;
                    return Math.max(2, Math.round((val / this.maxVal) * 140));
                },
                formatRp(val) {
                    if (val >= 1_000_000_000) return 'Rp ' + (val / 1_000_000_000).toFixed(1) + 'M';
                    if (val >= 1_000_000) return 'Rp ' + (val / 1_000_000).toFixed(1) + 'jt';
                    if (val >= 1_000) return 'Rp ' + (val / 1_000).toFixed(0) + 'rb';
                    return 'Rp ' + val.toFixed(0);
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
