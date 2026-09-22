<x-app-layout>
    <x-slot name="title">Laporan Penyusutan</x-slot>

    <x-page-header title="Laporan Penyusutan" subtitle="Daftar aset, total penyusutan, dan sisa nilai sampai tanggal tertentu">
        <div class="flex gap-2">
            <a href="{{ route('laporan.penyusutan.excel', ['sampai' => $sampai]) }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Excel
            </a>
            <a href="{{ route('laporan.penyusutan.csv', ['sampai' => $sampai]) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                CSV
            </a>
            <a href="{{ route('laporan.penyusutan.pdf', ['sampai' => $sampai]) }}" target="_blank" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
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

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Aset</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tgl Beli</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Harga Beli</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Lama (bln)</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Biaya/Bulan</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total Penyusutan</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Sisa Nilai</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($data['rows'] as $row)
                        @php $aset = $row['aset']; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-600">{{ $aset->kode }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $aset->nama }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $aset->kategori ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ formatTanggalSingkat($aset->tanggal_perolehan) }}</td>
                            <td class="px-4 py-3 text-right text-gray-800">{{ formatRupiah($aset->harga_perolehan) }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ $aset->masa_manfaat_bulan }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ formatRupiah($aset->beban_bulanan) }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ formatRupiah($row['akumulasi']) }}</td>
                            <td class="px-4 py-3 text-right font-medium {{ $row['nilai_buku'] > 0 ? 'text-emerald-700' : 'text-gray-400' }}">{{ formatRupiah($row['nilai_buku']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-16 text-center text-gray-500">Belum ada data aset.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-xs text-gray-500 uppercase">Total</td>
                        <td class="px-4 py-3 text-right text-gray-800">{{ formatRupiah($data['total_perolehan']) }}</td>
                        <td></td>
                        <td></td>
                        <td class="px-4 py-3 text-right text-gray-800">{{ formatRupiah($data['total_akumulasi']) }}</td>
                        <td class="px-4 py-3 text-right text-emerald-700">{{ formatRupiah($data['total_nilai_buku']) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-card>
</x-app-layout>