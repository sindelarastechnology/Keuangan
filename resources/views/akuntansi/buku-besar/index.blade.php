<x-app-layout>
    <x-slot name="title">Buku Besar</x-slot>

    <x-page-header title="Buku Besar" subtitle="Riwayat mutasi per akun"></x-page-header>

    <x-card>
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex flex-wrap gap-2 items-end">
                <div>
                    <x-input-label for="akun_id" value="Akun (opsional)" />
                    <x-select id="akun_id" name="akun_id" class="mt-1 w-56">
                        <option value="">Semua Akun</option>
                        @foreach($akunList as $a)
                            <option value="{{ $a->id }}" {{ request('akun_id') == $a->id ? 'selected' : '' }}>{{ $a->kode }} - {{ $a->nama }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div>
                    <x-input-label for="dari" value="Dari" />
                    <input type="date" id="dari" name="dari" value="{{ $dari }}" class="mt-1 border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <x-input-label for="sampai" value="Sampai" />
                    <input type="date" id="sampai" name="sampai" value="{{ $sampai }}" class="mt-1 border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <button class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm rounded-lg">Tampilkan</button>
                @if(request()->hasAny(['akun_id', 'dari']))
                    <a href="{{ route('buku-besar.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-lg">Reset</a>
                @endif
            </form>
        </div>

        @if($akun)
            <div class="px-4 py-3 border-b border-gray-100 flex flex-col sm:flex-row sm:justify-between gap-2">
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $akun->kode }} - {{ $akun->nama }}</h3>
                    <p class="text-xs text-gray-500">Saldo normal: {{ ucfirst($akun->saldo_normal) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500">Saldo Akhir</p>
                    <p class="font-bold text-emerald-700 text-lg">{{ formatRupiah($saldoAkhir) }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nomor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Debit</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Kredit</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if(request()->filled('dari') || $saldoAwal != 0)
                            <tr class="bg-slate-50 font-medium">
                                <td class="px-4 py-2.5 text-slate-500">{{ $dari ? formatTanggalSingkat($dari) : '-' }}</td>
                                <td class="px-4 py-2.5 text-slate-400">-</td>
                                <td class="px-4 py-2.5 text-slate-700 italic">Saldo Awal / Kumulatif Sebelumnya</td>
                                <td class="px-4 py-2.5 text-right text-slate-400">-</td>
                                <td class="px-4 py-2.5 text-right text-slate-400">-</td>
                                <td class="px-4 py-2.5 text-right font-semibold text-slate-800">{{ formatRupiah($saldoAwal) }}</td>
                            </tr>
                        @endif
                        @forelse($entries as $e)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">{{ formatTanggalSingkat($e->jtanggal) }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $e->jnomor }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $e->jket ?? '-' }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ $e->debit > 0 ? formatRupiah($e->debit) : '-' }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ $e->kredit > 0 ? formatRupiah($e->kredit) : '-' }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">{{ formatRupiah($e->saldo_berjalan) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">Tidak ada mutasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="overflow-x-auto">
                <x-atur-kolom :options="$kolomOptions" :aktif="$kolomAktif" action="{{ route('buku-besar.simpan-kolom') }}" />
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach($kolomAktif as $k)
                                <th class="px-4 py-3 {{ in_array($k, ['debit', 'kredit', 'saldo'], true) ? 'text-right' : ($k === 'detail' ? 'text-center' : 'text-left') }} text-xs font-semibold text-gray-500 uppercase">
                                    {{ $kolomOptions[$k] }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($entries as $e)
                            @php
                                $totalDebit = (float) $e->total_debit;
                                $totalKredit = (float) $e->total_kredit;
                                $normal = $e->saldo_normal;
                                $saldo = $normal === 'debit' ? ($totalDebit - $totalKredit) : ($totalKredit - $totalDebit);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                @foreach($kolomAktif as $k)
                                    @switch($k)
                                        @case('kode')
                                            <td class="px-4 py-3 font-medium text-gray-800">{{ $e->kode }}</td>
                                            @break
                                        @case('nama')
                                            <td class="px-4 py-3 text-gray-600">{{ $e->nama }}</td>
                                            @break
                                        @case('jenis')
                                            <td class="px-4 py-3">
                                                @php
                                                    $jenisActive = ['aset' => 'bg-blue-100 text-blue-700', 'kewajiban' => 'bg-red-100 text-red-700', 'modal' => 'bg-purple-100 text-purple-700', 'pendapatan' => 'bg-emerald-100 text-emerald-700', 'beban' => 'bg-orange-100 text-orange-700'];
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $jenisActive[$e->jenis] ?? 'bg-gray-100 text-gray-700' }}">
                                                    {{ ucfirst($e->jenis) }}
                                                </span>
                                            </td>
                                            @break
                                        @case('normal')
                                            <td class="px-4 py-3 text-gray-500 text-xs uppercase">{{ $normal }}</td>
                                            @break
                                        @case('debit')
                                            <td class="px-4 py-3 text-right text-gray-700">{{ $totalDebit > 0 ? formatRupiah($totalDebit) : '-' }}</td>
                                            @break
                                        @case('kredit')
                                            <td class="px-4 py-3 text-right text-gray-700">{{ $totalKredit > 0 ? formatRupiah($totalKredit) : '-' }}</td>
                                            @break
                                        @case('saldo')
                                            <td class="px-4 py-3 text-right font-semibold {{ $saldo >= 0 ? 'text-gray-800' : 'text-red-600' }}">{{ formatRupiah($saldo) }}</td>
                                            @break
                                        @case('detail')
                                            <td class="px-4 py-3 text-center">
                                                <a href="{{ route('buku-besar.index', ['akun_id' => $e->id, 'dari' => $dari, 'sampai' => $sampai]) }}" class="text-emerald-600 hover:text-emerald-800 text-xs font-medium">
                                                    Lihat Detail →
                                                </a>
                                            </td>
                                            @break
                                    @endswitch
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="{{ count($kolomAktif) }}" class="px-4 py-10 text-center text-gray-500">Belum ada data jurnal.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>
</x-app-layout>
