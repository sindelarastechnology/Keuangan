<x-app-layout>
    <x-slot name="title">Detail Pembelian</x-slot>

    <x-page-header title="Detail Pembelian" :subtitle="$pembelian->nomor">
        <div class="flex items-center gap-2">
            @if($pembelian->status === 'pending')
                <a href="{{ route('pembelian.edit', $pembelian) }}" class="inline-flex items-center gap-2 text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 px-3 py-2 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Draft
                </a>
                <form method="POST" action="{{ route('pembelian.post', $pembelian) }}">
                    @csrf
                    <x-button class="inline-flex items-center gap-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 px-3 py-2 rounded-lg transition">Posting Transaksi</x-button>
                </form>
                <form method="POST" action="{{ route('pembelian.void', $pembelian) }}">
                    @csrf
                    <button type="button" class="inline-flex items-center gap-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 px-3 py-2 rounded-lg transition"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'batalkan-pembelian-draft' }))">Batalkan Draft</button>
                    <x-confirm-dialog name="batalkan-pembelian-draft" title="Batalkan Draft Pembelian"
                                      message="Yakin ingin membatalkan draft pembelian '{{ $pembelian->nomor }}'? Draft tidak memengaruhi stok & jurnal, dan tindakan ini tidak dapat dibatalkan." />
                </form>
            @endif
            @if($pembelian->status === 'posted')
            <a href="{{ route('pembelian.pdf', $pembelian) }}" class="inline-flex items-center gap-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 px-3 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Faktur
            </a>
            @endif
            <a href="{{ route('pembelian.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </a>
        </div>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <x-card>
            <div class="p-5">
                <h3 class="font-bold text-lg text-slate-700 mb-4">{{ formatRupiah($pembelian->total) }}</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Tanggal</dt><dd class="font-medium">{{ formatTanggal($pembelian->tanggal) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Supplier</dt><dd class="font-medium">{{ $pembelian->supplier->nama }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Gudang</dt><dd class="font-medium">{{ $pembelian->gudang?->nama ?? '—' }}</dd></div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Metode Bayar</dt>
                        <dd><span class="inline-flex px-2 py-1 text-xs rounded-full {{ $pembelian->metode_bayar === 'tunai' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($pembelian->metode_bayar) }}</span></dd>
                    </div>
                    @if($pembelian->metode_bayar === 'tunai')
                    <div class="flex justify-between"><dt class="text-gray-500">Dibayar Melalui</dt><dd class="font-medium">{{ $pembelian->rekening?->nama ?? '—' }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-gray-500">Keterangan</dt><dd class="font-medium text-right">{{ $pembelian->keterangan ?? '-' }}</dd></div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            @if($pembelian->status === 'posted')
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">Posted</span>
                            @elseif($pembelian->status === 'pending')
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
            @if($pembelian->jurnal)
                <div class="p-5">
                    <p class="text-xs text-gray-500 mb-3">Jurnal: {{ $pembelian->jurnal->nomor }}</p>
                    <table class="w-full text-sm">
                        <thead><tr class="text-left text-xs text-gray-500"><th class="py-1">Akun</th><th class="py-1 text-right">Debit</th><th class="py-1 text-right">Kredit</th></tr></thead>
                        <tbody>
                            @foreach($pembelian->jurnal->items as $item)
                                <tr class="border-t border-gray-100">
                                    <td class="py-2">{{ $item->akun->nama }}</td>
                                    <td class="py-2 text-right">{{ $item->debit > 0 ? formatRupiah($item->debit) : '-' }}</td>
                                    <td class="py-2 text-right">{{ $item->kredit > 0 ? formatRupiah($item->kredit) : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="p-5 text-sm text-gray-500">Tidak ada jurnal terkait.</p>
            @endif
        </x-card>
    </div>

    @if($pembelian->status === 'posted' && $pembelian->metode_bayar === 'kredit')
    <x-card title="Status Hutang" class="mb-4">
        <div class="p-5">
            @php
                $sisa = (float) $pembelian->sisaHutang;
                $sisaTampil = max(0, $sisa);
                $dibayar = max(0, (float) $pembelian->total - $sisa);
            @endphp

            @if($pembelian->hutangTerverifikasi)
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm text-gray-500">Status</span>
                    @if($sisaTampil <= 0.005)
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">Lunas</span>
                    @else
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">Belum Lunas</span>
                    @endif
                </div>

                <dl class="space-y-3 text-sm mb-4">
                    <div class="flex justify-between"><dt class="text-gray-500">Tagihan</dt><dd class="font-medium">{{ formatRupiah($pembelian->total) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Sudah Dibayar</dt><dd class="font-medium text-blue-600">{{ formatRupiah(min((float) $pembelian->total, $dibayar)) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Sisa</dt><dd class="font-medium {{ $sisaTampil > 0.005 ? 'text-red-600' : '' }}">{{ formatRupiah($sisaTampil) }}</dd></div>
                </dl>

                @if($sisaTampil > 0.005)
                    <form method="POST" action="{{ route('pembelian.pelunasan', $pembelian) }}" class="space-y-3 border-t border-gray-100 pt-4">
                        @csrf
                        <div>
                            <label for="rekening_id" class="block text-sm text-gray-600 mb-1">Bayar Melalui</label>
                            <select id="rekening_id" name="rekening_id" required class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach($rekeningList as $rek)
                                    <option value="{{ $rek->id }}">{{ $rek->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="nominal" class="block text-sm text-gray-600 mb-1">Nominal Pelunasan</label>
                            <input id="nominal" name="nominal" type="number" step="0.01" min="0.01" value="{{ number_format($sisaTampil, 2, '.', '') }}" required class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="tanggal" class="block text-sm text-gray-600 mb-1">Tanggal</label>
                            <input id="tanggal" name="tanggal" type="date" value="{{ now()->toDateString() }}" required class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="flex items-center gap-2 pt-1">
                            <x-button class="inline-flex items-center gap-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 px-3 py-2 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Catat Pelunasan
                            </x-button>
                            <span class="text-xs text-gray-400">Pelunasan dicatat sebagai Kas Keluar dan dapat dibatalkan dari menu Kas Keluar.</span>
                        </div>
                    </form>
                @endif
            @else
                <div class="flex items-start gap-2 mb-2">
                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Butuh Verifikasi</span>
                </div>
                <p class="text-sm text-gray-600">
                    Transaksi ini tercatat sebelum fitur pelunasan per faktur tersedia, sehingga sisa hutangnya belum ter-atribusi ke faktur ini.
                    Saldo hutang tetap terpantau melalui saldo supplier di <b>BB Hutang</b>. Gunakan menu <b>Kas Keluar</b> untuk mencatat pembayaran ke supplier ini.
                </p>
            @endif
        </div>
    </x-card>
    @endif

    <x-card title="Rincian Pembelian">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Barang</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Harga Satuan</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Diskon</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($pembelian->items as $item)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $item->barang->nama }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ formatKuantitas($item->jumlah) }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ formatRupiah($item->harga_satuan) }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ formatRupiah($item->diskon) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800">{{ formatRupiah($item->subtotal) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr><td colspan="4" class="px-4 py-3 text-right text-gray-600">Subtotal</td><td class="px-4 py-3 text-right font-medium">{{ formatRupiah($pembelian->subtotal) }}</td></tr>
                    @if($pembelian->diskon_nominal > 0)
                    <tr><td colspan="4" class="px-4 py-3 text-right text-gray-600">Diskon</td><td class="px-4 py-3 text-right text-red-500">- {{ formatRupiah($pembelian->diskon_nominal) }}</td></tr>
                    @endif
                    @if($pembelian->pajak_nominal > 0)
                    <tr><td colspan="4" class="px-4 py-3 text-right text-gray-600">Pajak</td><td class="px-4 py-3 text-right font-medium">{{ formatRupiah($pembelian->pajak_nominal) }}</td></tr>
                    @endif
                    @if((float) $pembelian->ongkir > 0)
                    <tr><td colspan="4" class="px-4 py-3 text-right text-gray-600">Ongkir</td><td class="px-4 py-3 text-right font-medium">{{ formatRupiah($pembelian->ongkir) }}</td></tr>
                    @endif
                    <tr class="bg-gray-100"><td colspan="4" class="px-4 py-3 font-semibold">Grand Total</td><td class="px-4 py-3 text-right font-bold">{{ formatRupiah($pembelian->total) }}</td></tr>
                </tfoot>
            </table>
        </div>
    </x-card>
</x-app-layout>