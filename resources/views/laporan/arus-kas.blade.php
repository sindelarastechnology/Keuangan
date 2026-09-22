<x-app-layout>
    <x-slot name="title">Laporan Arus Kas</x-slot>

    <x-page-header title="Laporan Arus Kas" subtitle="Cash Flow Statement">
        <div class="flex gap-2">
            <a href="{{ route('laporan.arus-kas.excel', ['dari' => $dari, 'sampai' => $sampai]) }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Excel
            </a>
            <a href="{{ route('laporan.arus-kas.csv', ['dari' => $dari, 'sampai' => $sampai]) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                CSV
            </a>
            <a href="{{ route('laporan.arus-kas.pdf', ['dari' => $dari, 'sampai' => $sampai]) }}" target="_blank" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
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
            <p class="text-sm text-gray-500 mb-4">Arus Kas: {{ formatTanggalSingkat($dari) }} - {{ formatTanggalSingkat($sampai) }}</p>

            <div class="space-y-1.5 text-sm">
                <div class="flex justify-between font-semibold text-gray-800 border-b border-gray-200 pb-2">
                    <span>Kas Awal Periode</span>
                    <span>{{ formatRupiah($data['kas_awal']) }}</span>
                </div>
            </div>

            <h4 class="font-semibold text-gray-700 text-sm mb-2 mt-4 uppercase tracking-wide">Arus Kas dari Aktivitas Operasional</h4>
            <div class="space-y-1.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-700 pl-3">Laba Rugi</span>
                    <span class="font-medium {{ $data['laba_rugi'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ formatRupiah($data['laba_rugi']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700 pl-3">Penyusutan (Non-Kas)</span>
                    <span class="font-medium text-emerald-600">{{ formatRupiah($data['penyusutan']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700 pl-3">Perubahan Piutang</span>
                    <span class="font-medium {{ $data['perubahan_piutang'] <= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ formatRupiah(-$data['perubahan_piutang']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700 pl-3">Perubahan Persediaan</span>
                    <span class="font-medium {{ $data['perubahan_persediaan'] <= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ formatRupiah(-$data['perubahan_persediaan']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700 pl-3">Perubahan Hutang</span>
                    <span class="font-medium {{ $data['perubahan_hutang'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ formatRupiah($data['perubahan_hutang']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700 pl-3">Perubahan PPN Masukan</span>
                    <span class="font-medium {{ $data['perubahan_ppn_masukan'] <= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ formatRupiah(-$data['perubahan_ppn_masukan']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700 pl-3">Perubahan PPN Keluaran</span>
                    <span class="font-medium {{ $data['perubahan_ppn_keluaran'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ formatRupiah($data['perubahan_ppn_keluaran']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700 pl-3">{{ $data['laba_disposisi'] >= 0 ? 'Laba' : 'Rugi' }} Penjualan Aset</span>
                    <span class="font-medium {{ $data['laba_disposisi'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ formatRupiah(-$data['laba_disposisi']) }}</span>
                </div>
                <div class="flex justify-between mt-2 border-t border-gray-200 pt-2 font-semibold">
                    <span class="text-gray-800">Kas dari Operasional</span>
                    <span class="{{ $data['kas_operasional'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">{{ formatRupiah($data['kas_operasional']) }}</span>
                </div>
            </div>

            <h4 class="font-semibold text-gray-700 text-sm mb-2 mt-4 uppercase tracking-wide">Arus Kas dari Aktivitas Investasi</h4>
            <div class="space-y-1.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-700 pl-3">Pembelian/Penjualan Aset Tetap</span>
                    <span class="font-medium {{ $data['kas_investasi'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ formatRupiah($data['kas_investasi']) }}</span>
                </div>
                <div class="flex justify-between mt-2 border-t border-gray-200 pt-2 font-semibold">
                    <span class="text-gray-800">Kas dari Investasi</span>
                    <span class="{{ $data['kas_investasi'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">{{ formatRupiah($data['kas_investasi']) }}</span>
                </div>
            </div>

            <h4 class="font-semibold text-gray-700 text-sm mb-2 mt-4 uppercase tracking-wide">Arus Kas dari Aktivitas Pendanaan</h4>
            <div class="space-y-1.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-700 pl-3">Perubahan Modal</span>
                    <span class="font-medium {{ $data['kas_pendanaan'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ formatRupiah($data['kas_pendanaan']) }}</span>
                </div>
                <div class="flex justify-between mt-2 border-t border-gray-200 pt-2 font-semibold">
                    <span class="text-gray-800">Kas dari Pendanaan</span>
                    <span class="{{ $data['kas_pendanaan'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">{{ formatRupiah($data['kas_pendanaan']) }}</span>
                </div>
            </div>

            <div class="mt-6 space-y-1.5 text-sm border-t-2 border-gray-300 pt-3">
                <div class="flex justify-between font-semibold">
                    <span class="text-gray-800">Perubahan Kas Bersih</span>
                    <span class="{{ $data['perubahan_kas'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">{{ formatRupiah($data['perubahan_kas']) }}</span>
                </div>
                <div class="flex justify-between font-bold text-base">
                    <span class="text-gray-900">Kas Akhir Periode</span>
                    <span class="text-slate-800">{{ formatRupiah($data['kas_akhir']) }}</span>
                </div>
                @if($data['selisih'] > 0.01)
                <div class="flex justify-between text-xs text-amber-600 mt-1">
                    <span>Selisih Rekonsiliasi</span>
                    <span>{{ formatRupiah($data['selisih']) }}</span>
                </div>
                @endif
            </div>
        </div>
    </x-card>
</x-app-layout>
