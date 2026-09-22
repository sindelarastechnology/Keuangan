<x-app-layout>
    <x-slot name="title">Buku Pembantu Hutang</x-slot>

    <x-page-header title="Buku Pembantu Hutang" subtitle="Mutasi hutang per supplier"></x-page-header>

    <x-card>
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex flex-wrap gap-2 items-end">
                <div>
                    <x-input-label for="supplier_id" value="Supplier (opsional)" />
                    <x-select id="supplier_id" name="supplier_id" class="mt-1 w-56">
                        <option value="">Semua Supplier</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
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
                @if(request()->hasAny(['supplier_id', 'dari']))
                    <a href="{{ route('bb-hutang.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-lg">Reset</a>
                @endif
            </form>
        </div>

        @if($supplier)
            <div class="px-4 py-3 border-b border-gray-100 flex flex-col sm:flex-row sm:justify-between gap-2">
                <h3 class="font-semibold text-gray-800">{{ $supplier->nama }}</h3>
                <div class="text-right">
                    <p class="text-xs text-gray-500">Saldo Hutang</p>
                    <p class="font-bold text-red-600 text-lg">{{ formatRupiah($saldoAkhir) }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Debit (Bayar)</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Kredit (Beli)</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if(request()->filled('dari') || $saldoAwal != 0)
                            <tr class="bg-slate-50 font-medium">
                                <td class="px-4 py-2.5 text-slate-500">{{ $dari ? formatTanggalSingkat($dari) : '-' }}</td>
                                <td class="px-4 py-2.5 text-slate-700 italic">Saldo Awal Hutang</td>
                                <td class="px-4 py-2.5 text-right text-slate-400">-</td>
                                <td class="px-4 py-2.5 text-right text-slate-400">-</td>
                                <td class="px-4 py-2.5 text-right font-semibold text-slate-800">{{ formatRupiah($saldoAwal) }}</td>
                            </tr>
                        @endif
                        @forelse($hutang as $h)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">{{ formatTanggalSingkat($h->tanggal) }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $h->keterangan ?? '-' }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ $h->debit > 0 ? formatRupiah($h->debit) : '-' }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ $h->kredit > 0 ? formatRupiah($h->kredit) : '-' }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">{{ formatRupiah($h->saldo) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">Tidak ada mutasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="overflow-x-auto">
                <x-atur-kolom :options="$kolomOptions" :aktif="$kolomAktif" action="{{ route('bb-hutang.simpan-kolom') }}" />
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
                        @forelse($suppliers as $s)
                            @php
                                $lastSaldo = \App\Models\BbHutang::where('supplier_id', $s->id)
                                    ->orderByDesc('id')->value('saldo');
                                $totalDebit = (float) \App\Models\BbHutang::where('supplier_id', $s->id)->sum('debit');
                                $totalKredit = (float) \App\Models\BbHutang::where('supplier_id', $s->id)->sum('kredit');
                                $saldo = (float) ($lastSaldo ?? 0);
                            @endphp
                            @if($totalDebit > 0 || $totalKredit > 0)
                                <tr class="hover:bg-gray-50">
                                    @foreach($kolomAktif as $k)
                                        @switch($k)
                                            @case('nama')
                                                <td class="px-4 py-3 font-medium text-gray-800">{{ $s->nama }}</td>
                                                @break
                                            @case('kredit')
                                                <td class="px-4 py-3 text-right text-gray-700">{{ $totalKredit > 0 ? formatRupiah($totalKredit) : '-' }}</td>
                                                @break
                                            @case('debit')
                                                <td class="px-4 py-3 text-right text-gray-700">{{ $totalDebit > 0 ? formatRupiah($totalDebit) : '-' }}</td>
                                                @break
                                            @case('saldo')
                                                <td class="px-4 py-3 text-right font-semibold {{ $saldo > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ formatRupiah($saldo) }}</td>
                                                @break
                                            @case('detail')
                                                <td class="px-4 py-3 text-center">
                                                    <a href="{{ route('bb-hutang.index', ['supplier_id' => $s->id, 'dari' => $dari, 'sampai' => $sampai]) }}" class="text-emerald-600 hover:text-emerald-800 text-xs font-medium">
                                                        Lihat Detail →
                                                    </a>
                                                </td>
                                                @break
                                        @endswitch
                                    @endforeach
                                </tr>
                            @endif
                        @empty
                            <tr><td colspan="{{ count($kolomAktif) }}" class="px-4 py-10 text-center text-gray-500">Belum ada data hutang.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>
</x-app-layout>
