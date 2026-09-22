<x-app-layout>
    <x-slot name="title">Jurnal Umum</x-slot>

    <x-page-header title="Jurnal Umum" subtitle="Seluruh jurnal double-entry">
        <a href="{{ route('jurnal.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Jurnal Manual
        </a>
    </x-page-header>

    <x-card>
        <x-filter-bar>
            <form method="GET" class="flex flex-wrap gap-2">
                <input type="date" name="dari" value="{{ request('dari') }}" class="border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                <input type="date" name="sampai" value="{{ request('sampai') }}" class="border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                <x-select name="tipe" class="w-48">
                    <option value="">Semua Tipe</option>
                    @foreach($tipes as $key => $label)
                        <option value="{{ $key }}" {{ request('tipe') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </x-select>
                <button class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm rounded-lg">Filter</button>
            </form>
        </x-filter-bar>

        <x-atur-kolom :options="$kolomOptions" :aktif="$kolomAktif" action="{{ route('jurnal.simpan-kolom') }}" />

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($kolomAktif as $k)
                            <th class="px-4 py-3 {{ in_array($k, ['debit', 'kredit'], true) ? 'text-right' : (in_array($k, ['status', 'aksi'], true) ? 'text-center' : 'text-left') }} text-xs font-semibold text-gray-500 uppercase">
                                {{ $kolomOptions[$k] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jurnal as $j)
                        <tr class="hover:bg-gray-50">
                            @foreach($kolomAktif as $k)
                                @switch($k)
                                    @case('nomor')
                                        <td class="px-4 py-3 font-medium text-gray-800">{{ $j->nomor }}</td>
                                        @break
                                    @case('tanggal')
                                        <td class="px-4 py-3 text-gray-600">{{ formatTanggalSingkat($j->tanggal) }}</td>
                                        @break
                                    @case('keterangan')
                                        <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $j->keterangan ?? '-' }}</td>
                                        @break
                                    @case('tipe')
                                        <td class="px-4 py-3">
                                            <span class="inline-flex px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">{{ $j->tipe_label }}</span>
                                        </td>
                                        @break
                                    @case('debit')
                                        <td class="px-4 py-3 text-right text-gray-700">{{ formatRupiah($j->total_debit) }}</td>
                                        @break
                                    @case('kredit')
                                        <td class="px-4 py-3 text-right text-gray-700">{{ formatRupiah($j->total_kredit) }}</td>
                                        @break
                                    @case('status')
                                        <td class="px-4 py-3 text-center">
                                            @if($j->approval_status === 'approved')
                                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700">Disetujui</span>
                                            @elseif($j->approval_status === 'rejected')
                                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-rose-100 text-rose-700">Ditolak</span>
                                            @elseif($j->approval_status === 'pending_review')
                                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700">Menunggu</span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-500">Draf</span>
                                            @endif
                                        </td>
                                        @break
                                    @case('aksi')
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center gap-1">
                                                <a href="{{ route('jurnal.show', $j) }}" aria-label="Lihat Jurnal {{ $j->nomor }}" class="inline-flex items-center justify-center w-11 h-11 text-slate-600 hover:bg-slate-100 rounded-md">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                                @if($j->is_posted)
                                                <form method="POST" action="{{ route('jurnal.void', $j) }}">
                                                    @csrf
                                                    <button type="button" aria-label="Batalkan Jurnal {{ $j->nomor }}" class="inline-flex items-center justify-center w-11 h-11 text-amber-600 hover:bg-amber-50 rounded-md"
                                                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'batalkan-jurnal-{{ $j->id }}' }))">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    </button>
                                                    <x-confirm-dialog name="batalkan-jurnal-{{ $j->id }}" title="Batalkan Jurnal"
                                                                      message="Yakin ingin membatalkan jurnal '{{ $j->nomor }}'? Akan dibuat jurnal pembalik dan tindakan ini tidak dapat dibatalkan." />
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                        @break
                                @endswitch
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($kolomAktif) }}" class="px-4 py-16 text-center text-gray-500">Belum ada jurnal.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jurnal->hasPages())<div class="px-4 py-3 border-t border-gray-100">{{ $jurnal->links() }}</div>@endif
    </x-card>
</x-app-layout>
