<x-app-layout>
    <x-slot name="title">Laporan Laba Rugi</x-slot>

    <x-page-header title="Laporan Laba Rugi" subtitle="Pendapatan dan beban dalam periode">
        <div class="flex gap-2">
            <a href="{{ route('laporan.laba-rugi.excel', ['dari' => $dari, 'sampai' => $sampai]) }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Excel
            </a>
            <a href="{{ route('laporan.laba-rugi.csv', ['dari' => $dari, 'sampai' => $sampai]) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                CSV
            </a>
            <a href="{{ route('laporan.laba-rugi.pdf', ['dari' => $dari, 'sampai' => $sampai]) }}" target="_blank" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                PDF
            </a>
        </div>
    </x-page-header>

    <x-card>
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex flex-wrap gap-2 items-end">
                <div>
                    <x-input-label for="dari" value="Dari" />
                    <input type="date" id="dari" name="dari" value="{{ $dari }}" class="mt-1 border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <x-input-label for="sampai" value="Sampai" />
                    <input type="date" id="sampai" name="sampai" value="{{ $sampai }}" class="mt-1 border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <button class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm rounded-lg">Tampilkan</button>
            </form>
        </div>

        <div class="p-5 max-w-2xl">
            <h3 class="font-bold text-xl text-gray-800 mb-1">{{ setting('nama_perusahaan', 'Perusahaan') }}</h3>
            <p class="text-sm text-gray-500 mb-4">Laba Rugi: {{ formatTanggalSingkat($dari) }} - {{ formatTanggalSingkat($sampai) }}</p>

            <h4 class="font-semibold text-gray-700 text-sm mb-2 uppercase tracking-wide">Pendapatan</h4>
            <div class="space-y-1.5">
                @forelse($data['pendapatan'] as $p)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-700">{{ $p['akun']->nama }}</span>
                        <span class="font-medium text-emerald-600">{{ formatRupiah($p['saldo']) }}</span>
                    </div>
                @empty
                    <div class="flex justify-between text-sm text-gray-400"><span>Belum ada pendapatan</span><span>-</span></div>
                @endforelse
            </div>
            <div class="flex justify-between mt-2 border-t border-gray-200 pt-2 font-semibold">
                <span class="text-gray-800">Total Pendapatan</span>
                <span class="text-emerald-700">{{ formatRupiah($data['total_pendapatan']) }}</span>
            </div>

            <h4 class="font-semibold text-gray-700 text-sm mb-2 mt-6 uppercase tracking-wide">Beban</h4>
            <div class="space-y-1.5">
                @forelse($data['beban'] as $b)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-700">{{ $b['akun']->nama }}</span>
                        <span class="font-medium text-red-600">{{ formatRupiah($b['saldo']) }}</span>
                    </div>
                @empty
                    <div class="flex justify-between text-sm text-gray-400"><span>Belum ada beban</span><span>-</span></div>
                @endforelse
            </div>
            <div class="flex justify-between mt-2 border-t border-gray-200 pt-2 font-semibold">
                <span class="text-gray-800">Total Beban</span>
                <span class="text-red-700">{{ formatRupiah($data['total_beban']) }}</span>
            </div>

            <div class="flex justify-between items-center mt-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                <span class="font-bold text-gray-800">Laba / Rugi Bersih</span>
                <span class="font-bold text-xl {{ $data['laba_rugi'] >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ formatRupiah($data['laba_rugi']) }}</span>
            </div>
        </div>
    </x-card>
</x-app-layout>
