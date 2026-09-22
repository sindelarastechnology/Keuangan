<x-app-layout>
    <x-slot name="title">Detail Penjualan</x-slot>

    <x-page-header title="Detail Penjualan" :subtitle="$penjualan->nomor">
        <div class="flex items-center gap-2">
            @if($penjualan->status === 'pending')
                <a href="{{ route('penjualan.edit', $penjualan) }}" class="inline-flex items-center gap-2 text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 px-3 py-2 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Draft
                </a>
                <form method="POST" action="{{ route('penjualan.post', $penjualan) }}">
                    @csrf
                    <x-button class="inline-flex items-center gap-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 px-3 py-2 rounded-lg transition">Posting Transaksi</x-button>
                </form>
                <form method="POST" action="{{ route('penjualan.void', $penjualan) }}">
                    @csrf
                    <button type="button" class="inline-flex items-center gap-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 px-3 py-2 rounded-lg transition"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'batalkan-penjualan-draft' }))">Batalkan Draft</button>
                    <x-confirm-dialog name="batalkan-penjualan-draft" title="Batalkan Draft Penjualan"
                                      message="Yakin ingin membatalkan draft penjualan '{{ $penjualan->nomor }}'? Draft tidak memengaruhi stok & jurnal, dan tindakan ini tidak dapat dibatalkan." />
                </form>
            @endif
            @if($penjualan->status === 'posted')
            <a href="{{ route('penjualan.pdf', $penjualan) }}" class="inline-flex items-center gap-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 px-3 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Invoice
            </a>
            @endif
            <a href="{{ route('penjualan.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </a>
        </div>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <x-card>
            <div class="p-5">
                <h3 class="font-bold text-lg text-emerald-600 mb-4">{{ formatRupiah($penjualan->total) }}</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Tanggal</dt><dd class="font-medium">{{ formatTanggal($penjualan->tanggal) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Customer</dt><dd class="font-medium">{{ $penjualan->customer->nama }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Gudang</dt><dd class="font-medium">{{ $penjualan->gudang?->nama ?? '—' }}</dd></div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Metode Bayar</dt>
                        <dd><span class="inline-flex px-2 py-1 text-xs rounded-full {{ $penjualan->metode_bayar === 'tunai' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($penjualan->metode_bayar) }}</span></dd>
                    </div>
                    @if($penjualan->metode_bayar === 'tunai')
                    <div class="flex justify-between"><dt class="text-gray-500">Diterima Melalui</dt><dd class="font-medium">{{ $penjualan->rekening?->nama ?? '—' }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-gray-500">HPP Total</dt><dd class="font-medium">{{ formatRupiah($penjualan->hpp_total) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Laba Kotor</dt><dd class="font-medium {{ ($penjualan->subtotal - $penjualan->diskon_nominal - $penjualan->hpp_total) >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ formatRupiah($penjualan->subtotal - $penjualan->diskon_nominal - $penjualan->hpp_total) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Keterangan</dt><dd class="font-medium text-right">{{ $penjualan->keterangan ?? '-' }}</dd></div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            @if($penjualan->status === 'posted')
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">Posted</span>
                            @elseif($penjualan->status === 'pending')
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">Draft</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-200 text-gray-500">Batal</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </x-card>

        <x-card title="Jurnal Terkait">
            @if($penjualan->jurnal)
                <div class="p-5">
                    @foreach($penjualan->jurnal as $j)
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 mb-2">Jurnal: {{ $j->nomor }} ({{ $j->tipe_label }})</p>
                            <table class="w-full text-sm">
                                <thead><tr class="text-left text-xs text-gray-500"><th class="py-1">Akun</th><th class="py-1 text-right">Debit</th><th class="py-1 text-right">Kredit</th></tr></thead>
                                <tbody>
                                    @foreach($j->items as $item)
                                        <tr class="border-t border-gray-100">
                                            <td class="py-2">{{ $item->akun->nama }}</td>
                                            <td class="py-2 text-right">{{ $item->debit > 0 ? formatRupiah($item->debit) : '-' }}</td>
                                            <td class="py-2 text-right">{{ $item->kredit > 0 ? formatRupiah($item->kredit) : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="p-5 text-sm text-gray-500">Tidak ada jurnal terkait.</p>
            @endif
        </x-card>
    </div>

    @if($penjualan->status === 'posted' && $penjualan->metode_bayar === 'kredit')
    <x-card title="Status Piutang" class="mb-4">
        <div class="p-5">
            @php
                $sisa = (float) $penjualan->sisa_piutang;
                $sisaTampil = max(0, $sisa);
                $dibayar = max(0, (float) $penjualan->total - $sisa);
            @endphp

            @if($penjualan->piutang_terverifikasi)
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm text-gray-500">Status</span>
                    @if($sisaTampil <= 0.005)
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">Lunas</span>
                    @else
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">Belum Lunas</span>
                    @endif
                </div>

                <dl class="space-y-3 text-sm mb-4">
                    <div class="flex justify-between"><dt class="text-gray-500">Tagihan</dt><dd class="font-medium">{{ formatRupiah($penjualan->total) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Sudah Dibayar</dt><dd class="font-medium text-emerald-600">{{ formatRupiah(min((float) $penjualan->total, $dibayar)) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Sisa</dt><dd class="font-medium {{ $sisaTampil > 0.005 ? 'text-red-600' : '' }}">{{ formatRupiah($sisaTampil) }}</dd></div>
                </dl>

                @if($sisaTampil > 0.005)
                    <form method="POST" action="{{ route('penjualan.pelunasan', $penjualan) }}" class="space-y-3 border-t border-gray-100 pt-4">
                        @csrf
                        <div>
                            <label for="rekening_id" class="block text-sm text-gray-600 mb-1">Terima Melalui</label>
                            <select id="rekening_id" name="rekening_id" required class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                @foreach($rekeningList as $rek)
                                    <option value="{{ $rek->id }}">{{ $rek->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="nominal" class="block text-sm text-gray-600 mb-1">Nominal Pelunasan</label>
                            <input id="nominal" name="nominal" type="number" step="0.01" min="0.01" value="{{ number_format($sisaTampil, 2, '.', '') }}" required class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label for="tanggal" class="block text-sm text-gray-600 mb-1">Tanggal</label>
                            <input id="tanggal" name="tanggal" type="date" value="{{ now()->toDateString() }}" required class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div class="flex items-center gap-2 pt-1">
                            <x-button class="inline-flex items-center gap-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 px-3 py-2 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Terima Pelunasan
                            </x-button>
                            <span class="text-xs text-gray-400">Pelunasan dicatat sebagai Kas Masuk dan dapat dibatalkan dari menu Kas Masuk.</span>
                        </div>
                    </form>
                @endif
            @else
                <div class="flex items-start gap-2 mb-2">
                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Butuh Verifikasi</span>
                </div>
                <p class="text-sm text-gray-600">
                    Transaksi ini tercatat sebelum fitur pelunasan per faktur tersedia, sehingga sisa tagihannya belum ter-atribusi ke faktur ini.
                    Saldo piutang tetap terpantau melalui saldo customer di <b>BB Piutang</b>. Gunakan menu <b>Kas Masuk</b> untuk mencatat pembayaran dari customer ini.
                </p>
            @endif
        </div>
    </x-card>
    @endif

    <x-card title="Rincian Penjualan">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Barang</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Harga Jual</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Diskon</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Subtotal</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">HPP</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($penjualan->items as $item)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $item->barang->nama }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ formatKuantitas($item->jumlah) }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ formatRupiah($item->harga_satuan) }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ formatRupiah($item->diskon) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800">{{ formatRupiah($item->subtotal) }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ formatRupiah($item->hpp_total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr><td colspan="4" class="px-4 py-3 text-right text-gray-600">Subtotal</td><td class="px-4 py-3 text-right font-medium">{{ formatRupiah($penjualan->subtotal) }}</td><td></td></tr>
                    @if($penjualan->diskon_nominal > 0)
                    <tr><td colspan="4" class="px-4 py-3 text-right text-gray-600">Diskon</td><td class="px-4 py-3 text-right text-red-500">- {{ formatRupiah($penjualan->diskon_nominal) }}</td><td></td></tr>
                    @endif
                    @if($penjualan->pajak_nominal > 0)
                    <tr><td colspan="4" class="px-4 py-3 text-right text-gray-600">Pajak</td><td class="px-4 py-3 text-right font-medium">{{ formatRupiah($penjualan->pajak_nominal) }}</td><td></td></tr>
                    @endif
                    @if((float) $penjualan->ongkir > 0)
                    <tr><td colspan="4" class="px-4 py-3 text-right text-gray-600">Ongkir</td><td class="px-4 py-3 text-right font-medium">{{ formatRupiah($penjualan->ongkir) }}</td><td></td></tr>
                    @endif
                    <tr class="bg-gray-100"><td colspan="4" class="px-4 py-3 font-semibold">Grand Total</td><td class="px-4 py-3 text-right font-bold">{{ formatRupiah($penjualan->total) }}</td><td></td></tr>
                </tfoot>
            </table>
        </div>
    </x-card>
</x-app-layout>