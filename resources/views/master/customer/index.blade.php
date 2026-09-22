<x-app-layout>
    <x-slot name="title">Customer</x-slot>

    <x-page-header title="Customer" subtitle="Kelola data pelanggan">
        <a href="{{ route('customer.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Customer
        </a>
    </x-page-header>

    <x-card>
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex gap-2">
                <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama customer..."
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
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Saldo Piutang</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customers as $c)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $c->kode }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $c->nama }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $c->telepon ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $c->email ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-emerald-600">{{ formatRupiah($c->saldo_piutang ?? 0) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('customer.edit', $c) }}" class="inline-flex items-center justify-center w-11 h-11 text-blue-600 hover:bg-blue-50 rounded-md" aria-label="Edit Customer {{ $c->kode }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('customer.destroy', $c) }}">
                                        @csrf @method('DELETE')
                                        <button type="button" aria-label="Hapus Customer {{ $c->kode }}" class="inline-flex items-center justify-center w-11 h-11 text-red-600 hover:bg-red-50 rounded-md"
                                                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'hapus-customer-{{ $c->id }}' }))">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                        <x-confirm-dialog name="hapus-customer-{{ $c->id }}" title="Hapus Customer"
                                                          message="Yakin ingin menghapus customer '{{ $c->nama }}'? Tindakan ini tidak dapat dibatalkan." />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-16 text-center text-gray-500">Belum ada customer.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())<div class="px-4 py-3 border-t border-gray-100">{{ $customers->links() }}</div>@endif
    </x-card>
</x-app-layout>
