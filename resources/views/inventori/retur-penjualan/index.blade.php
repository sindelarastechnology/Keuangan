<x-app-layout>
    <x-slot name="title">Retur Penjualan</x-slot>

    <x-page-header title="Retur Penjualan" subtitle="Catat barang yang dikembalikan oleh pelanggan">
        <a href="{{ route('retur-penjualan.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Retur Baru
        </a>
    </x-page-header>

    <x-card>
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex flex-col sm:flex-row gap-2">
                <input type="date" name="dari" value="{{ request('dari') }}" class="border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                <input type="date" name="sampai" value="{{ request('sampai') }}" class="border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                <button class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm rounded-lg">Filter</button>
            </form>
        </div>

        <x-atur-kolom :options="$kolomOptions" :aktif="$kolomAktif" action="{{ route('retur-penjualan.simpan-kolom') }}" />

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($kolomAktif as $k)
                            <th class="px-4 py-3 {{ in_array($k, ['jumlah', 'nilai', 'status', 'aksi'], true) ? ($k === 'jumlah' || $k === 'nilai' ? 'text-right' : 'text-center') : 'text-left' }} text-xs font-semibold text-gray-500 uppercase">
                                {{ $kolomOptions[$k] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($returPenjualan as $p)
                        <tr class="hover:bg-gray-50">
                            @foreach($kolomAktif as $k)
                                @switch($k)
                                    @case('nomor')
                                        <td class="px-4 py-3 font-medium text-emerald-600">{{ $p->nomor }}</td>
                                        @break
                                    @case('tanggal')
                                        <td class="px-4 py-3 text-gray-600">{{ formatTanggalSingkat($p->tanggal) }}</td>
                                        @break
                                    @case('penjualan')
                                        <td class="px-4 py-3 text-gray-700">{{ $p->penjualan?->nomor ?? '-' }}</td>
                                        @break
                                    @case('pelanggan')
                                        <td class="px-4 py-3 text-gray-700">{{ $p->penjualan?->customer?->nama ?? '-' }}</td>
                                        @break
                                    @case('jumlah')
                                        <td class="px-4 py-3 text-right text-gray-600">{{ formatKuantitas($p->items->sum('jumlah')) }}</td>
                                        @break
                                    @case('nilai')
                                        <td class="px-4 py-3 text-right font-semibold text-emerald-600">{{ formatRupiah($p->items->sum('subtotal')) }}</td>
                                        @break
                                    @case('status')
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex px-2 py-1 text-xs rounded-full {{ $p->status === 'posted' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                                {{ $p->status === 'posted' ? 'Diposting' : 'Dibatalkan' }}
                                            </span>
                                        </td>
                                        @break
                                    @case('aksi')
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center gap-1">
                                                <a href="{{ route('retur-penjualan.show', $p) }}" aria-label="Lihat Retur Penjualan {{ $p->nomor }}" class="inline-flex items-center justify-center w-11 h-11 text-slate-600 hover:bg-slate-100 rounded-md">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                                @if($p->status === 'posted')
                                                    <form method="POST" action="{{ route('retur-penjualan.void', $p) }}">
                                                        @csrf
                                                        <button type="button" aria-label="Batalkan Retur Penjualan {{ $p->nomor }}" class="inline-flex items-center justify-center w-11 h-11 text-amber-600 hover:bg-amber-50 rounded-md"
                                                                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'batalkan-retur-penjualan-{{ $p->id }}' }))">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        </button>
                                                        <x-confirm-dialog name="batalkan-retur-penjualan-{{ $p->id }}" title="Batalkan Retur Penjualan"
                                                                          message="Yakin ingin membatalkan retur penjualan '{{ $p->nomor }}'? Stok & jurnal akan dikembalikan dan tindakan ini tidak dapat dibatalkan." />
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                        @break
                                @endswitch
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($kolomAktif) }}" class="px-4 py-16 text-center text-gray-500">Belum ada retur penjualan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($returPenjualan->hasPages())<div class="px-4 py-3 border-t border-gray-100">{{ $returPenjualan->links() }}</div>@endif
    </x-card>
</x-app-layout>