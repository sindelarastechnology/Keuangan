<x-app-layout>
    <x-slot name="title">Detail Kas Masuk</x-slot>

    <x-page-header title="Detail Kas Masuk" :subtitle="$kasMasuk->nomor">
        <a href="{{ route('kas-masuk.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <x-card>
            <div class="p-5">
                <h3 class="font-bold text-lg text-emerald-600 mb-4">+ {{ formatRupiah($kasMasuk->grand_total) }}</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Nomor</dt><dd class="font-medium">{{ $kasMasuk->nomor }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Tanggal</dt><dd class="font-medium">{{ formatTanggal($kasMasuk->tanggal) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Rekening Tujuan</dt><dd class="font-medium">{{ $kasMasuk->rekening->nama }}</dd>
                    </div>
                    @if($kasMasuk->pajak)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Pajak</dt><dd class="font-medium">{{ $kasMasuk->pajak->nama }} ({{ $kasMasuk->pajak->rate }}%) = {{ formatRupiah($kasMasuk->pajak_nominal) }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Keterangan</dt><dd class="font-medium text-right">{{ $kasMasuk->keterangan ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </x-card>

        <x-card title="Jurnal Terkait">
            @if($kasMasuk->jurnal)
                <div class="p-5">
                    <p class="text-xs text-gray-500 mb-3">Jurnal: {{ $kasMasuk->jurnal->nomor }}</p>
                    <table class="w-full text-sm">
                        <thead><tr class="text-left text-xs text-gray-500"><th class="py-1">Akun</th><th class="py-1 text-right">Debit</th><th class="py-1 text-right">Kredit</th></tr></thead>
                        <tbody>
                            @foreach($kasMasuk->jurnal->items as $item)
                                <tr class="border-t border-gray-100">
                                    <td class="py-2">{{ $item->akun->nama }}</td>
                                    <td class="py-2 text-right">{{ $item->debit > 0 ? formatRupiah($item->debit) : '-' }}</td>
                                    <td class="py-2 text-right">{{ $item->kredit > 0 ? formatRupiah($item->kredit) : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="p-5 text-sm text-gray-500">Tidak ada jurnal terkait.</p>
            @endif
        </x-card>
    </div>

    <x-card title="Rincian Penerimaan">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Akun</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Nominal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($kasMasuk->items as $item)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $item->akun->kode }} - {{ $item->akun->nama }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $item->keterangan ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-emerald-600">{{ formatRupiah($item->nominal) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                @if($kasMasuk->pajak_nominal > 0)
                <tfoot class="bg-gray-50">
                    <tr><td colspan="2" class="px-4 py-3 text-right text-gray-600">Pajak</td><td class="px-4 py-3 text-right font-medium text-gray-700">{{ formatRupiah($kasMasuk->pajak_nominal) }}</td></tr>
                </tfoot>
                @endif
                <tfoot class="bg-gray-100">
                    <tr><td colspan="2" class="px-4 py-3 font-semibold">Grand Total</td><td class="px-4 py-3 text-right font-bold text-emerald-600">{{ formatRupiah($kasMasuk->grand_total) }}</td></tr>
                </tfoot>
            </table>
        </div>
    </x-card>
</x-app-layout>
