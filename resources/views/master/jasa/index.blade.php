<x-app-layout>
    <x-slot name="title">Jasa</x-slot>

    <x-page-header title="Jasa" subtitle="Kelola data jasa / layanan">
        <a href="{{ route('jasa.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Jasa
        </a>
    </x-page-header>

    <x-card>
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex gap-2">
                <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama/kode jasa..."
                       class="w-full sm:max-w-xs border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                <button class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm rounded-lg">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Foto</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Jasa</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Satuan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Harga Jual</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jasa as $b)
                        <tr class="hover:bg-gray-50 align-top">
                            <td class="px-4 py-3">
                                @if($b->foto)
                                    <img src="{{ $b->foto_url }}" alt="{{ $b->nama }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full {{ $b->avatar_warna }} flex items-center justify-center text-white text-sm font-semibold">{{ $b->inisial }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->kode }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $b->nama }}
                                @if(! $b->is_aktif)
                                    <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-500">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->satuan ?: '-' }}</td>
                            <td class="px-4 py-3">
                                @if($b->kategori)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-purple-100 text-purple-700">{{ $b->kategori }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800">{{ formatRupiah($b->harga_jual) }}</td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs">
                                <span class="block truncate" title="{{ $b->keterangan }}">{{ $b->keterangan ?: '-' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('jasa.edit', $b) }}" class="inline-flex items-center justify-center w-11 h-11 text-blue-600 hover:bg-blue-50 rounded-md" aria-label="Edit Jasa {{ $b->kode }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('jasa.destroy', $b) }}">
                                        @csrf @method('DELETE')
                                        <button type="button" aria-label="Hapus Jasa {{ $b->kode }}" class="inline-flex items-center justify-center w-11 h-11 text-red-600 hover:bg-red-50 rounded-md"
                                                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'hapus-jasa-{{ $b->id }}' }))">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                        <x-confirm-dialog name="hapus-jasa-{{ $b->id }}" title="Hapus Jasa"
                                                          message="Yakin ingin menghapus jasa '{{ $b->nama ?: $b->kode }}'? Tindakan ini tidak dapat dibatalkan." />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-16 text-center text-gray-500">Belum ada jasa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jasa->hasPages())<div class="px-4 py-3 border-t border-gray-100">{{ $jasa->links() }}</div>@endif
    </x-card>
</x-app-layout>