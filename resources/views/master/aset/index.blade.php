<x-app-layout>
    <x-slot name="title">Aset Tetap</x-slot>

    <x-page-header title="Aset Tetap" subtitle="Daftar aset tetap dan riwayat penyusutan">
        <a href="{{ route('aset.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Aset
        </a>
    </x-page-header>

    <x-card>
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex flex-wrap gap-2">
                <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari kode atau nama aset..."
                       class="border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                <select name="kategori" class="border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Jenis</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" @selected(request('kategori') === $kat)>{{ $kat }}</option>
                    @endforeach
                </select>
                <select name="status" class="border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
                </select>
                <button class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm rounded-lg">Cari</button>
            </form>
        </div>

        <x-atur-kolom :options="$kolomOptions" :aktif="$kolomAktif" action="{{ route('aset.simpan-kolom') }}" />

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($kolomAktif as $k)
                            <th class="px-4 py-3 {{ in_array($k, ['harga', 'akumulasi', 'nilai', 'beban'], true) ? 'text-right' : (in_array($k, ['status', 'aksi'], true) ? 'text-center' : 'text-left') }} text-xs font-semibold text-gray-500 uppercase">
                                {{ $kolomOptions[$k] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($asets as $a)
                        <tr class="hover:bg-gray-50">
                            @foreach($kolomAktif as $k)
                                @switch($k)
                                    @case('kode')
                                        <td class="px-4 py-3 text-gray-600">{{ $a->kode }}</td>
                                        @break
                                    @case('nama')
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-800">{{ $a->nama }}</div>
                                            <div class="text-xs text-gray-500">{{ $a->kategori }}</div>
                                        </td>
                                        @break
                                    @case('lokasi')
                                        <td class="px-4 py-3 text-gray-600">{{ $a->lokasi ?: '-' }}</td>
                                        @break
                                    @case('tanggal')
                                        <td class="px-4 py-3 text-gray-600">{{ formatTanggalSingkat($a->tanggal_perolehan) }}</td>
                                        @break
                                    @case('harga')
                                        <td class="px-4 py-3 text-right text-gray-800">{{ formatRupiah($a->harga_perolehan) }}</td>
                                        @break
                                    @case('akumulasi')
                                        <td class="px-4 py-3 text-right text-gray-600">{{ formatRupiah($a->akumulasi) }}</td>
                                        @break
                                    @case('nilai')
                                        <td class="px-4 py-3 text-right font-medium {{ $a->nilai_buku > 0 ? 'text-emerald-700' : 'text-gray-400' }}">{{ formatRupiah($a->nilai_buku) }}</td>
                                        @break
                                    @case('beban')
                                        <td class="px-4 py-3 text-right text-gray-600">{{ formatRupiah($a->beban_bulanan) }}</td>
                                        @break
                                    @case('status')
                                        <td class="px-4 py-3 text-center">
                                            @if($a->status === 'selesai')
                                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700">Selesai</span>
                                            @elseif($a->status === 'nonaktif')
                                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Nonaktif</span>
                                            @elseif($a->sudah_disusutkan_penuh)
                                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">Tuntas</span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700">Aktif</span>
                                            @endif
                                        </td>
                                        @break
                                    @case('aksi')
                                        <td class="px-4 py-3 w-44">
                                            <div class="flex justify-center gap-1">
                                                <a href="{{ route('aset.show', $a) }}" class="inline-flex items-center justify-center w-11 h-11 text-gray-600 hover:bg-gray-100 rounded-md" title="Detail" aria-label="Detail Aset {{ $a->kode }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                                <a href="{{ route('aset.edit', $a) }}" class="inline-flex items-center justify-center w-11 h-11 text-blue-600 hover:bg-blue-50 rounded-md" title="Edit" aria-label="Edit Aset {{ $a->kode }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>

                                                @if($a->status === 'aktif')
                                                    <form method="POST" action="{{ route('aset.selesai', $a) }}" class="contents">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center justify-center w-11 h-11 text-emerald-600 hover:bg-emerald-50 rounded-md" title="Tandai Selesai" aria-label="Tandai selesai {{ $a->kode }}">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        </button>
                                                    </form>

                                                    <div x-data="{ alasan: 'rusak' }">
                                                        <button type="button" x-on:click="$dispatch('open-modal', 'hapus-aset-{{ $a->id }}')" class="inline-flex items-center justify-center w-11 h-11 text-red-600 hover:bg-red-50 rounded-md" title="Hapus Aset" aria-label="Hapus Aset {{ $a->kode }}">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>

                                                        <x-modal name="hapus-aset-{{ $a->id }}" maxWidth="md">
                                                            <form method="POST" action="{{ route('aset.disposisi', $a) }}">
                                                                @csrf
                                                                <div class="p-6">
                                                                    <div class="flex items-start gap-3">
                                                                        <div class="shrink-0 w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center">
                                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                                        </div>
                                                                        <div>
                                                                            <h2 class="text-lg font-semibold text-gray-900">Hapus Aset</h2>
                                                                            <p class="mt-1 text-sm text-gray-600">
                                                                                Aset "<span class="font-medium">{{ $a->kode }} - {{ $a->nama }}</span>". Pilih alasan penghapusan:
                                                                            </p>
                                                                        </div>
                                                                    </div>

                                                                    <div class="mt-5 space-y-2">
                                                                        @foreach(\App\Services\AsetDisposisiService::ALASAN as $alasan => $label)
                                                                            <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 cursor-pointer has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                                                                                <input type="radio" name="alasan" value="{{ $alasan }}" x-model="alasan" class="text-emerald-600 focus:ring-emerald-500" {{ $loop->first ? 'checked' : '' }}>
                                                                                <span class="text-sm font-medium text-gray-800">{{ $label }}</span>
                                                                            </label>
                                                                        @endforeach
                                                                    </div>

                                                                    <div class="mt-4" x-show="alasan === 'dijual'" x-cloak x-transition>
                                                                        <label for="harga-jual-{{ $a->id }}" class="block text-sm font-medium text-gray-700 mb-1">Harga Jual</label>
                                                                        <input id="harga-jual-{{ $a->id }}" type="number" name="harga_jual" min="0.01" step="any"
                                                                               x-bind:required="alasan === 'dijual'"
                                                                               placeholder="Rp"
                                                                               class="border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 rounded-lg shadow-sm text-sm w-full">
                                                                        @error('harga_jual')
                                                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                                        @enderror
                                                                    </div>

                                                                    <div class="mt-4 rounded-lg bg-gray-50 px-4 py-3 text-xs text-gray-500">
                                                                        Penyusutan akan dihentikan dan jurnal penghapusan aset dibuat. Bila dijual, selisihnya dicatat sebagai laba/rugi.
                                                                    </div>

                                                                    <div class="mt-6 flex justify-end gap-3">
                                                                        <x-button variant="secondary" type="button" x-on:click="$dispatch('close')">Batal</x-button>
                                                                        <x-button variant="danger" type="submit">Hapus Aset</x-button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </x-modal>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        @break
                                @endswitch
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($kolomAktif) }}" class="px-4 py-16 text-center text-gray-500">Belum ada aset tetap.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($asets->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $asets->links() }}</div>
        @endif
    </x-card>
</x-app-layout>