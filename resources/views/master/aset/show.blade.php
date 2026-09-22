<x-app-layout>
    <x-slot name="title">{{ $aset->kode }} - {{ $aset->nama }}</x-slot>

    <x-page-header title="{{ $aset->kode }} - {{ $aset->nama }}">
        <div class="flex gap-2">
            <a href="{{ route('aset.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </a>
            @if($aset->status !== 'nonaktif')
                <a href="{{ route('aset.edit', $aset) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
            @endif
        </div>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            <x-card title="Informasi Aset">
                @if($aset->foto)
                    <div class="p-5 pb-0">
                        <img src="{{ asset('storage/'.$aset->foto) }}" alt="Foto {{ $aset->nama }}" class="w-full max-h-56 object-cover rounded-xl" loading="lazy">
                    </div>
                @endif
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Status</span>
                        @if($aset->status === 'selesai')
                            <span class="font-medium text-indigo-600">Selesai</span>
                        @elseif($aset->status === 'nonaktif')
                            <span class="font-medium text-gray-600">Nonaktif</span>
                        @elseif($aset->sudah_disusutkan_penuh)
                            <span class="font-medium text-blue-600">Tuntas Disusutkan</span>
                        @else
                            <span class="font-medium text-emerald-600">Aktif</span>
                        @endif
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Jenis Barang</span>
                        <span class="font-medium text-gray-800">{{ $aset->kategori ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Tanggal Beli</span>
                        <span class="font-medium text-gray-800">{{ formatTanggal($aset->tanggal_perolehan) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Taruh di</span>
                        <span class="font-medium text-gray-800">{{ $aset->lokasi ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Harga Beli</span>
                        <span class="font-medium text-gray-800">{{ formatRupiah($aset->harga_perolehan) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Perkiraan Harga Jual Nanti</span>
                        <span class="font-medium text-gray-800">{{ formatRupiah($aset->nilai_residu ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Lama Dipakai</span>
                        <span class="font-medium text-gray-800">{{ $aset->masa_manfaat_bulan }} bulan</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Biaya/Bulan</span>
                        <span class="font-medium text-gray-800">{{ formatRupiah($aset->beban_bulanan) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Penyusutan Mulai</span>
                        <span class="font-medium text-gray-800">{{ $aset->mulai_periode }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Pembayaran</span>
                        <span class="font-medium text-gray-800">
                            @if($aset->rekening)
                                {{ $aset->rekening->nama }}
                            @elseif($aset->supplier)
                                {{ $aset->supplier->nama }} (masih utang)
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Akun Aset</span>
                        <span class="font-medium text-gray-800">{{ $aset->akunAset->kode }} - {{ $aset->akunAset->nama }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Akun Akumulasi</span>
                        <span class="font-medium text-gray-800">{{ $aset->akunAkumulasi->kode }} - {{ $aset->akunAkumulasi->nama }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Akun Beban</span>
                        <span class="font-medium text-gray-800">{{ $aset->akunBeban->kode }} - {{ $aset->akunBeban->nama }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Sumber Dana</span>
                        <span class="font-medium text-gray-800">{{ $aset->sumberDana ? $aset->sumberDana->kode.' - '.$aset->sumberDana->nama : '-' }}</span>
                    </div>
                    @if($aset->deskripsi)
                        <div class="sm:col-span-2 border-b border-gray-100 pb-2">
                            <span class="text-gray-500 me-2">Deskripsi</span>
                            <span class="font-medium text-gray-800">{{ $aset->deskripsi }}</span>
                        </div>
                    @endif
                    @if($jurnalPerolehan)
                        <div class="sm:col-span-2 pt-1">
                            <a href="{{ route('jurnal.show', $jurnalPerolehan) }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 hover:text-emerald-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Lihat jurnal pembelian ({{ $jurnalPerolehan->nomor }})
                            </a>
                        </div>
                    @endif
                </div>
            </x-card>

            @if($aset->status === 'nonaktif' && $aset->disposisi_alasan)
                <x-card title="Informasi Penghapusan">
                    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div class="flex justify-between border-b border-gray-100 pb-2">
                            <span class="text-gray-500">Alasan</span>
                            <span class="font-medium text-gray-800">{{ \App\Services\AsetDisposisiService::ALASAN[$aset->disposisi_alasan] ?? $aset->disposisi_alasan }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 pb-2">
                            <span class="text-gray-500">Tanggal</span>
                            <span class="font-medium text-gray-800">{{ formatTanggal($aset->disposisi_tanggal) }}</span>
                        </div>
                        @if($aset->disposisi_alasan === 'dijual')
                            <div class="flex justify-between border-b border-gray-100 pb-2">
                                <span class="text-gray-500">Harga Jual</span>
                                <span class="font-medium text-gray-800">{{ formatRupiah($aset->disposisi_harga_jual) }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-100 pb-2">
                                <span class="text-gray-500">Hasil</span>
                                <span class="font-medium {{ ($aset->disposisi_laba ?? 0) < 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                    {{ ($aset->disposisi_laba ?? 0) < 0 ? 'Rugi' : 'Laba' }} {{ formatRupiah(abs($aset->disposisi_laba ?? 0)) }}
                                </span>
                            </div>
                        @endif
                        @if($jurnalPenghapusan)
                            <div class="sm:col-span-2 pt-1">
                                <a href="{{ route('jurnal.show', $jurnalPenghapusan) }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 hover:text-emerald-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Lihat jurnal penghapusan ({{ $jurnalPenghapusan->nomor }})
                                </a>
                            </div>
                        @endif
                    </div>
                </x-card>
            @endif
        </div>

        <div class="space-y-4">
            <x-card title="Ringkasan Penyusutan">
                <div class="p-5 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Penyusutan</span>
                        <span class="font-semibold text-gray-800">{{ formatRupiah($aset->akumulasi) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Sisa Nilai</span>
                        <span class="font-semibold text-emerald-700">{{ formatRupiah($aset->nilai_buku) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Sisa Penyusutan</span>
                        <span class="font-medium {{ $aset->nilai_sisa_disusutkan > 0 ? 'text-gray-800' : 'text-blue-600' }}">{{ formatRupiah($aset->nilai_sisa_disusutkan) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Bulan Berjalan</span>
                        <span class="font-medium text-gray-800">{{ $aset->penyusutan->count() }} bulan</span>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <x-card class="mt-4" title="Riwayat Penyusutan">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Periode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Biaya</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total Penyusutan Setelah</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Sisa Nilai Setelah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jurnal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($aset->penyusutan as $p)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $p->periode }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ formatTanggalSingkat($p->tanggal) }}</td>
                            <td class="px-4 py-3 text-right text-gray-800">{{ formatRupiah($p->beban) }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ formatRupiah($p->akumulasi_setelah) }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ formatRupiah($p->nilai_buku_setelah) }}</td>
                            <td class="px-4 py-3">
                                @if($p->jurnal)
                                    <a href="{{ route('jurnal.show', $p->jurnal) }}" class="text-emerald-700 hover:underline">{{ $p->jurnal->nomor }}</a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $p->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center text-gray-500">Belum ada penyusutan. Jalankan proses penyusutan pada periode bersangkutan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>