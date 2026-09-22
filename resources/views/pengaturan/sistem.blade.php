<x-app-layout>
    <x-slot name="title">Pengaturan Sistem</x-slot>

    <x-page-header title="Pengaturan Sistem" subtitle="Akun penting yang dipakai sistem & gudang default transaksi"></x-page-header>

    <div x-data="{ tab: '{{ request('tab', 'akun') }}' }">

        {{-- Tab Navigation --}}
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex gap-6">
                <button type="button"
                    @click="tab = 'akun'"
                    :class="tab === 'akun' ? 'border-emerald-600 text-emerald-700 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="inline-flex items-center gap-2 py-3 border-b-2 text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h2m-2 4h2M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v4M18 9a3 3 0 110-6 3 3 0 010 6zm-7 9a2 2 0 11-4 0 2 2 0 014 0zm7 1v-6m-3 3h6"/></svg>
                    Akun Penting
                </button>
                <button type="button"
                    @click="tab = 'gudang'"
                    :class="tab === 'gudang' ? 'border-emerald-600 text-emerald-700 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="inline-flex items-center gap-2 py-3 border-b-2 text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-7m0-7V4a1 1 0 011-1h6a1 1 0 011 1v3m0 7v7m0-7H3m6 0h6m0 0v7m0-7V7m0 0a1 1 0 011-1h6a1 1 0 011 1v3m-7 7h7"/></svg>
                    Pengaturan Gudang
                </button>
            </nav>
        </div>

        <form method="POST" action="{{ route('pengaturan.sistem.update') }}">
            @csrf

            {{-- Tab: Akun Penting --}}
            <div x-show="tab === 'akun'" x-cloak>
                <x-card>
                    <div class="p-5">
                        <h3 class="text-sm font-semibold text-gray-700 mb-1">Akun Penting</h3>
                        <p class="text-xs text-gray-500 mb-5">
                            Akun perkiraan yang dipakai sistem secara otomatis saat mencatat jurnal, kas, dan laporan.
                            Kosongkan sebuah pilihan untuk memakai akun default (sesuai kode di sebelah label).
                        </p>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Akun</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode Default</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Akun yang Dipakai</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($roleList as $r)
                                        @php($field = 'akun_'.$r['role'])
                                        <tr>
                                            <td class="px-4 py-3">
                                                <span class="text-sm font-medium text-gray-800">{{ $r['label'] }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600">{{ $r['jenisLabel'] }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono rounded-md bg-emerald-50 text-emerald-700">{{ $r['kodeDefault'] }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <select name="{{ $field }}" class="w-full sm:w-72 border-gray-300 rounded-lg text-sm">
                                                    <option value="">— Otomatis ({{ $r['kodeDefault'] }}) —</option>
                                                    @foreach($akunOptions[$r['jenis']] as $opt)
                                                        <option value="{{ $opt['id'] }}" {{ $r['savedId'] === (int) $opt['id'] ? 'selected' : '' }}>{{ $opt['label'] }}</option>
                                                    @endforeach
                                                </select>
                                                @if($errors->has($field))
                                                    <p class="mt-1 text-xs text-red-600">{{ $errors->first($field) }}</p>
                                                @endif
                                                @if(isset($resolved[$r['role']]) && $resolved[$r['role']] && $r['savedId'] === 0)
                                                    <p class="mt-1 text-xs text-gray-400">
                                                        Saat ini: {{ $resolved[$r['role']]->kode }} — {{ $resolved[$r['role']]->nama }}
                                                    </p>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end pt-5">
                            <x-loading-button size="lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan Akun Penting
                            </x-loading-button>
                            <input type="hidden" name="tab" value="akun">
                        </div>
                    </div>
                </x-card>
            </div>

            {{-- Tab: Pengaturan Gudang --}}
            <div x-show="tab === 'gudang'" x-cloak>
                <div class="max-w-3xl space-y-4">
                    <x-card>
                        <div class="p-5">
                            <h3 class="text-sm font-semibold text-gray-700 mb-1">Gudang Default Transaksi</h3>
                            <p class="text-xs text-gray-500 mb-5">
                                Gudang yang otomatis terisi pada transaksi pembelian & penjualan baru. Kosongkan untuk
                                memakai Gudang Utama (otomatis, urutan pertama).
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <x-input-label for="gudang_default_pembelian" value="Gudang Utama Pembelian" />
                                    <x-select id="gudang_default_pembelian" name="gudang_default_pembelian" class="mt-1 w-full">
                                        <option value="">— Gudang Utama —</option>
                                        @foreach($gudangList as $g)
                                            <option value="{{ $g->id }}" {{ (int) old('gudang_default_pembelian', $gudangPembelian) === (int) $g->id ? 'selected' : '' }}>{{ $g->kode }} — {{ $g->nama }}</option>
                                        @endforeach
                                    </x-select>
                                    <p class="mt-1 text-xs text-gray-400">Dipakai pada form Pembelian (items masuk ke gudang ini).</p>
                                    @if($errors->has('gudang_default_pembelian'))
                                        <p class="mt-1 text-xs text-red-600">{{ $errors->first('gudang_default_pembelian') }}</p>
                                    @endif
                                </div>
                                <div>
                                    <x-input-label for="gudang_default_penjualan" value="Gudang Penjualan Eceran" />
                                    <x-select id="gudang_default_penjualan" name="gudang_default_penjualan" class="mt-1 w-full">
                                        <option value="">— Gudang Utama —</option>
                                        @foreach($gudangList as $g)
                                            <option value="{{ $g->id }}" {{ (int) old('gudang_default_penjualan', $gudangPenjualan) === (int) $g->id ? 'selected' : '' }}>{{ $g->kode }} — {{ $g->nama }}</option>
                                        @endforeach
                                    </x-select>
                                    <p class="mt-1 text-xs text-gray-400">Dipakai sebagai default pada form Penjualan.</p>
                                    @if($errors->has('gudang_default_penjualan'))
                                        <p class="mt-1 text-xs text-red-600">{{ $errors->first('gudang_default_penjualan') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </x-card>

                    <x-card>
                        <div class="p-5">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Daftar Gudang</h3>
                            <div class="divide-y divide-gray-100">
                                @forelse($gudangList as $g)
                                    <div class="flex items-center justify-between px-1 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="text-sm font-mono text-gray-500">{{ $g->kode }}</span>
                                            <span class="text-sm text-gray-800">{{ $g->nama }}</span>
                                            @if((int) $g->id === (int) $gudangUtamaId)
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">Utama</span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-400">
                                            {{ isset($gudangStok[$g->id]) ? $gudangStok[$g->id].' jenis barang' : 'Belum ada stok' }}
                                        </span>
                                    </div>
                                @empty
                                    <div class="px-1 py-8 text-center text-sm text-gray-400">Belum ada gudang.</div>
                                @endforelse
                            </div>
                        </div>
                    </x-card>

                    <div class="flex justify-end">
                        <x-loading-button size="lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Pengaturan Gudang
                        </x-loading-button>
                        <input type="hidden" name="tab" value="gudang">
                    </div>
                </div>
            </div>
        </form>

    </div>
</x-app-layout>