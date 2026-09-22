<x-app-layout>
    <x-slot name="title">Rekening</x-slot>

    <x-page-header title="Rekening" subtitle="Kelola Kas & Rekening Bank dalam satu tempat">
        <a href="{{ route('rekening.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Rekening
        </a>
    </x-page-header>

    <x-card>
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex flex-col sm:flex-row gap-2">
                <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama rekening..."
                       class="w-full sm:max-w-xs border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                <select name="jenis" class="sm:max-w-[160px] border-gray-300 rounded-lg text-sm">
                    <option value="">Semua Jenis</option>
                    <option value="kas" {{ request('jenis') === 'kas' ? 'selected' : '' }}>Kas</option>
                    <option value="bank" {{ request('jenis') === 'bank' ? 'selected' : '' }}>Bank</option>
                </select>
                <button class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm rounded-lg">Cari</button>
            </form>
        </div>

        <x-atur-kolom :options="$kolomOptions" :aktif="$kolomAktif" action="{{ route('rekening.simpan-kolom') }}" />

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($kolomAktif as $k)
                            <th class="px-4 py-3 {{ in_array($k, ['saldo', 'aksi'], true) ? ($k === 'saldo' ? 'text-right' : 'text-center') : 'text-left' }} text-xs font-semibold text-gray-500 uppercase">{{ $kolomOptions[$k] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rekenings as $r)
                        <tr class="hover:bg-gray-50">
                            @foreach($kolomAktif as $k)
                                @switch($k)
                                    @case('jenis')
                                        <td class="px-4 py-3">
                                            @if($r->jenis === 'bank')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Bank</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Kas</span>
                                            @endif
                                        </td>
                                        @break
                                    @case('nama')
                                        <td class="px-4 py-3 font-medium text-gray-800">{{ $r->nama }}</td>
                                        @break
                                    @case('nomor')
                                        <td class="px-4 py-3 text-gray-600">
                                            @if($r->jenis === 'bank')
                                                {{ $r->nomor_rekening ?? '-' }}{{ $r->nama_pemilik ? ' • ' . $r->nama_pemilik : '' }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        @break
                                    @case('saldo')
                                        <td class="px-4 py-3 text-right font-semibold {{ $r->saldo >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ formatRupiah($r->saldo) }}</td>
                                        @break
                                    @case('aksi')
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center gap-1">
                                                <a href="{{ route('rekening.edit', $r) }}" class="inline-flex items-center justify-center w-11 h-11 text-blue-600 hover:bg-blue-50 rounded-md" aria-label="Edit Rekening {{ $r->nama }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>
                                                <form method="POST" action="{{ route('rekening.destroy', $r) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="button" aria-label="Hapus Rekening {{ $r->nama }}" class="inline-flex items-center justify-center w-11 h-11 text-red-600 hover:bg-red-50 rounded-md"
                                                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'hapus-rekening-{{ $r->id }}' }))">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                    <x-confirm-dialog name="hapus-rekening-{{ $r->id }}" title="Hapus Rekening"
                                                                      message="Yakin ingin menghapus rekening '{{ $r->nama }}'? Tindakan ini tidak dapat dibatalkan." />
                                                </form>
                                            </div>
                                        </td>
                                        @break
                                @endswitch
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($kolomAktif) }}" class="px-4 py-16 text-center text-gray-500">Belum ada rekening.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rekenings->hasPages())<div class="px-4 py-3 border-t border-gray-100">{{ $rekenings->links() }}</div>@endif
    </x-card>
</x-app-layout>
