<x-app-layout>
    <x-slot name="title">Terima Pembayaran Piutang</x-slot>

    <x-page-header title="Terima Pembayaran Piutang" :subtitle="$customer->nama">
        <a href="{{ route('tagihan.piutang') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Piutang
        </a>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-card title="Faktur yang Belum Lunas">
            <div class="p-5">
                @if($faktur->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Faktur</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Sisa</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($faktur as $f)
                                    <tr class="{{ $f->id === $selectedFakturId ? 'bg-emerald-50' : '' }}">
                                        <td class="px-4 py-2.5">
                                            <a href="{{ route('penjualan.show', $f) }}" class="font-medium text-emerald-600 hover:underline">{{ $f->nomor }}</a>
                                            @if($f->id === $selectedFakturId)
                                                <span class="ml-2 inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-emerald-100 text-emerald-700">Dipilih</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2.5 text-gray-600">{{ formatTanggalSingkat($f->tanggal) }}</td>
                                        <td class="px-4 py-2.5 text-right font-medium">{{ formatRupiah(max(0, (float) $f->sisa_piutang)) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-3 text-xs text-gray-500">Besaran pembayaran dialokasikan otomatis ke faktur terlama terlebih dahulu (FIFO).</p>
                @else
                    <p class="text-sm text-gray-500">Tidak ada faktur yang ter-atribusi lagi. Pembayaran akan mengurangi saldo piutang customer secara umum (resi dari pembukuan lama).</p>
                @endif
            </div>
        </x-card>

        <x-card title="Form Pembayaran">
            <div class="p-5">
                <dl class="space-y-2 text-sm mb-4 pb-4 border-b border-gray-100">
                    <div class="flex justify-between"><dt class="text-gray-500">Total Piutang</dt><dd class="font-semibold text-emerald-600">{{ formatRupiah($sisa) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Sisa Setelah Bayar</dt><dd class="font-medium">{{ formatRupiah(max(0, $sisa - $nominal)) }}</dd></div>
                </dl>

                <form method="POST" action="{{ route('tagihan.piutang.bayar.submit', $customer) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label for="rekening_id" class="block text-sm text-gray-600 mb-1">Terima Melalui</label>
                        <select id="rekening_id" name="rekening_id" required class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach($rekeningList as $rek)
                                <option value="{{ $rek->id }}" {{ old('rekening_id') == $rek->id ? 'selected' : '' }}>{{ $rek->nama }}</option>
                            @endforeach
                        </select>
                        @error('rekening_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="nominal" class="block text-sm text-gray-600 mb-1">Nominal Pembayaran</label>
                        <input id="nominal" name="nominal" type="number" step="0.01" min="0.01" max="{{ $sisa }}"
                               value="{{ old('nominal', number_format($nominal, 2, '.', '')) }}" required
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <p class="mt-1 text-xs text-gray-400">Maksimal {{ formatRupiah($sisa) }}.</p>
                        @error('nominal')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="tanggal" class="block text-sm text-gray-600 mb-1">Tanggal</label>
                        <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', now()->toDateString()) }}" required class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @error('tanggal')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="keterangan" class="block text-sm text-gray-600 mb-1">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Opsional">{{ old('keterangan') }}</textarea>
                        @error('keterangan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-center gap-2 pt-1">
                        <x-button class="inline-flex items-center gap-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 px-3 py-2 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Catat Pembayaran
                        </x-button>
                        <span class="text-xs text-gray-400">Tercatat sebagai Kas Masuk dan dapat dibatalkan dari menu Kas Masuk.</span>
                    </div>
                </form>
            </div>
        </x-card>
    </div>
</x-app-layout>