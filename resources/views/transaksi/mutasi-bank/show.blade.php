<x-app-layout>
    <x-slot name="title">Detail Transfer Rekening</x-slot>

    <x-page-header title="Detail Transfer Rekening" :subtitle="$mutasiBank->nomor">
        <a href="{{ route('mutasi-bank.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-card>
            <div class="p-5">
                <h3 class="font-bold text-lg text-slate-700 mb-4">{{ formatRupiah($mutasiBank->nominal) }}</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Tanggal</dt><dd class="font-medium">{{ formatTanggal($mutasiBank->tanggal) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Rekening Asal</dt><dd class="font-medium">{{ $mutasiBank->rekeningAsal->nama }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Rekening Tujuan</dt><dd class="font-medium">{{ $mutasiBank->rekeningTujuan->nama }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Keterangan</dt><dd class="font-medium text-right">{{ $mutasiBank->keterangan ?? '-' }}</dd></div>
                </dl>
            </div>
        </x-card>

        <x-card title="Jurnal Terkait">
            @if($mutasiBank->jurnal)
                <div class="p-5">
                    <p class="text-xs text-gray-500 mb-3">Jurnal: {{ $mutasiBank->jurnal->nomor }}</p>
                    <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="text-left text-xs text-gray-500"><th class="py-1">Akun</th><th class="py-1 text-right">Debit</th><th class="py-1 text-right">Kredit</th></tr></thead>
                        <tbody>
                            @foreach($mutasiBank->jurnal->items as $item)
                                <tr class="border-t border-gray-100">
                                    <td class="py-2">{{ $item->akun->nama }}</td>
                                    <td class="py-2 text-right">{{ $item->debit > 0 ? formatRupiah($item->debit) : '-' }}</td>
                                    <td class="py-2 text-right">{{ $item->kredit > 0 ? formatRupiah($item->kredit) : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            @else
                <p class="p-5 text-sm text-gray-500">Tidak ada jurnal terkait.</p>
            @endif
        </x-card>
    </div>
</x-app-layout>
