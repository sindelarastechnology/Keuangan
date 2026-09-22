<x-app-layout>
    <x-slot name="title">Supplier</x-slot>

    <x-page-header title="Supplier" subtitle="Kelola data pemasok">
        <x-button variant="primary" href="{{ route('supplier.create') }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Supplier
        </x-button>
    </x-page-header>

    <x-card>
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex gap-2">
                <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama supplier..."
                       class="w-full sm:max-w-xs border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                <button class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm rounded-lg">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Telepon</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Saldo Hutang</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($suppliers as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $s->kode }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $s->nama }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $s->telepon ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $s->email ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-red-600">{{ formatRupiah($s->saldo_hutang ?? 0) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('supplier.edit', $s) }}" aria-label="Edit {{ $s->nama }}" class="inline-flex items-center justify-center w-11 h-11 text-blue-600 hover:bg-blue-50 rounded-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('supplier.destroy', $s) }}">
                                        @csrf @method('DELETE')
                                        <button type="button" aria-label="Hapus {{ $s->nama }}" class="inline-flex items-center justify-center w-11 h-11 text-red-600 hover:bg-red-50 rounded-md"
                                                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'hapus-supplier-{{ $s->id }}' }))">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                        <x-confirm-dialog name="hapus-supplier-{{ $s->id }}" title="Hapus Supplier"
                                                          message="Yakin ingin menghapus supplier '{{ $s->nama }}'? Tindakan ini tidak dapat dibatalkan." />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-16 text-center text-gray-500">Belum ada supplier.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($suppliers->hasPages())<div class="px-4 py-3 border-t border-gray-100">{{ $suppliers->links() }}</div>@endif
    </x-card>
</x-app-layout>
