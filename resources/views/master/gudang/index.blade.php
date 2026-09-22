<x-app-layout>
    <x-slot name="title">Data Gudang</x-slot>

    <x-page-header title="Data Gudang" subtitle="Kelola gudang penyimpanan barang">
        <a href="{{ route('gudang.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Gudang
        </a>
    </x-page-header>

    <x-card>
        <x-atur-kolom :options="$kolomOptions" :aktif="$kolomAktif" action="{{ route('gudang.simpan-kolom') }}" />

        <p class="px-4 pt-1 text-xs text-gray-500 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Klik baris gudang untuk melihat stok barang di gudang tersebut.
        </p>

        <div class="overflow-x-auto" x-data="{ openId: null }">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($kolomAktif as $k)
                            <th class="px-4 py-3 {{ in_array($k, ['stok_count'], true) ? 'text-center' : 'text-left' }} text-xs font-semibold text-gray-500 uppercase {{ $k === 'aksi' ? 'text-center' : '' }}">{{ $kolomOptions[$k] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($gudangs as $g)
                        @php
                            $itemsGudang = $g->stok
                                ->filter(fn ($s) => (float) $s->qty > 0 && $s->barang)
                                ->sortBy(fn ($s) => mb_strtolower((string) $s->barang->nama));
                        @endphp
                        <tr class="hover:bg-gray-50 cursor-pointer transition" @click="openId = (openId === {{ $g->id }}) ? null : {{ $g->id }}">
                            @foreach($kolomAktif as $k)
                                @switch($k)
                                    @case('kode')
                                        <td class="px-4 py-3 font-medium text-gray-800">{{ $g->kode }}</td>
                                        @break
                                    @case('nama')
                                        <td class="px-4 py-3 font-medium text-gray-800">
                                            <span class="inline-flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200" :class="openId === {{ $g->id }} ? 'rotate-180 text-emerald-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                {{ $g->nama }}
                                                @if($g->id === (int) optional(\App\Models\Gudang::utama())->id)
                                                    <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-700">Utama</span>
                                                @endif
                                            </span>
                                        </td>
                                        @break
                                    @case('alamat')
                                        <td class="px-4 py-3 text-gray-600">{{ $g->alamat ?? '-' }}</td>
                                        @break
                                    @case('stok_count')
                                        <td class="px-4 py-3 text-center text-gray-600">{{ number_format($g->stok_count, 0, ',', '.') }}</td>
                                        @break
                                    @case('status')
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center">
                                                @if($g->is_aktif)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Aktif</span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Nonaktif</span>
                                                @endif
                                            </div>
                                        </td>
                                        @break
                                    @case('aksi')
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center gap-1">
                                                <a href="{{ route('gudang.edit', $g) }}" @click.stop class="inline-flex items-center justify-center w-11 h-11 text-blue-600 hover:bg-blue-50 rounded-md" aria-label="Edit Gudang {{ $g->nama }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>
                                                <form method="POST" action="{{ route('gudang.destroy', $g) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="button" @click.stop aria-label="Hapus Gudang {{ $g->nama }}" class="inline-flex items-center justify-center w-11 h-11 text-red-600 hover:bg-red-50 rounded-md"
                                                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'hapus-gudang-{{ $g->id }}' }))">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                    <x-confirm-dialog name="hapus-gudang-{{ $g->id }}" title="Hapus Gudang"
                                                                      message="Yakin ingin menghapus gudang '{{ $g->nama }}'? Tindakan ini tidak dapat dibatalkan." />
                                                </form>
                                            </div>
                                        </td>
                                        @break
                                @endswitch
                            @endforeach
                        </tr>
                        <tr x-cloak x-show="openId === {{ $g->id }}" class="bg-gray-50/60">
                            <td colspan="{{ count($kolomAktif) }}" class="px-4 py-4">
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-700">Stok Barang — {{ $g->nama }}</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">Item dengan stok tersimpan di gudang ini.</p>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                        <span>{{ $itemsGudang->count() }}</span> item
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-3">
                                    @forelse($itemsGudang as $s)
                                        <div class="group text-left flex flex-col p-3 rounded-xl border border-gray-200 bg-white hover:border-emerald-300 hover:bg-emerald-50/60 transition">
                                            <span class="w-full flex items-start justify-between gap-2">
                                                @if($s->barang->foto)
                                                    <img src="{{ $s->barang->foto_url }}" alt="Foto {{ $s->barang->nama }}" class="shrink-0 w-10 h-10 rounded-lg object-cover">
                                                @else
                                                    <span class="shrink-0 w-10 h-10 rounded-lg flex items-center justify-center text-sm font-bold text-white {{ $s->barang->avatar_warna }}">{{ $s->barang->inisial }}</span>
                                                @endif
                                                <span class="inline-flex items-center text-[10px] font-medium rounded px-1.5 py-0.5 {{ $s->barang->tipe === 'jasa' ? 'bg-violet-100 text-violet-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $s->barang->tipe === 'jasa' ? 'Jasa' : 'Barang' }}</span>
                                            </span>
                                            <span class="mt-2 block text-sm font-medium text-gray-800 leading-snug line-clamp-2">{{ $s->barang->label }}</span>
                                            <span class="block text-xs text-gray-500 mt-0.5">{{ $s->barang->kode }}</span>
                                            <span class="mt-2 pt-2 border-t border-gray-100 block text-[11px] font-medium text-emerald-600">Stok {{ formatKuantitas($s->qty) }} {{ $s->barang->satuan }}</span>
                                        </div>
                                    @empty
                                        <p class="col-span-full py-8 text-center text-sm text-gray-500">Belum ada item dengan stok di gudang ini.</p>
                                    @endforelse
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($kolomAktif) }}" class="px-4 py-16 text-center text-gray-500">Belum ada gudang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>