<x-app-layout>
    <x-slot name="title">Data Nama</x-slot>

    <x-page-header title="Data Nama" subtitle="Kelola data pelanggan &amp; pemasok">
        <a href="{{ route('data-nama.create', ['entitas' => $entitas]) }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Data Nama
        </a>
    </x-page-header>

    <x-card>
        <div class="px-4 pt-4 flex gap-2">
            @foreach(['customer' => 'Customer', 'supplier' => 'Supplier'] as $nilai => $label)
                <a href="{{ route('data-nama.index', ['entitas' => $nilai]) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-medium {{ $entitas === $nilai ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex gap-2">
                <input type="hidden" name="entitas" value="{{ $entitas }}">
                <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama / kode..."
                       class="w-full sm:max-w-xs border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                <button class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm rounded-lg">Cari</button>
            </form>
        </div>

        <x-atur-kolom :options="$kolomOptions" :aktif="$kolomAktif" action="{{ route('data-nama.simpan-kolom') }}" />

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($kolomAktif as $k)
                            <th class="px-4 py-3 {{ in_array($k, ['saldo', 'aksi'], true) ? ($k === 'saldo' ? 'text-right' : 'text-center') : 'text-left' }} text-xs font-semibold text-gray-500 uppercase">
                                {{ $k === 'saldo' && $entitas === 'supplier' ? 'Saldo Hutang' : ($k === 'saldo' ? 'Saldo Piutang' : $kolomOptions[$k]) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($dataNama as $d)
                        <tr class="hover:bg-gray-50">
                            @foreach($kolomAktif as $k)
                                @switch($k)
                                    @case('kode')
                                        <td class="px-4 py-3 font-medium text-gray-800">{{ $d->kode }}</td>
                                        @break
                                    @case('nama')
                                        <td class="px-4 py-3 text-gray-600">{{ $d->nama }}</td>
                                        @break
                                    @case('telepon')
                                        <td class="px-4 py-3 text-gray-600">{{ $d->telepon ?? '-' }}</td>
                                        @break
                                    @case('email')
                                        <td class="px-4 py-3 text-gray-600">{{ $d->email ?? '-' }}</td>
                                        @break
                                    @case('alamat')
                                        <td class="px-4 py-3 text-gray-600">{{ $d->alamat ?? '-' }}</td>
                                        @break
                                    @case('saldo')
                                        <td class="px-4 py-3 text-right font-semibold {{ $entitas === 'supplier' ? 'text-red-600' : 'text-emerald-600' }}">
                                            {{ formatRupiah($entitas === 'supplier' ? ($d->saldo_hutang ?? 0) : ($d->saldo_piutang ?? 0)) }}
                                        </td>
                                        @break
                                    @case('keterangan')
                                        <td class="px-4 py-3 text-gray-500">{{ $d->keterangan ?? '-' }}</td>
                                        @break
                                    @case('aksi')
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center gap-1">
                                                <a href="{{ route('data-nama.edit', [$entitas, $d]) }}" class="inline-flex items-center justify-center w-11 h-11 text-blue-600 hover:bg-blue-50 rounded-md" aria-label="Edit Data {{ $d->kode }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>
                                                <form method="POST" action="{{ route('data-nama.destroy', [$entitas, $d]) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="button" aria-label="Hapus Data {{ $d->kode }}" class="inline-flex items-center justify-center w-11 h-11 text-red-600 hover:bg-red-50 rounded-md"
                                                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'hapus-data-nama-{{ $d->id }}' }))">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                    <x-confirm-dialog name="hapus-data-nama-{{ $d->id }}" title="Hapus {{ ucfirst($entitas) }}"
                                                                      message="Yakin ingin menghapus '{{ $d->nama }}'? Tindakan ini tidak dapat dibatalkan." />
                                                </form>
                                            </div>
                                        </td>
                                        @break
                                @endswitch
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($kolomAktif) }}" class="px-4 py-16 text-center text-gray-500">Belum ada {{ $entitas === 'supplier' ? 'supplier' : 'customer' }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($dataNama->hasPages())<div class="px-4 py-3 border-t border-gray-100">{{ $dataNama->links() }}</div>@endif
    </x-card>
</x-app-layout>