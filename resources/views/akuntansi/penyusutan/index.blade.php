<x-app-layout>
    <x-slot name="title">Proses Penyusutan</x-slot>

    <x-page-header title="Proses Penyusutan" subtitle="Hitung & catat beban penyusutan per periode (garis lurus)">
        <a href="{{ route('penyusutan.kalkulator') }}" class="inline-flex items-center gap-2 bg-slate-600 hover:bg-slate-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m-6 4h6m-3 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Kalkulator
        </a>
    </x-page-header>

    <x-card>
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex flex-wrap gap-2 items-end">
                <div>
                    <x-input-label for="periode" value="Periode" />
                    <input type="month" id="periode" name="periode" value="{{ $periode }}" class="mt-1 border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <button class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm rounded-lg">Tampilkan</button>
            </form>
        </div>
    </x-card>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mt-4">
        <x-card>
            <div class="p-4">
                <p class="text-xs text-gray-500 mb-1">Aset Aktif</p>
                <p class="text-xl font-bold text-gray-800">{{ number_format($ringkasan['total_aset'], 0, ',', '.') }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="p-4">
                <p class="text-xs text-gray-500 mb-1">Total Harga Beli</p>
                <p class="text-sm font-bold text-gray-800">{{ formatRupiah($ringkasan['nilai_perolehan']) }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="p-4">
                <p class="text-xs text-gray-500 mb-1">Total Penyusutan</p>
                <p class="text-sm font-bold text-gray-800">{{ formatRupiah($ringkasan['total_akumulasi']) }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="p-4">
                <p class="text-xs text-gray-500 mb-1">Sisa Nilai</p>
                <p class="text-sm font-bold text-emerald-700">{{ formatRupiah($ringkasan['total_nilai_buku']) }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="p-4">
                <p class="text-xs text-gray-500 mb-1">Beban {{ $periode }}</p>
                <p class="text-sm font-bold {{ $ringkasan['beban_periode'] > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ formatRupiah($ringkasan['beban_periode']) }}</p>
            </div>
        </x-card>
    </div>

    <x-card class="mt-4">
        <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="text-sm text-gray-600">
                Periode <strong>{{ $periode }}</strong>
                @if($ringkasan['sudah_diproses'])
                    - sudah diproses
                @else
                    - belum diproses
                @endif
            </div>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('penyusutan.proses') }}">
                    @csrf
                    <input type="hidden" name="periode" value="{{ $periode }}">
                    <button type="button" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'konfirmasi-proses-penyusutan' }))">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Proses Penyusutan {{ $periode }}
                    </button>
                    <x-confirm-dialog name="konfirmasi-proses-penyusutan" title="Proses Penyusutan"
                                      message="Yakin memproses penyusutan untuk periode '{{ $periode }}'? Jurnal beban akan dibuat otomatis." />
                </form>
                @if($ringkasan['sudah_diproses'])
                    <form method="POST" action="{{ route('penyusutan.batalkan') }}">
                        @csrf
                        <input type="hidden" name="periode" value="{{ $periode }}">
                        <button type="button" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition"
                                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'konfirmasi-batal-penyusutan' }))">
                            Batalkan
                        </button>
                        <x-confirm-dialog name="konfirmasi-batal-penyusutan" title="Batalkan Penyusutan"
                                          message="Yakin membatalkan penyusutan periode '{{ $periode }}'? Jurnal akan dibalik dan riwayat dihapus." />
                    </form>
                @endif
            </div>
        </div>

        <x-atur-kolom :options="$kolomOptions" :aktif="$kolomAktif" action="{{ route('penyusutan.simpan-kolom') }}" />

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($kolomAktif as $k)
                            <th class="px-4 py-3 {{ in_array($k, ['harga', 'akumulasi', 'nilai_buku', 'beban'], true) ? 'text-right' : 'text-left' }} text-xs font-semibold text-gray-500 uppercase">
                                {{ $kolomOptions[$k] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($asets as $a)
                        @php
                            $belumMulai = $periode < $a->mulai_periode;
                            if ($a->status === 'selesai') {
                                $status = ['label' => 'Selesai', 'class' => 'bg-indigo-100 text-indigo-700'];
                            } elseif ($a->status === 'nonaktif') {
                                $status = ['label' => 'Nonaktif', 'class' => 'bg-gray-100 text-gray-600'];
                            } elseif ($a->sudah_disusutkan_penuh) {
                                $status = ['label' => 'Tuntas', 'class' => 'bg-blue-100 text-blue-700'];
                            } elseif ($belumMulai) {
                                $status = ['label' => 'Belum Mulai ('.$a->mulai_periode.')', 'class' => 'bg-gray-100 text-gray-600'];
                            } elseif ($a->penyusutan->isNotEmpty()) {
                                $status = ['label' => 'Sudah Diproses', 'class' => 'bg-emerald-100 text-emerald-700'];
                            } else {
                                $status = ['label' => 'Siap Diproses', 'class' => 'bg-amber-100 text-amber-700'];
                            }
                        @endphp
                        <tr class="hover:bg-gray-50">
                            @foreach($kolomAktif as $k)
                                @switch($k)
                                    @case('kode')
                                        <td class="px-4 py-3 text-gray-600">{{ $a->kode }}</td>
                                        @break
                                    @case('nama')
                                        <td class="px-4 py-3 font-medium text-gray-800">{{ $a->nama }}</td>
                                        @break
                                    @case('harga')
                                        <td class="px-4 py-3 text-right text-gray-800">{{ formatRupiah($a->harga_perolehan) }}</td>
                                        @break
                                    @case('akumulasi')
                                        <td class="px-4 py-3 text-right text-gray-600">{{ formatRupiah($a->akumulasi) }}</td>
                                        @break
                                    @case('nilai_buku')
                                        <td class="px-4 py-3 text-right font-medium text-gray-800">{{ formatRupiah($a->nilai_buku) }}</td>
                                        @break
                                    @case('beban')
                                        <td class="px-4 py-3 text-right text-gray-600">{{ formatRupiah($a->beban_bulanan) }}</td>
                                        @break
                                    @case('status')
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex px-2 py-1 text-xs rounded-full {{ $status['class'] }}">{{ $status['label'] }}</span>
                                        </td>
                                        @break
                                @endswitch
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($kolomAktif) }}" class="px-4 py-16 text-center text-gray-500">Belum ada aset tetap.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>