<x-app-layout>
    <x-slot name="title">Detail Perubahan Stok</x-slot>

    <x-page-header title="Detail Perubahan Stok" :subtitle="$perubahanStok->nomor">
        <div class="flex items-center gap-2">
            <a href="{{ route('perubahan-stok.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </a>
        </div>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <x-card>
            <div class="p-5">
                <h3 class="font-bold text-lg text-emerald-600 mb-4">{{ formatRupiah($perubahanStok->items->sum('subtotal')) }}</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Tanggal</dt><dd class="font-medium">{{ formatTanggal($perubahanStok->tanggal) }}</dd></div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Jenis</dt>
                        <dd><span class="inline-flex px-2 py-1 text-xs rounded-full @if($perubahanStok->jenis === 'rusak' || $perubahanStok->jenis === 'hilang') bg-red-100 text-red-700 @elseif($perubahanStok->jenis === 'salah') bg-amber-100 text-amber-700 @elseif($perubahanStok->jenis === 'lebih') bg-blue-100 text-blue-700 @else bg-gray-100 text-gray-600 @endif">{{ ucfirst($perubahanStok->jenis) }}</span></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd><span class="inline-flex px-2 py-1 text-xs rounded-full {{ $perubahanStok->status === 'posted' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $perubahanStok->status === 'posted' ? 'Diposting' : 'Dibatalkan' }}</span></dd>
                    </div>
                    <div class="flex justify-between"><dt class="text-gray-500">Keterangan</dt><dd class="font-medium text-right">{{ $perubahanStok->keterangan ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Dicatat Oleh</dt><dd class="font-medium">{{ $perubahanStok->creator?->name ?? '-' }}</dd></div>
                </dl>
            </div>
        </x-card>

        <x-card title="Jurnal Terkait">
            @if($perubahanStok->jurnal->count() > 0)
                <div class="p-5">
                    @foreach($perubahanStok->jurnal as $j)
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

    <x-card title="Rincian Perubahan Stok">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Barang</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Arah</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Harga Rata-rata</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Nilai</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($perubahanStok->items as $item)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $item->barang?->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex px-2 py-1 text-xs rounded-full {{ $item->arah === 'masuk' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $item->arah === 'masuk' ? 'Bertambah +' : 'Berkurang −' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ formatKuantitas($item->jumlah) }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ formatRupiah($item->harga) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800">{{ formatRupiah($item->subtotal) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $item->keterangan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr class="bg-gray-100"><td colspan="4" class="px-4 py-3 font-semibold">Total Nilai</td><td class="px-4 py-3 text-right font-bold">{{ formatRupiah($perubahanStok->items->sum('subtotal')) }}</td><td></td></tr>
                </tfoot>
            </table>
        </div>
        @if($perubahanStok->status === 'posted')
            <div class="px-4 py-4 border-t border-gray-100 flex justify-end">
                <form method="POST" action="{{ route('perubahan-stok.void', $perubahanStok) }}">
                    @csrf
                    <button type="button" aria-label="Batalkan Perubahan Stok {{ $perubahanStok->nomor }}" class="inline-flex items-center gap-2 text-sm font-medium text-amber-600 hover:text-amber-700 border border-amber-300 hover:bg-amber-50 px-4 py-2 rounded-lg transition"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'batalkan-perubahan-stok-{{ $perubahanStok->id }}' }))">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Batalkan Perubahan Stok
                    </button>
                    <x-confirm-dialog name="batalkan-perubahan-stok-{{ $perubahanStok->id }}" title="Batalkan Perubahan Stok"
                                      message="Yakin ingin membatalkan perubahan stok '{{ $perubahanStok->nomor }}'? Stok & jurnal akan dikembalikan dan tindakan ini tidak dapat dibatalkan." />
                </form>
            </div>
        @endif
    </x-card>
</x-app-layout>