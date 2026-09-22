<x-app-layout>
    <x-slot name="title">Buku Pembantu Persediaan</x-slot>

    <x-page-header title="Buku Pembantu Persediaan" subtitle="Kartu stok barang (metode rata-rata)"></x-page-header>

    <x-card>
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex flex-wrap gap-2 items-end">
                <div>
                    <x-input-label for="barang_id" value="Barang (opsional)" />
                    <x-select id="barang_id" name="barang_id" class="mt-1 w-56">
                        <option value="">Semua Barang</option>
                        @foreach($barang as $b)
                            <option value="{{ $b->id }}" {{ request('barang_id') == $b->id ? 'selected' : '' }}>{{ $b->label }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div>
                    <x-input-label for="dari" value="Dari" />
                    <input type="date" id="dari" name="dari" value="{{ $dari }}" class="mt-1 border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <x-input-label for="sampai" value="Sampai" />
                    <input type="date" id="sampai" name="sampai" value="{{ $sampai }}" class="mt-1 border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <button class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm rounded-lg">Tampilkan</button>
                @if(request()->hasAny(['barang_id', 'dari']))
                    <a href="{{ route('bb-persediaan.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-lg">Reset</a>
                @endif
            </form>
        </div>

        @if($brg)
            <div class="px-4 py-3 border-b border-gray-100 flex flex-col sm:flex-row sm:justify-between gap-2">
                <h3 class="font-semibold text-gray-800">{{ $brg->label }}</h3>
                <div class="text-right">
                    <p class="text-xs text-gray-500">Saldo Akhir</p>
                    <p class="font-bold text-emerald-700 text-lg">{{ formatRupiah($saldoAkhirHarga) }} <span class="text-sm text-gray-500">({{ formatKuantitas($saldoAkhirQty) }} {{ $brg->satuan }})</span></p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Masuk</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Harga Masuk</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Keluar</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Harga Keluar</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Saldo Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Saldo (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if(request()->filled('dari') || $saldoAwalQty != 0)
                            <tr class="bg-slate-50 font-medium">
                                <td class="px-4 py-2.5 text-slate-500">{{ $dari ? formatTanggalSingkat($dari) : '-' }}</td>
                                <td class="px-4 py-2.5 text-slate-700 italic">Saldo Awal Persediaan</td>
                                <td class="px-4 py-2.5 text-right text-slate-400">-</td>
                                <td class="px-4 py-2.5 text-right text-slate-400">-</td>
                                <td class="px-4 py-2.5 text-right text-slate-400">-</td>
                                <td class="px-4 py-2.5 text-right text-slate-400">-</td>
                                <td class="px-4 py-2.5 text-right font-medium text-slate-800">{{ formatKuantitas($saldoAwalQty) }}</td>
                                <td class="px-4 py-2.5 text-right font-semibold text-slate-800">{{ formatRupiah($saldoAwalHarga) }}</td>
                            </tr>
                        @endif
                        @forelse($persediaan as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">{{ formatTanggalSingkat($p->tanggal) }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $p->keterangan ?? '-' }}</td>
                                <td class="px-4 py-3 text-right {{ $p->masuk_qty > 0 ? 'text-emerald-600' : 'text-gray-300' }}">{{ $p->masuk_qty > 0 ? formatKuantitas($p->masuk_qty) : '-' }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ $p->masuk_harga > 0 ? formatRupiah($p->masuk_harga) : '-' }}</td>
                                <td class="px-4 py-3 text-right {{ $p->keluar_qty > 0 ? 'text-red-600' : 'text-gray-300' }}">{{ $p->keluar_qty > 0 ? formatKuantitas($p->keluar_qty) : '-' }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ $p->keluar_harga > 0 ? formatRupiah($p->keluar_harga) : '-' }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-800">{{ formatKuantitas($p->saldo_qty) }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">{{ formatRupiah($p->saldo_harga) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-10 text-center text-gray-500">Tidak ada mutasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="overflow-x-auto">
                <x-atur-kolom :options="$kolomOptions" :aktif="$kolomAktif" action="{{ route('bb-persediaan.simpan-kolom') }}" />
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach($kolomAktif as $k)
                                <th class="px-4 py-3 {{ in_array($k, ['debit_qty', 'kredit_qty', 'saldo_qty', 'saldo_harga'], true) ? 'text-right' : ($k === 'detail' ? 'text-center' : 'text-left') }} text-xs font-semibold text-gray-500 uppercase">
                                    {{ $kolomOptions[$k] }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($barang as $b)
                            @php
                                $totalMasuk = (float) \App\Models\BbPersediaan::where('barang_id', $b->id)->sum('masuk_qty');
                                $totalKeluar = (float) \App\Models\BbPersediaan::where('barang_id', $b->id)->sum('keluar_qty');
                                $lastSaldo = \App\Models\BbPersediaan::where('barang_id', $b->id)
                                    ->orderByDesc('id')->first();
                                $saldoQty = $lastSaldo ? (float) $lastSaldo->saldo_qty : 0;
                                $saldoHarga = $lastSaldo ? (float) $lastSaldo->saldo_harga : 0;
                            @endphp
                            @if($totalMasuk > 0 || $totalKeluar > 0)
                                <tr class="hover:bg-gray-50">
                                    @foreach($kolomAktif as $k)
                                        @switch($k)
                                            @case('kode')
                                                <td class="px-4 py-3 font-medium text-gray-800">{{ $b->kode }}</td>
                                                @break
                                            @case('nama')
                                                <td class="px-4 py-3 text-gray-600">{{ $b->nama }}</td>
                                                @break
                                            @case('satuan')
                                                <td class="px-4 py-3 text-gray-500">{{ $b->satuan }}</td>
                                                @break
                                            @case('debit_qty')
                                                <td class="px-4 py-3 text-right text-emerald-600">{{ $totalMasuk > 0 ? formatKuantitas($totalMasuk) : '-' }}</td>
                                                @break
                                            @case('kredit_qty')
                                                <td class="px-4 py-3 text-right text-red-600">{{ $totalKeluar > 0 ? formatKuantitas($totalKeluar) : '-' }}</td>
                                                @break
                                            @case('saldo_qty')
                                                <td class="px-4 py-3 text-right font-medium text-gray-800">{{ formatKuantitas($saldoQty) }}</td>
                                                @break
                                            @case('saldo_harga')
                                                <td class="px-4 py-3 text-right font-semibold text-gray-800">{{ formatRupiah($saldoHarga) }}</td>
                                                @break
                                            @case('detail')
                                                <td class="px-4 py-3 text-center">
                                                    <a href="{{ route('bb-persediaan.index', ['barang_id' => $b->id, 'dari' => $dari, 'sampai' => $sampai]) }}" class="text-emerald-600 hover:text-emerald-800 text-xs font-medium">
                                                        Lihat Detail →
                                                    </a>
                                                </td>
                                                @break
                                        @endswitch
                                    @endforeach
                                </tr>
                            @endif
                        @empty
                            <tr><td colspan="{{ count($kolomAktif) }}" class="px-4 py-10 text-center text-gray-500">Belum ada data persediaan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>
</x-app-layout>
