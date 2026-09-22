<x-app-layout>
    <x-slot name="title">Daftar Harga</x-slot>

    <x-page-header title="Daftar Harga" subtitle="Harga barang per supplier/customer (termasuk tier volume & historis)">
        <a href="{{ route('daftar-harga.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Harga
        </a>
    </x-page-header>

    <x-card>
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex flex-wrap gap-2 items-end">
                <div class="flex-1 min-w-[160px]">
                    <x-input-label for="entitas" value="Tipe Harga" />
                    <x-select id="entitas" name="entitas" class="mt-1 w-full">
                        <option value="">Semua</option>
                        <option value="supplier" {{ request('entitas') === 'supplier' ? 'selected' : '' }}>Beli (Supplier)</option>
                        <option value="customer" {{ request('entitas') === 'customer' ? 'selected' : '' }}>Jual (Customer)</option>
                    </x-select>
                </div>
                <div class="flex-1 min-w-[200px]" id="filter-supplier-wrap" @if(request('entitas') === 'customer') style="display:none" @endif>
                    <x-input-label for="supplier_id" value="Supplier" />
                    <x-select id="supplier_id" name="supplier_id" class="mt-1 w-full">
                        <option value="">Semua Supplier</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div class="flex-1 min-w-[200px]" id="filter-customer-wrap" @if(request('entitas') !== 'customer') style="display:none" @endif>
                    <x-input-label for="customer_id" value="Customer" />
                    <x-select id="customer_id" name="customer_id" class="mt-1 w-full">
                        <option value="">Semua Customer</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="show_history" value="1" class="rounded text-emerald-600" {{ $showHistory ? 'checked' : '' }}>
                        Tampilkan Historis
                    </label>
                    <button class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm rounded-lg">Filter</button>
                    @if(request()->hasAny(['entitas', 'supplier_id', 'customer_id', 'show_history']))
                        <a href="{{ route('daftar-harga.index') }}" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-lg">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var entitasEl = document.getElementById('entitas');
                if (!entitasEl) { return; }
                entitasEl.addEventListener('change', function () {
                    var sup = document.getElementById('filter-supplier-wrap');
                    var cust = document.getElementById('filter-customer-wrap');
                    var showCust = this.value === 'customer';
                    if (sup) sup.style.display = showCust ? 'none' : '';
                    if (cust) cust.style.display = showCust ? '' : 'none';
                });
            });
        </script>

        <x-atur-kolom :options="$kolomOptions" :aktif="$kolomAktif" action="{{ route('daftar-harga.simpan-kolom') }}" :wajib="['barang', 'aksi']" />

        @foreach(['supplier' => 'Harga Beli (Supplier)', 'customer' => 'Harga Jual (Customer)'] as $ent => $entTitle)
            @if(isset($daftarHarga[$ent]) && $daftarHarga[$ent]->isNotEmpty())
                <div class="border-b border-gray-200 px-4 py-2 bg-gray-50">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $ent === 'customer' ? 'bg-violet-100 text-violet-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $entTitle }}</span>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($daftarHarga[$ent] as $key => $items)
                        @php
                            $first = $items->first();
                            $counterparty = $first->entitas === 'customer' ? $first->customer : $first->supplier;
                            $nama = $counterparty?->nama ?? '-';
                        @endphp
                        <div x-data="{ open: true }">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-gray-400 transition" :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    <span class="font-semibold text-gray-800">{{ $nama }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $items->pluck('barang_id')->unique()->count() }} barang</span>
                                </div>
                            </button>

                            <div x-show="open">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                @foreach($kolomAktif as $k)
                                                    <th class="px-4 py-2.5 {{ in_array($k, ['harga', 'status', 'aksi'], true) ? ($k === 'harga' ? 'text-right' : 'text-center') : 'text-left' }} text-xs font-semibold text-gray-500 uppercase">{{ $kolomOptions[$k] }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($items->groupBy('barang_id') as $barangId => $tiers)
                                                @foreach($tiers as $i => $dh)
                                                    <tr class="{{ $dh->is_aktif ? 'hover:bg-gray-50' : 'bg-gray-50/50' }}">
                                                        @foreach($kolomAktif as $k)
                                                            @switch($k)
                                                                @case('barang')
                                                                    @if($i === 0)
                                                                        <td class="px-4 py-2.5 font-medium text-gray-800" rowspan="{{ $tiers->count() }}">
                                                                            {{ $dh->barang->label ?? '-' }}
                                                                        </td>
                                                                    @endif
                                                                    @break
                                                                @case('tier')
                                                                    <td class="px-4 py-2.5">
                                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                                                            {{ formatKuantitas($dh->min_qty) }}{{ $dh->max_qty ? ' - ' . formatKuantitas($dh->max_qty) : '+' }} pcs
                                                                        </span>
                                                                    </td>
                                                                    @break
                                                                @case('harga')
                                                                    <td class="px-4 py-2.5 text-right font-semibold text-gray-800">{{ formatRupiah($dh->harga) }}</td>
                                                                    @break
                                                                @case('keterangan')
                                                                    <td class="px-4 py-2.5 text-gray-500 text-xs">{{ $dh->keterangan ?? '-' }}</td>
                                                                    @break
                                                                @case('status')
                                                                    <td class="px-4 py-2.5 text-center">
                                                                        @if($dh->is_aktif && !$dh->tanggal_selesai)
                                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Aktif</span>
                                                                        @elseif($dh->is_aktif && $dh->tanggal_selesai)
                                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Berlaku s/d {{ $dh->tanggal_selesai->format('d/m/Y') }}</span>
                                                                        @else
                                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Nonaktif</span>
                                                                        @endif
                                                                    </td>
                                                                    @break
                                                                @case('aksi')
                                                                    @if($i === 0)
                                                                        <td class="px-4 py-2.5 text-center">
                                                                            <div class="flex justify-center gap-1">
                                                                                <a href="{{ route('daftar-harga.edit', $dh) }}" class="inline-flex items-center justify-center w-11 h-11 text-blue-600 hover:bg-blue-50 rounded-md" title="Edit" aria-label="Edit Harga {{ $dh->barang->label ?? '' }}">
                                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                                                </a>
                                                                                <form method="POST" action="{{ route('daftar-harga.destroy', $dh) }}" class="inline">
                                                                                    @csrf @method('DELETE')
                                                                                    <button type="button" aria-label="Nonaktifkan Harga {{ $dh->barang->label ?? '' }}" class="inline-flex items-center justify-center w-11 h-11 text-red-600 hover:bg-red-50 rounded-md" title="Nonaktifkan"
                                                                                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'nonaktifkan-harga-{{ $dh->id }}' }))">
                                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                                    </button>
                                                                                    <x-confirm-dialog name="nonaktifkan-harga-{{ $dh->id }}" title="Nonaktifkan Harga"
                                                                                                      message="Yakin ingin menonaktifkan semua harga untuk item '{{ $dh->barang->label ?? '-' }}'? Riwayat akan tetap tersimpan dan tindakan ini tidak dapat dibatalkan." />
                                                                                </form>
                                                                            </div>
                                                                        </td>
                                                                    @endif
                                                                    @break
                                                            @endswitch
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach

        @if(!isset($daftarHarga['supplier']) && !isset($daftarHarga['customer']))
            <div class="px-4 py-16 text-center text-gray-500 text-sm">Belum ada daftar harga.</div>
        @endif
    </x-card>
</x-app-layout>
