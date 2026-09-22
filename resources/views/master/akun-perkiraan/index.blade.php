<x-app-layout>
    <x-slot name="title">Akun Perkiraan</x-slot>

    <x-page-header title="Akun Perkiraan" subtitle="Daftar Chart of Accounts (COA)">
        <a href="{{ route('akun-perkiraan.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Akun
        </a>
    </x-page-header>

    <x-card>
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex gap-2">
                <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari kode atau nama akun..."
                       class="w-full sm:max-w-xs border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                <button class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm rounded-lg">Cari</button>
            </form>
        </div>

        <x-atur-kolom :options="$kolomOptions" :aktif="$kolomAktif" action="{{ route('akun-perkiraan.simpan-kolom') }}" />

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($kolomAktif as $k)
                            <th class="px-4 py-3 {{ in_array($k, ['kelompok', 'aksi'], true) ? 'text-center' : 'text-left' }} text-xs font-semibold text-gray-500 uppercase">
                                {{ $kolomOptions[$k] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($akun as $a)
                        <tr class="hover:bg-gray-50 {{ $a->is_header ? 'bg-slate-50 font-semibold' : '' }}">
                            @foreach($kolomAktif as $k)
                                @switch($k)
                                    @case('kode')
                                        <td class="px-4 py-3 text-gray-600">{{ $a->kode }}</td>
                                        @break
                                    @case('nama')
                                        <td class="px-4 py-3 {{ $a->is_header ? 'font-semibold text-gray-800' : 'text-gray-700' }}">
                                            <span class="{{ $a->is_header ? '' : 'ms-4' }}">{{ $a->nama }}</span>
                                        </td>
                                        @break
                                    @case('jenis')
                                        <td class="px-4 py-3">
                                            <span class="inline-flex px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">{{ $a->jenis_label }}</span>
                                        </td>
                                        @break
                                    @case('kelompok')
                                        <td class="px-4 py-3 text-center text-xs">
                                            @if($a->is_header)
                                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">Header</span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700">Detail</span>
                                            @endif
                                        </td>
                                        @break
                                    @case('posisi')
                                        <td class="px-4 py-3">
                                            <span class="{{ $a->saldo_normal === 'debit' ? 'text-blue-600' : 'text-orange-600' }}">{{ strtoupper($a->saldo_normal) }}</span>
                                        </td>
                                        @break
                                    @case('aksi')
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center gap-1">
                                                <a href="{{ route('akun-perkiraan.edit', $a) }}" class="inline-flex items-center justify-center w-11 h-11 text-blue-600 hover:bg-blue-50 rounded-md" title="Edit" aria-label="Edit Akun {{ $a->kode }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>
                                                <form method="POST" action="{{ route('akun-perkiraan.destroy', $a) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="button" aria-label="Hapus Akun {{ $a->kode }}" class="inline-flex items-center justify-center w-11 h-11 text-red-600 hover:bg-red-50 rounded-md" title="Hapus"
                                                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'hapus-akun-{{ $a->id }}' }))">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                    <x-confirm-dialog name="hapus-akun-{{ $a->id }}" title="Hapus Akun"
                                                                      message="Yakin ingin menghapus akun '{{ $a->kode }} - {{ $a->nama }}'? Tindakan ini tidak dapat dibatalkan." />
                                                </form>
                                            </div>
                                        </td>
                                        @break
                                @endswitch
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($kolomAktif) }}" class="px-4 py-16 text-center text-gray-500">Belum ada akun perkiraan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($akun->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $akun->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
