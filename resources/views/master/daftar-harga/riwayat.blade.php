<x-app-layout>
    <x-slot name="title">Riwayat Harga</x-slot>

    <x-page-header title="Riwayat Harga" subtitle="Lihat semua riwayat harga (termasuk yang sudah tidak aktif)">
        <a href="{{ route('daftar-harga.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <x-card>
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex flex-wrap gap-2 items-end">
                <div>
                    <x-input-label for="entitas" value="Tipe Harga" />
                    <x-select id="entitas" name="entitas" class="mt-1">
                        <option value="supplier" {{ $entitas !== 'customer' ? 'selected' : '' }}>Beli (Supplier)</option>
                        <option value="customer" {{ $entitas === 'customer' ? 'selected' : '' }}>Jual (Customer)</option>
                    </x-select>
                </div>
                <div id="rv-supplier-wrap" @if($entitas === 'customer') style="display:none" @endif>
                    <x-input-label for="supplier_id" value="Supplier" />
                    <x-select id="supplier_id" name="supplier_id" class="mt-1 w-56">
                        <option value="">Pilih Supplier...</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div id="rv-customer-wrap" @if($entitas !== 'customer') style="display:none" @endif>
                    <x-input-label for="customer_id" value="Customer" />
                    <x-select id="customer_id" name="customer_id" class="mt-1 w-56">
                        <option value="">Pilih Customer...</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div>
                    <x-input-label for="barang_id" value="Barang" />
                    <x-select id="barang_id" name="barang_id" class="mt-1 w-56" required>
                        <option value="">Pilih Barang...</option>
                        @foreach($barangList as $b)
                            <option value="{{ $b->id }}" {{ request('barang_id') == $b->id ? 'selected' : '' }}>{{ $b->label }}</option>
                        @endforeach
                    </x-select>
                </div>
                <button class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm rounded-lg">Tampilkan Riwayat</button>
            </form>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var entitasEl = document.getElementById('entitas');
                if (!entitasEl) { return; }
                entitasEl.addEventListener('change', function () {
                    var sup = document.getElementById('rv-supplier-wrap');
                    var cust = document.getElementById('rv-customer-wrap');
                    var showCust = this.value === 'customer';
                    if (sup) sup.style.display = showCust ? 'none' : '';
                    if (cust) cust.style.display = showCust ? '' : 'none';
                });
            });
        </script>

        @if($riwayat !== null)
            @if($perubahan->isNotEmpty())
                <div class="px-4 py-3 border-b border-gray-100 bg-slate-50">
                    <h4 class="font-semibold text-gray-800 text-sm">Riwayat Perubahan Harga</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Catatan setiap perubahan harga (harga lama &rarr; baru) pada kombinasi supplier/customer + barang.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tier Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Harga Lama</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Harga Baru</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Berlaku Mulai</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Catatan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($perubahan as $p)
                                <tr>
                                    <td class="px-4 py-3">
                                        @if($p->tipe === 'ubah')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Diubah</span>
                                        @elseif($p->tipe === 'nonaktif')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Nonaktif</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Baru</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($p->tipe === 'ubah')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                                {{ formatKuantitas($p->min_qty_baru) }}{{ $p->max_qty_baru ? ' - ' . formatKuantitas($p->max_qty_baru) : '+' }} pcs
                                                @if($p->min_qty_lama && $p->min_qty_lama != $p->min_qty_baru)
                                                    <span class="text-gray-400 ml-1">(sebelumnya {{ formatKuantitas($p->min_qty_lama) }}{{ $p->max_qty_lama ? ' - ' . formatKuantitas($p->max_qty_lama) : '+' }})</span>
                                                @endif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                                {{ formatKuantitas($p->min_qty_baru) }}{{ $p->max_qty_baru ? ' - ' . formatKuantitas($p->max_qty_baru) : '+' }} pcs
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-500">{{ $p->harga_lama !== null ? formatRupiah($p->harga_lama) : '-' }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-800">{{ $p->harga_baru !== null ? formatRupiah($p->harga_baru) : '-' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $p->tanggal_mulai_baru ? formatTanggalSingkat($p->tanggal_mulai_baru) : ($p->tanggal_mulai_lama ? formatTanggalSingkat($p->tanggal_mulai_lama) : '-') }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $p->catatan ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-400 whitespace-nowrap">{{ $p->created_at ? $p->created_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="px-4 py-3 border-b border-gray-100 bg-slate-50">
                <h4 class="font-semibold text-gray-800 text-sm">Daftar Baris Harga</h4>
                <p class="text-xs text-gray-500 mt-0.5">Semua baris harga (aktif &amp; nonaktif) untuk kombinasi ini.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tier Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Harga</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Berlaku Sejak</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Berlaku Sampai</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($riwayat as $r)
                            <tr class="{{ $r->is_aktif ? 'hover:bg-gray-50' : 'bg-gray-50/50' }}">
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                        {{ formatKuantitas($r->min_qty) }}{{ $r->max_qty ? ' - ' . formatKuantitas($r->max_qty) : '+' }} pcs
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">{{ formatRupiah($r->harga) }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $r->keterangan ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $r->tanggal_mulai ? formatTanggalSingkat($r->tanggal_mulai) : '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $r->tanggal_selesai ? formatTanggalSingkat($r->tanggal_selesai) : '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($r->is_aktif)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">Tidak ada riwayat harga untuk kombinasi ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-4 py-16 text-center text-gray-500 text-sm">Pilih supplier/customer dan barang untuk melihat riwayat harganya.</div>
        @endif
    </x-card>
</x-app-layout>
