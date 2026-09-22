<x-app-layout>
    <x-slot name="title">Detail Transfer Barang</x-slot>

    <x-page-header title="Detail Transfer Barang" :subtitle="$transferGudang->nomor">
        <div class="flex items-center gap-2">
            <a href="{{ route('transfer-gudang.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </a>
        </div>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <x-card>
            <div class="p-5">
                <h3 class="font-bold text-lg text-emerald-600 mb-4">{{ formatKuantitas($transferGudang->items->sum('jumlah')) }} qty dipindahkan</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Tanggal</dt><dd class="font-medium">{{ formatTanggal($transferGudang->tanggal) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Gudang Asal</dt><dd class="font-medium">{{ $transferGudang->asal?->nama ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Gudang Tujuan</dt><dd class="font-medium">{{ $transferGudang->tujuan?->nama ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Status</dt><dd><span class="inline-flex px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700">Diposting</span></dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Keterangan</dt><dd class="font-medium text-right">{{ $transferGudang->keterangan ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Dicatat Oleh</dt><dd class="font-medium">{{ $transferGudang->creator?->name ?? '-' }}</dd></div>
                </dl>
            </div>
        </x-card>

        <x-card title="Info Transfer">
            <div class="p-5">
                <p class="text-sm text-gray-500 leading-relaxed">
                    Transfer barang antar gudang hanya memindahkan stok dari gudang asal ke gudang tujuan.
                    Stok total barang, nilai persediaan, maupun jurnal tidak terpengaruh.
                </p>
            </div>
        </x-card>
    </div>

    <x-card title="Rincian Transfer">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Barang</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($transferGudang->items as $item)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $item->barang?->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800">{{ formatKuantitas($item->jumlah) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $item->keterangan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr class="bg-gray-100"><td class="px-4 py-3 font-semibold">Total</td><td class="px-4 py-3 text-right font-bold">{{ formatKuantitas($transferGudang->items->sum('jumlah')) }}</td><td></td></tr>
                </tfoot>
            </table>
        </div>
    </x-card>
</x-app-layout>