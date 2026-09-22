<x-app-layout>
    <x-slot name="title">Neraca Saldo</x-slot>

    <x-page-header title="Neraca Saldo" subtitle="Trial Balance">
        <div class="flex gap-2">
            <a href="{{ route('laporan.neraca-saldo.excel', ['sampai' => $sampai]) }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Excel
            </a>
            <a href="{{ route('laporan.neraca-saldo.csv', ['sampai' => $sampai]) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                CSV
            </a>
            <a href="{{ route('laporan.neraca-saldo.pdf', ['sampai' => $sampai]) }}" target="_blank" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                PDF
            </a>
        </div>
    </x-page-header>

    <x-card>
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex flex-wrap gap-2 items-end">
                <div>
                    <x-input-label for="sampai" value="Sampai Tanggal" />
                    <input type="date" id="sampai" name="sampai" value="{{ $sampai }}" class="mt-1 border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <button class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm rounded-lg">Tampilkan</button>
            </form>
        </div>

        <div class="p-5">
            <h3 class="font-bold text-xl text-gray-800 mb-1">{{ setting('nama_perusahaan', 'Perusahaan') }}</h3>
            <p class="text-sm text-gray-500 mb-4">Neraca Saldo per {{ formatTanggalSingkat($sampai) }}</p>

            @if($data['is_balanced'])
                <div class="mb-4 px-3 py-2 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-700">
                    ✓ Neraca Seimbang (Balanced)
                </div>
            @else
                <div class="mb-4 px-3 py-2 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    ⚠ Neraca Tidak Seimbang - Selisih: {{ formatRupiah(abs($data['total_debit'] - $data['total_kredit'])) }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 border-y border-gray-200">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-gray-700">Kode</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-700">Nama Akun</th>
                            <th class="px-3 py-2 text-right font-semibold text-gray-700">Debit</th>
                            <th class="px-3 py-2 text-right font-semibold text-gray-700">Kredit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data['rows'] as $row)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2 text-gray-600">{{ $row['akun']->kode }}</td>
                                <td class="px-3 py-2 text-gray-800">{{ $row['akun']->nama }}</td>
                                <td class="px-3 py-2 text-right font-medium {{ $row['debit'] > 0 ? 'text-slate-700' : 'text-gray-300' }}">
                                    {{ $row['debit'] > 0 ? formatRupiah($row['debit']) : '-' }}
                                </td>
                                <td class="px-3 py-2 text-right font-medium {{ $row['kredit'] > 0 ? 'text-slate-700' : 'text-gray-300' }}">
                                    {{ $row['kredit'] > 0 ? formatRupiah($row['kredit']) : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-8 text-center text-gray-400">Tidak ada data akun</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-300 font-semibold">
                        <tr>
                            <td colspan="2" class="px-3 py-3 text-gray-800">Total</td>
                            <td class="px-3 py-3 text-right text-slate-800">{{ formatRupiah($data['total_debit']) }}</td>
                            <td class="px-3 py-3 text-right text-slate-800">{{ formatRupiah($data['total_kredit']) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </x-card>
</x-app-layout>
