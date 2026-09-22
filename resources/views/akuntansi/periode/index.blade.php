<x-app-layout>
    <x-slot name="title">Periode Akuntansi</x-slot>

    <x-page-header title="Periode Akuntansi" subtitle="Kelola periode bulanan dan periode aktif"></x-page-header>

    <x-card>
        <div class="p-4 border-b border-gray-100 flex items-center">
            <form method="GET" class="flex items-center gap-2">
                <x-input-label for="tahun" value="Tahun" class="sr-only" />
                <select name="tahun" id="tahun" onchange="this.form.submit()" class="border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </form>
            <span class="ml-auto text-xs text-gray-500">Klik "Buka Periode" untuk menjadikan periode aktif.</span>
        </div>

        <x-atur-kolom :options="$kolomOptions" :aktif="$kolomAktif" action="{{ route('periode.simpan-kolom') }}" />

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($kolomAktif as $k)
                            <th class="px-4 py-3 {{ in_array($k, ['status', 'buka', 'aksi'], true) ? 'text-center' : 'text-left' }} text-xs font-semibold text-gray-500 uppercase">
                                {{ $kolomOptions[$k] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($periodes as $p)
                        <tr class="hover:bg-gray-50 {{ $p->is_open ? 'bg-emerald-50/50' : '' }}">
                            @foreach($kolomAktif as $k)
                                @switch($k)
                                    @case('label')
                                        <td class="px-4 py-3 font-medium text-gray-800">{{ $p->label }}</td>
                                        @break
                                    @case('status')
                                        <td class="px-4 py-3 text-center">
                                            @if($p->is_locked)
                                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700">Terkunci</span>
                                            @elseif($p->is_open)
                                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700">Aktif</span>
                                            @elseif($p->is_closed)
                                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-600">Ditutup</span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-500">Belum dibuka</span>
                                            @endif
                                        </td>
                                        @break
                                    @case('buka')
                                        <td class="px-4 py-3 text-center text-xs text-gray-500">{{ $p->tanggal_buka ? formatTanggalSingkat($p->tanggal_buka) : '-' }}</td>
                                        @break
                                    @case('aksi')
                                        <td class="px-4 py-3 text-center">
                                            @if($p->is_locked)
                                                <form method="POST" action="{{ route('periode.buka-kunci', $p) }}">
                                                    @csrf
                                                    <button class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs rounded-lg">Buka Kunci</button>
                                                </form>
                                            @elseif($p->is_open)
                                                <span class="text-xs text-gray-400">Periode aktif</span>
                                            @elseif(!$p->is_closed)
                                                <div class="flex flex-col gap-1.5 items-center">
                                                    <form method="POST" action="{{ route('periode.buka', $p) }}">
                                                        @csrf
                                                        <button class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs rounded-lg">Buka Periode</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('periode.kunci', $p) }}">
                                                        @csrf
                                                        <input type="hidden" name="reason" value="Kunci manual">
                                                        <button type="button" class="px-3 py-1.5 bg-slate-500 hover:bg-slate-600 text-white text-xs rounded-lg"
                                                                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'kunci-periode-{{ $p->id }}' }))">Kunci</button>
                                                        <x-confirm-dialog name="kunci-periode-{{ $p->id }}" title="Kunci Periode"
                                                                          message="Yakin ingin mengunci periode '{{ $p->label }}'? Transaksi baru di periode ini akan ditolak dan tindakan ini dapat dibatalkan lewat Buka Kunci." />
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400">Terkunci</span>
                                            @endif
                                        </td>
                                        @break
                                @endswitch
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($kolomAktif) }}" class="px-4 py-12 text-center text-gray-500">Belum ada periode untuk tahun {{ $tahun }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>
