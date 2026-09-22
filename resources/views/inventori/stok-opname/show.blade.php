<x-app-layout>
    <x-slot name="title">Detail Stok Opname</x-slot>

    <x-page-header title="Detail Stok Opname" :subtitle="$stokOpname->nomor">
        <div class="flex items-center gap-2">
            <a href="{{ route('stok-opname.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </a>
        </div>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <x-card>
            <div class="p-5">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Tanggal</dt><dd class="font-medium">{{ formatTanggal($stokOpname->tanggal) }}</dd></div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd><span class="inline-flex px-2 py-1 text-xs rounded-full {{ $stokOpname->status === 'posted' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $stokOpname->status === 'posted' ? 'Diposting' : 'Dibatalkan' }}</span></dd>
                    </div>
                    <div class="flex justify-between"><dt class="text-gray-500">Keterangan</dt><dd class="font-medium text-right">{{ $stokOpname->keterangan ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Dicatat Oleh</dt><dd class="font-medium">{{ $stokOpname->creator?->name ?? '-' }}</dd></div>
                </dl>
            </div>
        </x-card>

        <x-card title="Jurnal Terkait">
            @if($stokOpname->jurnal->count() > 0)
                <div class="p-5">
                    @foreach($stokOpname->jurnal as $j)
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
                <p class="p-5 text-sm text-gray-500">Tidak ada jurnal terkait (tidak ada selisih).</p>
            @endif
        </x-card>
    </div>

    <x-card title="Rincian Stok Opname">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Barang</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Stok Sistem</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Stok Fisik</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Selisih</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Harga</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Nilai</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($stokOpname->items as $item)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $item->barang?->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ formatKuantitas($item->stok_sistem) }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ formatKuantitas($item->stok_fisik) }}</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $item->selisih > 0 ? 'text-emerald-600' : ($item->selisih < 0 ? 'text-red-600' : 'text-gray-600') }}">{{ formatKuantitas($item->selisih) }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ formatRupiah($item->harga) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800">{{ formatRupiah($item->subtotal) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $item->keterangan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr class="bg-gray-100">
                        <td colspan="5" class="px-4 py-3 font-semibold">Total Nilai</td>
                        <td class="px-4 py-3 text-right font-bold">{{ formatRupiah($stokOpname->items->sum('subtotal')) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @if($stokOpname->status === 'posted')
            <div class="px-4 py-4 border-t border-gray-100 flex justify-end">
                <form method="POST" action="{{ route('stok-opname.void', $stokOpname) }}">
                    @csrf
                    <button type="button" aria-label="Batalkan Stok Opname {{ $stokOpname->nomor }}" class="inline-flex items-center gap-2 text-sm font-medium text-amber-600 hover:text-amber-700 border border-amber-300 hover:bg-amber-50 px-4 py-2 rounded-lg transition"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'batalkan-stok-opname-{{ $stokOpname->id }}' }))">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Batalkan Stok Opname
                    </button>
                    <x-confirm-dialog name="batalkan-stok-opname-{{ $stokOpname->id }}" title="Batalkan Stok Opname"
                                      message="Yakin ingin membatalkan stok opname '{{ $stokOpname->nomor }}'? Stok & jurnal akan dikembalikan dan tindakan ini tidak dapat dibatalkan." />
                </form>
            </div>
        @endif
    </x-card>
</x-app-layout>