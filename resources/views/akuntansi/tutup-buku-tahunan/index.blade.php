<x-app-layout>
    <x-slot name="title">Tutup Buku Tahunan</x-slot>

    <x-page-header title="Tutup Buku Tahunan" subtitle="Tutup seluruh periode dalam satu tahun & pindahkan laba/rugi ke laba ditahan"></x-page-header>

    <x-card>
        <div class="p-5">
            <p class="text-sm text-gray-600 leading-relaxed">
                Tutup buku tahunan membuat satu jurnal penutup untuk seluruh pendapatan &amp; beban selama satu tahun buku,
                lalu menutup keduabelas periode bulan pada tahun tersebut. Pastikan <strong>tidak ada periode bulan yang sudah ditutup</strong>
                dan akun <strong>Laba Ditahan (kode 32)</strong> sudah tersedia di Master Akun sebelum melakukan penutupan.
            </p>
        </div>
    </x-card>

    <x-card class="mt-4" title="Penutupan per Tahun">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tahun</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Periode</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Pendapatan</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Beban</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Laba / Rugi</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tahunan as $t)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $t['tahun'] }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $t['jumlah_periode'] }}/12 ({{ $t['jumlah_tertutup'] }} tertutup)</td>
                            @if($t['tutup'])
                                <td class="px-4 py-3 text-right text-emerald-600">{{ formatRupiah($t['tutup']->total_pendapatan) }}</td>
                                <td class="px-4 py-3 text-right text-red-600">{{ formatRupiah($t['tutup']->total_beban) }}</td>
                                <td class="px-4 py-3 text-right font-semibold {{ $t['tutup']->laba_rugi >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ formatRupiah($t['tutup']->laba_rugi) }}</td>
                            @elseif($t['ringkasan'])
                                <td class="px-4 py-3 text-right text-emerald-600">{{ formatRupiah($t['ringkasan']['total_pendapatan']) }}</td>
                                <td class="px-4 py-3 text-right text-red-600">{{ formatRupiah($t['ringkasan']['total_beban']) }}</td>
                                <td class="px-4 py-3 text-right font-semibold {{ $t['ringkasan']['laba_rugi'] >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ formatRupiah($t['ringkasan']['laba_rugi']) }}</td>
                            @else
                                <td class="px-4 py-3 text-right text-gray-400">-</td>
                                <td class="px-4 py-3 text-right text-gray-400">-</td>
                                <td class="px-4 py-3 text-right text-gray-400">-</td>
                            @endif
                            <td class="px-4 py-3 text-center">
                                @if($t['tutup'])
                                    <span class="inline-flex px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700">Ditutup</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Terbuka</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if(! $t['tutup'] && $t['jumlah_periode'] >= 12 && $t['jumlah_tertutup'] === 0)
                                    <form method="POST" action="{{ route('tutup-buku-tahunan.store') }}">
                                        @csrf
                                        <input type="hidden" name="tahun" value="{{ $t['tahun'] }}">
                                        <button type="button" class="inline-flex items-center gap-1 text-xs font-medium text-amber-700 hover:text-amber-800 border border-amber-300 hover:bg-amber-50 px-3 py-1.5 rounded-lg transition"
                                                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'konfirmasi-tutup-tahunan-{{ $t['tahun'] }}' }))">
                                            Tutup Buku Tahunan
                                        </button>
                                        <x-confirm-dialog name="konfirmasi-tutup-tahunan-{{ $t['tahun'] }}" title="Tutup Buku Tahunan {{ $t['tahun'] }}"
                                                          message="Yakin menutup buku tahun {{ $t['tahun'] }}? Seluruh periode tahun tersebut akan terkunci dan satu jurnal penutup dibuat. Tindakan ini tidak dapat diubah." />
                                    </form>
                                @elseif(! $t['tutup'])
                                    <span class="text-xs text-gray-400">
                                        @if($t['jumlah_periode'] < 12) Periode belum lengkap.
                                        @elseif($t['jumlah_tertutup'] > 0) Ada periode bulan tertutup.
                                        @endif
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">{{ formatTanggalSingkat($t['tutup']->tanggal) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center text-gray-500">Belum ada tahun buku terdata.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>