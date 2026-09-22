<x-app-layout>
    <x-slot name="title">Piutang Customer</x-slot>

    <x-page-header title="Piutang Customer" subtitle="Tagihan piutang yang belum lunas dari seluruh customer">
        <a href="{{ route('tagihan.hutang') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            Lihat Hutang Supplier
        </a>
    </x-page-header>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Piutang Berjalan</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ formatRupiah($total) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Customer dengan Tagihan</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $rows->count() }}</p>
        </div>
    </div>

    <x-card>
        <div class="hidden md:block">
            <x-atur-kolom :options="$kolomOptions" :aktif="$kolomAktif" action="{{ route('tagihan.piutang.simpan-kolom') }}" />
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($kolomAktif as $k)
                            <th class="px-4 py-3 {{ in_array($k, ['faktur', 'sisa', 'aksi'], true) ? ($k === 'sisa' ? 'text-right' : 'text-center') : 'text-left' }} text-xs font-semibold text-gray-500 uppercase">
                                {{ $kolomOptions[$k] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rows as $r)
                        <tr class="hover:bg-gray-50">
                            @foreach($kolomAktif as $k)
                                @switch($k)
                                    @case('kode')
                                        <td class="px-4 py-3 text-gray-700">{{ $r['customer']->kode }}</td>
                                        @break
                                    @case('nama')
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-800">{{ $r['customer']->nama }}</div>
                                            <div class="text-xs text-gray-500">{{ $r['customer']->telepon }}</div>
                                        </td>
                                        @break
                                    @case('faktur')
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700">
                                                {{ $r['jumlah_faktur'] }}
                                            </span>
                                        </td>
                                        @break
                                    @case('sisa')
                                        <td class="px-4 py-3 text-right font-semibold text-emerald-600">{{ formatRupiah($r['sisa']) }}</td>
                                        @break
                                    @case('aksi')
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center">
                                                <a href="{{ route('tagihan.piutang.bayar', $r['customer']) }}"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Terima Pembayaran
                                                </a>
                                            </div>
                                        </td>
                                        @break
                                @endswitch
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($kolomAktif) }}" class="px-4 py-16 text-center text-gray-500">Tidak ada piutang customer yang belum lunas.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
        <div class="md:hidden space-y-3 p-4">
            @forelse($rows as $r)
                <x-mobile-card
                    title="{{ $r['customer']->nama }}"
                    titleClass="text-gray-800"
                    subtitle="{{ $r['jumlah_faktur'] }} faktur terbuka"
                    amount="{{ formatRupiah($r['sisa']) }}"
                    amountClass="text-emerald-600"
                >
                    <x-slot name="actions">
                        <x-button size="sm" :href="route('tagihan.piutang.bayar', $r['customer'])">Terima Pembayaran</x-button>
                    </x-slot>
                </x-mobile-card>
            @empty
                <p class="py-10 text-center text-gray-500">Tidak ada piutang customer yang belum lunas.</p>
            @endforelse
        </div>
    </x-card>
</x-app-layout>