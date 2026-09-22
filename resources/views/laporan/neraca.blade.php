<x-app-layout>
    <x-slot name="title">Laporan Neraca</x-slot>

    <x-page-header title="Laporan Neraca" subtitle="Posisi aset, kewajiban, dan modal">
        <div class="flex gap-2">
            <a href="{{ route('laporan.neraca.excel', ['sampai' => $sampai]) }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Excel
            </a>
            <a href="{{ route('laporan.neraca.csv', ['sampai' => $sampai]) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                CSV
            </a>
            <a href="{{ route('laporan.neraca.pdf', ['sampai' => $sampai]) }}" target="_blank" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                PDF
            </a>
        </div>
    </x-page-header>

    <x-card>
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex flex-wrap gap-2 items-end">
                <div>
                    <x-input-label for="sampai" value="Tanggal" />
                    <input type="date" id="sampai" name="sampai" value="{{ $sampai }}" class="mt-1 border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <button class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm rounded-lg">Tampilkan</button>
            </form>
        </div>

        <div class="p-5 max-w-2xl">
            <h3 class="font-bold text-xl text-gray-800 mb-1">{{ setting('nama_perusahaan', 'Perusahaan') }}</h3>
            <p class="text-sm text-gray-500 mb-6">Neraca per {{ formatTanggal($sampai) }}</p>

            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase border-b border-gray-200">
                        <th class="py-2"></th><th class="py-2 text-right">Debit</th><th class="py-2 text-right">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="3" class="pt-4 pb-2 font-semibold text-emerald-700">ASET</td></tr>
                    @forelse($data['aset'] as $a)
                        <tr>
                            <td class="py-1.5 text-gray-700 pl-4">{{ $a['akun']->nama }}</td>
                            <td class="py-1.5 text-right text-gray-700">{{ formatRupiah($a['saldo']) }}</td>
                            <td></td>
                        </tr>
                    @empty
                        <tr><td class="py-1.5 text-gray-400 pl-4">Tidak ada aset</td><td></td><td></td></tr>
                    @endforelse
                    <tr><td class="pt-2 pb-4 pl-4 font-semibold border-b border-gray-100">Total Aset</td><td class="pt-2 pb-4 text-right font-bold text-emerald-700 border-b border-gray-100">{{ formatRupiah($data['total_aset']) }}</td><td></td></tr>

                    <tr><td colspan="3" class="pt-4 pb-2 font-semibold text-amber-700">KEWAJIBAN</td></tr>
                    @forelse($data['kewajiban'] as $k)
                        <tr>
                            <td class="py-1.5 text-gray-700 pl-4">{{ $k['akun']->nama }}</td>
                            <td></td>
                            <td class="py-1.5 text-right text-gray-700">{{ formatRupiah($k['saldo']) }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-1.5 text-gray-400 pl-4">Tidak ada kewajiban</td><td></td><td></td></tr>
                    @endforelse
                    <tr><td class="pt-2 pb-4 pl-4 font-semibold border-b border-gray-100">Total Kewajiban</td><td></td><td class="pt-2 pb-4 text-right font-bold text-amber-700 border-b border-gray-100">{{ formatRupiah($data['total_kewajiban']) }}</td></tr>

                    <tr><td colspan="3" class="pt-4 pb-2 font-semibold text-blue-700">MODAL</td></tr>
                    @forelse($data['modal'] as $m)
                        <tr>
                            <td class="py-1.5 text-gray-700 pl-4">{{ $m['akun']->nama }}</td>
                            <td></td>
                            <td class="py-1.5 text-right text-gray-700">{{ formatRupiah($m['saldo']) }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-1.5 text-gray-400 pl-4">Tidak ada modal</td><td></td><td></td></tr>
                    @endforelse

                    {{-- Laba / Rugi Periode Berjalan sudah tercantum pada baris modal di atas --}}
                    <tr><td class="pt-2 pb-1 pl-4 font-semibold border-t border-gray-100">Total Modal</td><td></td><td class="pt-2 pb-1 text-right font-bold text-blue-700 border-t border-gray-100">{{ formatRupiah($data['total_modal']) }}</td></tr>

                    {{-- Total Kewajiban + Modal --}}
                    <tr class="border-t-2 border-gray-300">
                        <td class="pt-3 pb-2 pl-4 font-bold text-gray-800">Total Kewajiban &amp; Modal</td>
                        <td></td>
                        <td class="pt-3 pb-2 text-right font-bold text-gray-800">{{ formatRupiah($data['total_kewajiban_modal'] ?? ($data['total_kewajiban'] + $data['total_modal'])) }}</td>
                    </tr>

                    {{-- Indikator Balance --}}
                    @php $selisih = $data['selisih'] ?? ($data['total_aset'] - $data['total_kewajiban'] - $data['total_modal']); @endphp
                    <tr>
                        <td colspan="3" class="pt-3 pb-2">
                            @if(abs($selisih) < 1)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Neraca Seimbang (Balanced)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    Selisih: {{ formatRupiah(abs($selisih)) }}
                                </span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </x-card>
</x-app-layout>
