<x-app-layout>
    <x-slot name="title">Data Produk</x-slot>

    <x-page-header title="Data Produk" subtitle="Kelola data barang &amp; jasa">
        <a href="{{ route('data-produk.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Produk
        </a>
    </x-page-header>

    <x-card>
        <div class="px-4 pt-4 flex gap-2">
            @foreach(['' => 'Semua', 'barang' => 'Barang', 'jasa' => 'Jasa'] as $nilai => $label)
                <a href="{{ route('data-produk.index', array_filter(['tipe' => $nilai ?: null], fn ($v) => $v !== null)) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-medium {{ ($nilai ?: null) === $tipe ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="p-4 border-b border-gray-100 space-y-3">
            <form method="GET" class="flex flex-wrap gap-2 items-end">
                @if($tipe)
                    <input type="hidden" name="tipe" value="{{ $tipe }}">
                @endif
                <div>
                    <x-input-label value="Cari" />
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Nama / kode / barcode..."
                           class="mt-1 w-full sm:w-56 border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <x-input-label value="Kategori" />
                    <x-select name="kategori" class="mt-1 sm:w-44">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $k)
                            <option value="{{ $k }}" {{ request('kategori') === $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div>
                    <x-input-label value="Satuan" />
                    <x-select name="satuan" class="mt-1 sm:w-40">
                        <option value="">Semua Satuan</option>
                        @foreach($satuanList as $s)
                            <option value="{{ $s }}" {{ request('satuan') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div>
                    <x-input-label value="Stok" />
                    <x-select name="stok" class="mt-1 sm:w-44">
                        <option value="">Semua Stok</option>
                        <option value="rendah" {{ request('stok') === 'rendah' ? 'selected' : '' }}>Menipis (&le; stok min.)</option>
                        <option value="habis" {{ request('stok') === 'habis' ? 'selected' : '' }}>Habis</option>
                    </x-select>
                </div>
                <button class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm rounded-lg">Cari</button>
                @if(request()->has('cari') || request()->has('kategori') || request()->has('satuan') || request()->has('stok'))
                    <a href="{{ route('data-produk.index', $tipe ? ['tipe' => $tipe] : []) }}" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800">Reset</a>
                @endif
            </form>
        </div>

        <div class="flex justify-end px-4 pt-3">
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'konfig-kolom' }))"
                    class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                Atur Kolom
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($kolomAktif as $k)
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase {{ in_array($k, ['stok', 'min_stok', 'harga_beli', 'harga_jual'], true) ? 'text-right' : '' }} {{ $k === 'aksi' ? 'text-center' : '' }}">{{ $kolomOptions[$k] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($produk as $b)
                        <tr class="hover:bg-gray-50 align-top">
                            @foreach($kolomAktif as $k)
                                @switch($k)
                                    @case('foto')
                                        <td class="px-4 py-3">
                                            @if($b->foto)
                                                <img src="{{ $b->foto_url }}" alt="{{ $b->nama }}" class="w-10 h-10 rounded-full object-cover">
                                            @else
                                                <div class="w-10 h-10 rounded-full {{ $b->avatar_warna }} flex items-center justify-center text-white text-sm font-semibold">{{ $b->inisial }}</div>
                                            @endif
                                        </td>
                                        @break
                                    @case('kode')
                                        <td class="px-4 py-3 text-gray-600">{{ $b->kode }}</td>
                                        @break
                                    @case('nama')
                                        <td class="px-4 py-3 font-medium text-gray-800">
                                            {{ $b->nama }}
                                            <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs {{ $b->tipe === 'jasa' ? 'bg-violet-100 text-violet-700' : 'bg-emerald-100 text-emerald-700' }}">{{ ucfirst($b->tipe) }}</span>
                                            @if(! $b->is_aktif)
                                                <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-500">Nonaktif</span>
                                            @endif
                                        </td>
                                        @break
                                    @case('satuan')
                                        <td class="px-4 py-3 text-gray-600">{{ $b->satuan ?: '-' }}</td>
                                        @break
                                    @case('kategori')
                                        <td class="px-4 py-3">
                                            @if($b->kategori)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-emerald-100 text-emerald-700">{{ $b->kategori }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        @break
                                    @case('merek')
                                        <td class="px-4 py-3 text-gray-600">{{ $b->merek ?: '-' }}</td>
                                        @break
                                    @case('ukuran')
                                        <td class="px-4 py-3 text-gray-600">{{ $b->ukuran ?: '-' }}</td>
                                        @break
                                    @case('warna')
                                        <td class="px-4 py-3">
                                            @if($b->warna)
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs border" style="border-color: {{ $b->warna_hex ?? '#d1d5db' }};">
                                                    <span class="w-3 h-3 rounded-full border" style="background-color: {{ $b->warna_hex ?? '#9ca3af' }};"></span>
                                                    {{ $b->warna }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        @break
                                    @case('barcode')
                                        <td class="px-4 py-3">
                                            @if($b->barcode)
                                                <span class="font-mono text-xs text-gray-600">{{ $b->barcode }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        @break
                                    @case('stok')
                                        <td class="px-4 py-3 text-right">
                                            <span class="{{ (float) $b->stok <= 0 ? 'text-red-600 font-semibold' : ((float) $b->stok <= (float) $b->min_stok ? 'text-amber-600 font-semibold' : 'text-gray-700') }}">
                                                {{ formatKuantitas($b->stok) }}
                                            </span>
                                            @if($b->satuan)
                                                <span class="text-xs text-gray-400 ml-1">{{ $b->satuan }}</span>
                                            @endif
                                            @if((float) $b->stok > 0 && (float) $b->stok <= (float) $b->min_stok)
                                                <div class="text-[11px] text-amber-600 mt-0.5">Menipis</div>
                                            @elseif((float) $b->stok <= 0)
                                                <div class="text-[11px] text-red-600 mt-0.5">Habis</div>
                                            @endif
                                        </td>
                                        @break
                                    @case('min_stok')
                                        <td class="px-4 py-3 text-right text-gray-600">{{ formatKuantitas($b->min_stok) }}</td>
                                        @break
                                    @case('gudang')
                                        <td class="px-4 py-3">
                                            @forelse($b->stokGudang as $sg)
                                                @if($loop->first)
                                                    <span class="text-gray-600">{{ $sg->gudang?->nama }}:</span> <span class="font-semibold text-gray-800">{{ formatKuantitas($sg->qty) }}</span>
                                                @else
                                                    <span class="block text-xs text-gray-500 mt-0.5">{{ $sg->gudang?->nama }}: <span class="font-semibold">{{ formatKuantitas($sg->qty) }}</span></span>
                                                @endif
                                            @empty
                                                <span class="text-gray-400">-</span>
                                            @endforelse
                                        </td>
                                        @break
                                    @case('harga_beli')
                                        <td class="px-4 py-3 text-right text-gray-600">{{ formatRupiah($b->harga_beli) }}</td>
                                        @break
                                    @case('harga_jual')
                                        <td class="px-4 py-3 text-right font-semibold text-gray-800">{{ formatRupiah($b->harga_jual) }}</td>
                                        @break
                                    @case('keterangan')
                                        <td class="px-4 py-3 text-gray-500 max-w-xs">
                                            <span class="block truncate" title="{{ $b->keterangan }}">{{ $b->keterangan ?: '-' }}</span>
                                        </td>
                                        @break
                                    @case('aksi')
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center gap-1">
                                                <a href="{{ route('bb-persediaan.index', ['barang_id' => $b->id]) }}" class="inline-flex items-center justify-center w-11 h-11 text-emerald-600 hover:bg-emerald-50 rounded-md" aria-label="Kartu Stok {{ $b->kode }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m6 6v-2m-6-6a2 2 0 104 0 2 2 0 00-4 0zm-4 10h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                </a>
                                                <a href="{{ route('data-produk.edit', $b) }}" class="inline-flex items-center justify-center w-11 h-11 text-blue-600 hover:bg-blue-50 rounded-md" aria-label="Edit Produk {{ $b->kode }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>
                                                <form method="POST" action="{{ route('data-produk.destroy', $b) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="button" aria-label="Hapus Produk {{ $b->kode }}" class="inline-flex items-center justify-center w-11 h-11 text-red-600 hover:bg-red-50 rounded-md"
                                                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'hapus-produk-{{ $b->id }}' }))">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                    <x-confirm-dialog name="hapus-produk-{{ $b->id }}" title="Hapus {{ ucfirst($b->tipe) }}"
                                                                      message="Yakin ingin menghapus '{{ $b->nama ?: $b->kode }}'? Tindakan ini tidak dapat dibatalkan." />
                                                </form>
                                            </div>
                                        </td>
                                        @break
                                @endswitch
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($kolomAktif) }}" class="px-4 py-16 text-center text-gray-500">Belum ada {{ $tipe === 'jasa' ? 'jasa' : 'produk' }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($produk->hasPages())<div class="px-4 py-3 border-t border-gray-100">{{ $produk->links() }}</div>@endif
    </x-card>

    <x-modal name="konfig-kolom" :show="false" maxWidth="md">
        <form method="POST" action="{{ route('data-produk.simpan-kolom') }}" class="p-6">
            @csrf
            <h3 class="text-lg font-semibold text-gray-800">Atur Kolom Tabel</h3>
            <p class="text-sm text-gray-500 mt-0.5">Pilih kolom yang ingin ditampilkan pada tabel data produk.</p>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($kolomOptions as $key => $label)
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="kolom[]" value="{{ $key }}"
                               class="rounded text-emerald-600"
                               {{ in_array($key, $kolomAktif, true) ? 'checked' : '' }}
                               {{ $key === 'aksi' ? 'disabled' : '' }}>
                        {{ $label }}
                        @if($key === 'aksi')
                            <span class="text-xs text-gray-400">(selalu tampil)</span>
                        @endif
                    </label>
                @endforeach
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <x-secondary-button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'konfig-kolom' }))">Batal</x-secondary-button>
                <x-primary-button type="submit">Simpan Pengaturan</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>