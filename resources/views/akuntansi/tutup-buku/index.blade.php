<x-app-layout>
    <x-slot name="title">Tutup Buku</x-slot>

    <x-page-header title="Tutup Buku" subtitle="Tutup periode & pindahkan laba/rugi ke laba ditahan"></x-page-header>

    @if($periode && !$periode->is_closed)
        <x-card>
            <div class="p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-lg text-gray-800">Periode Aktif: {{ $periode->label }}</h3>
                        <p class="text-sm text-gray-500">Periode ini masih terbuka. Tutup buku akan mengunci periode dan memindahkan laba/rugi ke akun Laba Ditahan.</p>
                    </div>
                    <form method="POST" action="{{ route('tutup-buku.store') }}">
                        @csrf
                        <input type="text" name="keterangan" placeholder="Keterangan (opsional)" class="border-gray-300 rounded-lg text-sm mb-2 w-full sm:w-64">
                        <button type="button" class="w-full bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition"
                                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'konfirmasi-tutup-buku' }))">Tutup Buku {{ $periode->label }}</button>
                        <x-confirm-dialog name="konfirmasi-tutup-buku" title="Tutup Buku"
                                          message="Yakin menutup buku periode '{{ $periode->label }}'? Tindakan ini mengunci periode dan tidak dapat diubah." />
                    </form>
                </div>
            </div>
        </x-card>

        @if($ringkasan)
            <x-card class="mt-4" title="Ringkasan Laba Rugi Periode">
                <div class="p-5 space-y-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Periode</span>
                        <span class="font-medium">{{ formatTanggalSingkat($ringkasan['dari']) }} - {{ formatTanggalSingkat($ringkasan['sampai']) }}</span>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm border-b border-gray-100 pb-2">
                            <span class="text-gray-500">Total Pendapatan</span>
                            <span class="font-medium text-emerald-600">{{ formatRupiah($ringkasan['total_pendapatan']) }}</span>
                        </div>
                        <div class="flex justify-between text-sm pt-2">
                            <span class="text-gray-500">Total Beban</span>
                            <span class="font-medium text-red-600">{{ formatRupiah($ringkasan['total_beban']) }}</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center border-t border-gray-200 pt-3">
                        <span class="font-semibold text-gray-800">Laba / Rugi Bersih</span>
                        <span class="font-bold text-xl {{ $ringkasan['laba_rugi'] >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ formatRupiah($ringkasan['laba_rugi']) }}</span>
                    </div>
                </div>
            </x-card>
        @endif
    @elseif($periode && $periode->is_closed)
        <x-card>
            <div class="p-10 text-center">
                <p class="text-emerald-600 font-semibold text-lg mb-2">Periode {{ $periode->label }} sudah ditutup.</p>
                <p class="text-sm text-gray-500">Buka periode baru melalui menu Periode untuk melanjutkan.</p>
            </div>
        </x-card>
    @else
        <x-card>
            <div class="p-10 text-center">
                <p class="text-gray-600 font-semibold text-lg mb-2">Tidak ada periode aktif.</p>
                <p class="text-sm text-gray-500">Buka periode terlebih dahulu melalui menu Periode.</p>
            </div>
        </x-card>
    @endif

    <x-card class="mt-4" title="Riwayat Tutup Buku">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Periode</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Pendapatan</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Beban</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Laba / Rugi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($riwayat as $r)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-600">{{ formatTanggalSingkat($r->tanggal) }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $r->periode->label }}</td>
                            <td class="px-4 py-3 text-right text-emerald-600">{{ formatRupiah($r->total_pendapatan) }}</td>
                            <td class="px-4 py-3 text-right text-red-600">{{ formatRupiah($r->total_beban) }}</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $r->laba_rugi >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ formatRupiah($r->laba_rugi) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $r->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">Belum ada tutup buku.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>
