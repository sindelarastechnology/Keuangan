<x-app-layout>
    <x-slot name="title">Penjualan</x-slot>

    <x-page-header title="Penjualan" subtitle="Penjualan produk / jasa kepada customer">
        <a href="{{ route('penjualan.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Penjualan Baru
        </a>
    </x-page-header>

    <x-card>
        <x-filter-bar>
            <form method="GET" class="flex flex-col sm:flex-row gap-2">
                <input type="date" name="dari" value="{{ request('dari') }}" class="border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                <input type="date" name="sampai" value="{{ request('sampai') }}" class="border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                <button class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm rounded-lg">Filter</button>
            </form>
        </x-filter-bar>

        <div class="hidden md:block">
            <x-atur-kolom :options="$kolomOptions" :aktif="$kolomAktif" action="{{ route('penjualan.simpan-kolom') }}" />
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($kolomAktif as $k)
                            <th class="px-4 py-3 {{ in_array($k, ['bayar', 'total', 'aksi'], true) ? ($k === 'total' ? 'text-right' : 'text-center') : 'text-left' }} text-xs font-semibold text-gray-500 uppercase">
                                {{ $kolomOptions[$k] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($penjualan as $p)
                        @php($sisa = max(0, (float) ($sisaPeta[$p->id] ?? 0)))
                        <tr class="hover:bg-gray-50">
                            @foreach($kolomAktif as $k)
                                @switch($k)
                                    @case('nomor')
                                        <td class="px-4 py-3">
                                            <div class="font-medium {{ $p->status === 'draft' ? 'text-gray-400' : 'text-emerald-600' }}">{{ $p->nomor }}</div>
                                            @if($p->status === 'pending')
                                                <span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-amber-100 text-amber-700">Draft</span>
                                            @endif
                                            @if($p->status === 'draft')
                                                <span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-gray-200 text-gray-500">Batal</span>
                                            @endif
                                            @if($p->retur->isNotEmpty())
                                                <span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-rose-100 text-rose-700">
                                                    Diretur ×{{ $p->retur->count() }}
                                                </span>
                                            @endif
                                        </td>
                                        @break
                                    @case('tanggal')
                                        <td class="px-4 py-3 text-gray-600">{{ formatTanggalSingkat($p->tanggal) }}</td>
                                        @break
                                    @case('customer')
                                        <td class="px-4 py-3 text-gray-700">{{ $p->customer->nama }}</td>
                                        @break
                                    @case('bayar')
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex px-2 py-1 text-xs rounded-full {{ $p->metode_bayar === 'tunai' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                                {{ ucfirst($p->metode_bayar) }}
                                            </span>
                                            @if($p->metode_bayar === 'kredit' && $p->status === 'posted' && $sisa > 0.005)
                                                <div class="mt-1 inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-amber-100 text-amber-700">Sisa {{ formatRupiah($sisa) }}</div>
                                            @endif
                                        </td>
                                        @break
                                    @case('total')
                                        <td class="px-4 py-3 text-right font-semibold {{ $p->status === 'draft' ? 'text-gray-400' : 'text-emerald-600' }}">{{ formatRupiah($p->total) }}</td>
                                        @break
                                    @case('aksi')
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center gap-1">
                                                <a href="{{ route('penjualan.show', $p) }}" aria-label="Lihat Penjualan {{ $p->nomor }}" class="inline-flex items-center justify-center w-11 h-11 text-slate-600 hover:bg-slate-100 rounded-md">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                                @if($p->metode_bayar === 'kredit' && $p->status === 'posted' && $sisa > 0.005)
                                                    <a href="{{ route('tagihan.piutang.bayar', ['customer' => $p->customer, 'faktur' => $p->id]) }}" title="Terima pembayaran piutang faktur ini" aria-label="Terima Pembayaran Piutang {{ $p->nomor }}" class="inline-flex items-center justify-center w-11 h-11 text-emerald-600 hover:bg-emerald-50 rounded-md">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    </a>
                                                @endif
                                                @if($p->status === 'pending')
                                                    <a href="{{ route('penjualan.edit', $p) }}" title="Edit draft penjualan" aria-label="Edit Draft Penjualan {{ $p->nomor }}" class="inline-flex items-center justify-center w-11 h-11 text-sky-600 hover:bg-sky-50 rounded-md">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    </a>
                                                    <form method="POST" action="{{ route('penjualan.post', $p) }}">
                                                        @csrf
                                                        <button type="submit" title="Posting transaksi (book stok & jurnal)" aria-label="Posting Penjualan {{ $p->nomor }}" class="inline-flex items-center justify-center w-11 h-11 text-emerald-600 hover:bg-emerald-50 rounded-md">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('penjualan.void', $p) }}">
                                                        @csrf
                                                        <button type="button" title="Batalkan draft penjualan" aria-label="Batalkan Draft Penjualan {{ $p->nomor }}" class="inline-flex items-center justify-center w-11 h-11 text-amber-600 hover:bg-amber-50 rounded-md"
                                                                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'batalkan-penjualan-{{ $p->id }}-draft' }))">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                        <x-confirm-dialog name="batalkan-penjualan-{{ $p->id }}-draft" title="Batalkan Draft Penjualan"
                                                                          message="Yakin ingin membatalkan draft penjualan '{{ $p->nomor }}'? Draft tidak memengaruhi stok & jurnal, dan tindakan ini tidak dapat dibatalkan." />
                                                    </form>
                                                @endif
                                                @if($p->status === 'posted')
                                                    <form method="POST" action="{{ route('penjualan.void', $p) }}">
                                                        @csrf
                                                        <button type="button" aria-label="Batalkan Penjualan {{ $p->nomor }}" class="inline-flex items-center justify-center w-11 h-11 text-amber-600 hover:bg-amber-50 rounded-md"
                                                                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'batalkan-penjualan-{{ $p->id }}' }))">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        </button>
                                                        <x-confirm-dialog name="batalkan-penjualan-{{ $p->id }}" title="Batalkan Penjualan"
                                                                          message="Yakin ingin membatalkan penjualan '{{ $p->nomor }}'? Stok & jurnal akan dikembalikan dan tindakan ini tidak dapat dibatalkan." />
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                        @break
                                @endswitch
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($kolomAktif) }}" class="px-4 py-16 text-center text-gray-500">Belum ada penjualan.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
        <div class="md:hidden space-y-3 p-4">
            @forelse($penjualan as $p)
                @php($sisa = max(0, (float) ($sisaPeta[$p->id] ?? 0)))
                <x-mobile-card
                    title="{{ $p->nomor }}"
                    titleClass="text-emerald-600"
                    subtitle="{{ formatTanggalSingkat($p->tanggal) }} · {{ $p->customer->nama }}"
                    amount="{{ formatRupiah($p->total) }}"
                    amountClass="text-emerald-600"
                    :href="route('penjualan.show', $p)"
                >
                    <x-slot name="badge">
                        @if($p->status === 'pending')
                            <span class="inline-flex px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700">Draft</span>
                        @endif
                        @if($p->status === 'draft')
                            <span class="inline-flex px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-500">Batal</span>
                        @endif
                        <span class="inline-flex px-2 py-1 text-xs rounded-full {{ $p->metode_bayar === 'tunai' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ ucfirst($p->metode_bayar) }}
                        </span>
                        @if($p->metode_bayar === 'kredit' && $p->status === 'posted' && $sisa > 0.005)
                            <span class="inline-flex px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700">Sisa {{ formatRupiah($sisa) }}</span>
                        @endif
                    </x-slot>
                    @if($p->retur->isNotEmpty())
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-rose-100 text-rose-700">
                            Diretur ×{{ $p->retur->count() }}
                        </span>
                    @endif
                    <x-slot name="actions">
                        <x-button variant="secondary" size="sm" :href="route('penjualan.show', $p)">Detail</x-button>
                        @if($p->metode_bayar === 'kredit' && $p->status === 'posted' && $sisa > 0.005)
                            <x-button size="sm" :href="route('tagihan.piutang.bayar', ['customer' => $p->customer, 'faktur' => $p->id])">Bayar</x-button>
                        @endif
                        @if($p->status === 'pending')
                            <x-button variant="secondary" size="sm" :href="route('penjualan.edit', $p)">Edit</x-button>
                            <form method="POST" action="{{ route('penjualan.post', $p) }}" class="flex">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-lg border border-emerald-300 text-emerald-700 hover:bg-emerald-50 transition">Posting</button>
                            </form>
                            <form method="POST" action="{{ route('penjualan.void', $p) }}" class="flex">
                                @csrf
                                <button type="button" aria-label="Batalkan Draft Penjualan {{ $p->nomor }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-lg border border-amber-300 text-amber-700 hover:bg-amber-50 transition"
                                        onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'batalkan-penjualan-{{ $p->id }}-draft-mobile' }))">Batalkan</button>
                                <x-confirm-dialog name="batalkan-penjualan-{{ $p->id }}-draft-mobile" title="Batalkan Draft Penjualan"
                                                  message="Yakin ingin membatalkan draft penjualan '{{ $p->nomor }}'? Draft tidak memengaruhi stok & jurnal, dan tindakan ini tidak dapat dibatalkan." />
                            </form>
                        @endif
                        @if($p->status === 'posted')
                            <form method="POST" action="{{ route('penjualan.void', $p) }}" class="flex">
                                @csrf
                                <button type="button" aria-label="Batalkan Penjualan {{ $p->nomor }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-lg border border-amber-300 text-amber-700 hover:bg-amber-50 transition"
                                        onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'batalkan-penjualan-{{ $p->id }}-mobile' }))">Batalkan</button>
                                <x-confirm-dialog name="batalkan-penjualan-{{ $p->id }}-mobile" title="Batalkan Penjualan"
                                                  message="Yakin ingin membatalkan penjualan '{{ $p->nomor }}'? Stok & jurnal akan dikembalikan dan tindakan ini tidak dapat dibatalkan." />
                            </form>
                        @endif
                    </x-slot>
                </x-mobile-card>
            @empty
                <p class="py-10 text-center text-gray-500">Belum ada penjualan.</p>
            @endforelse
        </div>
        @if($penjualan->hasPages())<div class="px-4 py-3 border-t border-gray-100">{{ $penjualan->links() }}</div>@endif
    </x-card>
</x-app-layout>
