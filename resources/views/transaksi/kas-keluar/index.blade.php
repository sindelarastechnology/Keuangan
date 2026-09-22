<x-app-layout>
    <x-slot name="title">Kas Keluar</x-slot>

    <x-page-header title="Kas Keluar" subtitle="Catatan pengeluaran kas">
        <a href="{{ route('kas-keluar.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Kas Keluar Baru
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
            <x-atur-kolom :options="$kolomOptions" :aktif="$kolomAktif" action="{{ route('kas-keluar.simpan-kolom') }}" />
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($kolomAktif as $k)
                            <th class="px-4 py-3 {{ in_array($k, ['total', 'aksi'], true) ? ($k === 'total' ? 'text-right' : 'text-center') : 'text-left' }} text-xs font-semibold text-gray-500 uppercase">
                                {{ $kolomOptions[$k] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($kasKeluar as $kk)
                        <tr class="hover:bg-gray-50">
                            @foreach($kolomAktif as $k)
                                @switch($k)
                                    @case('nomor')
                                        <td class="px-4 py-3 font-medium text-red-600">{{ $kk->nomor }}</td>
                                        @break
                                    @case('tanggal')
                                        <td class="px-4 py-3 text-gray-600">{{ formatTanggalSingkat($kk->tanggal) }}</td>
                                        @break
                                    @case('keterangan')
                                        <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $kk->keterangan ?? '-' }}</td>
                                        @break
                                    @case('total')
                                        <td class="px-4 py-3 text-right font-semibold text-red-600">- {{ formatRupiah($kk->grand_total) }}</td>
                                        @break
                                    @case('aksi')
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center gap-1">
                                                <a href="{{ route('kas-keluar.show', $kk) }}" aria-label="Lihat Kas Keluar {{ $kk->nomor }}" class="inline-flex items-center justify-center w-11 h-11 text-slate-600 hover:bg-slate-100 rounded-md">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                                <form method="POST" action="{{ route('kas-keluar.destroy', $kk) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="button" aria-label="Hapus Kas Keluar {{ $kk->nomor }}" class="inline-flex items-center justify-center w-11 h-11 text-red-600 hover:bg-red-50 rounded-md"
                                                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'hapus-kas-keluar-{{ $kk->id }}' }))">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                    <x-confirm-dialog name="hapus-kas-keluar-{{ $kk->id }}" title="Hapus Kas Keluar"
                                                                      message="Yakin ingin menghapus kas keluar '{{ $kk->nomor }}'? Jurnal terkait akan dibatalkan dan tindakan ini tidak dapat dibatalkan." />
                                                </form>
                                            </div>
                                        </td>
                                        @break
                                @endswitch
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($kolomAktif) }}" class="px-4 py-16 text-center text-gray-500">Belum ada kas keluar.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
        <div class="md:hidden space-y-3 p-4">
            @forelse($kasKeluar as $kk)
                <x-mobile-card
                    title="{{ $kk->nomor }}"
                    titleClass="text-red-600"
                    subtitle="{{ formatTanggalSingkat($kk->tanggal) }}"
                    amount="- {{ formatRupiah($kk->grand_total) }}"
                    amountClass="text-red-600"
                    :href="route('kas-keluar.show', $kk)"
                >
                    @if($kk->keterangan)
                        <p class="text-sm text-gray-600 truncate">{{ $kk->keterangan }}</p>
                    @endif
                    <x-slot name="actions">
                        <x-button variant="secondary" size="sm" :href="route('kas-keluar.show', $kk)">Detail</x-button>
                        <form method="POST" action="{{ route('kas-keluar.destroy', $kk) }}" class="flex">
                            @csrf @method('DELETE')
                            <button type="button" aria-label="Hapus Kas Keluar {{ $kk->nomor }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-lg border border-red-300 text-red-600 hover:bg-red-50 transition"
                                    onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'hapus-kas-keluar-{{ $kk->id }}-mobile' }))">Hapus</button>
                            <x-confirm-dialog name="hapus-kas-keluar-{{ $kk->id }}-mobile" title="Hapus Kas Keluar"
                                              message="Yakin ingin menghapus kas keluar '{{ $kk->nomor }}'? Jurnal terkait akan dibatalkan dan tindakan ini tidak dapat dibatalkan." />
                        </form>
                    </x-slot>
                </x-mobile-card>
            @empty
                <p class="py-10 text-center text-gray-500">Belum ada kas keluar.</p>
            @endforelse
        </div>
        @if($kasKeluar->hasPages())<div class="px-4 py-3 border-t border-gray-100">{{ $kasKeluar->links() }}</div>@endif
    </x-card>
</x-app-layout>
