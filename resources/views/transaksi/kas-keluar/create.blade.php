<x-app-layout>
    <x-slot name="title">Kas Keluar Baru</x-slot>

    <x-page-header title="Kas Keluar Baru" subtitle="Catat pengeluaran uang dari kas">
        <a href="{{ route('kas-keluar.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <form method="POST" action="{{ route('kas-keluar.store') }}" x-data="kasKeluarForm()" @submit="syncItems()">
        @csrf

        <div x-show="saldoRekeningKurang()" x-cloak x-transition class="mb-4 px-4 py-3 bg-amber-50 border border-amber-300 rounded-lg text-sm text-amber-800">
            Saldo rekening tidak mencukupi: saldo saat ini <span class="font-semibold" x-text="'Rp ' + formatRupiah(saldoRekening())"></span>,
            sedangkan grand total yang akan keluar <span class="font-semibold" x-text="'Rp ' + formatRupiah(grandTotal())"></span>.
            Transaksi tetap dapat disimpan, namun saldo rekening akan menjadi minus.
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
            <x-card>
                <div class="p-4 space-y-4">
                    <div>
                        <x-input-label for="tanggal" value="Tanggal" />
                        <x-text-input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" class="mt-1" required />
                        <x-input-error :messages="$errors->get('tanggal')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="rekening_id" value="Rekening Sumber" />
                        <x-select id="rekening_id" name="rekening_id" class="mt-1" required x-model="rekeningId">
                            @foreach($rekeningList as $k)
                                <option value="{{ $k->id }}" {{ old('rekening_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error :messages="$errors->get('rekening_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="pajak_id" value="Pajak (Opsional)" />
                        <x-select id="pajak_id" name="pajak_id" class="mt-1" @change="syncItems()">
                            <option value="">— Tanpa Pajak —</option>
                            @foreach($pajakList as $p)
                                <option value="{{ $p->id }}" {{ old('pajak_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }} ({{ $p->rate }}%)</option>
                            @endforeach
                        </x-select>
                        <x-input-error :messages="$errors->get('pajak_id')" class="mt-1" />
                    </div>
                    <div x-show="showSupplier" x-transition>
                        <x-input-label for="supplier_id" value="Supplier (Bayar Hutang)" />
                        <x-select id="supplier_id" name="supplier_id" class="mt-1">
                            <option value="">— Pilih Supplier —</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error :messages="$errors->get('supplier_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="keterangan" value="Keterangan" />
                        <x-textarea id="keterangan" name="keterangan" class="mt-1" rows="3">{{ old('keterangan') }}</x-textarea>
                    </div>
                </div>
            </x-card>

            <div class="lg:col-span-2">
                <x-card>
                    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800">Item Pengeluaran</h3>
                        <button type="button" @click="tambahItem()" class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Tambah Baris
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis Pengeluaran</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Nominal</th>
                                    <th class="px-2 py-3 w-10"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="index">
                                    <tr>
                                        <td class="px-2 py-2 w-1/3">
                                            <select x-model="item.kategori" @change="setAkun(item)" class="border-gray-300 rounded-lg text-sm w-full" required>
                                                <option value="">Pilih Jenis...</option>
                                                @foreach($katList as $k)
                                                    <option value="{{ $k['value'] }}">{{ $k['label'] }}</option>
                                                @endforeach
                                            </select>
                                            <select x-model="item.akun_id" :name="'items['+index+'][akun_id]'" x-show="item.kategori === 'manual'" class="border-gray-300 rounded-lg text-sm w-full mt-2" :required="item.kategori === 'manual'">
                                                <option value="">Pilih Akun...</option>
                                                @foreach($akunList as $a)
                                                    <option value="{{ $a->id }}">{{ $a->kode }} - {{ $a->nama }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" :name="'items['+index+'][kategori]'" :value="item.kategori">
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="text" x-model="item.keterangan" :name="'items['+index+'][keterangan]'" placeholder="Keterangan" class="border-gray-300 rounded-lg text-sm w-full">
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="number" step="0.01" x-model="item.nominal" :name="'items['+index+'][nominal]'" placeholder="0" class="border-gray-300 rounded-lg text-sm w-full text-right" required>
                                        </td>
                                        <td class="px-2 py-2">
                                            <button type="button" @click="hapusItem(index)" class="p-1 text-red-500 hover:bg-red-50 rounded" :class="items.length === 1 ? 'opacity-30 cursor-not-allowed' : ''" :disabled="items.length === 1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="px-4 py-4 border-t border-gray-100 bg-gray-50 space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Total Pengeluaran</span>
                            <span class="font-bold text-red-600 text-lg" x-text="'Rp ' + formatRupiah(total())"></span>
                        </div>
                        <div class="flex justify-between items-center" x-show="pajakAktif()">
                            <span class="text-gray-500 text-sm" x-text="'Pajak (' + pajakRate() + '%)'"></span>
                            <span class="font-semibold text-gray-700 text-sm" x-text="'Rp ' + formatRupiah(pajakNominal())"></span>
                        </div>
                        <div class="flex justify-between items-center border-t border-gray-200 pt-3">
                            <span class="font-semibold text-gray-700">Grand Total</span>
                            <span class="font-bold text-red-700 text-xl" x-text="'Rp ' + formatRupiah(grandTotal())"></span>
                        </div>
                    </div>

                    <div class="px-4 py-4 flex justify-end">
                        <x-loading-button size="lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Kas Keluar
                        </x-loading-button>
                    </div>
                </x-card>
            </div>
        </div>
    </form>

    <script>
        function kasKeluarForm() {
            var pajakRateMap = @json($pajakList->pluck('rate', 'id'));
            var katMap = @json(collect($katList)->pluck('akun_id', 'value'));
            var saldoMap = @json($saldoMap);
            var namaMap = @json($namaMap);
            return {
                items: [{kategori: '', akun_id: ''}],
                pajakId: @json(old('pajak_id', '')),
                showSupplier: @json(old('supplier_id') ? true : false),
                rekeningId: '',
                init() { var sel = document.getElementById('rekening_id'); this.rekeningId = sel ? sel.value : ''; },
                tambahItem() { this.items.push({kategori: '', akun_id: ''}); },
                hapusItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
                setAkun(item) {
                    item.akun_id = item.kategori ? (katMap[item.kategori] || '') : '';
                    // Show supplier dropdown when utang category is selected
                    this.showSupplier = this.items.some(it => it.kategori === 'utang');
                    if (!this.showSupplier) {
                        var sel = document.getElementById('supplier_id');
                        if (sel) sel.value = '';
                    }
                },
                total() { return this.items.reduce((s, it) => s + (parseFloat(it.nominal) || 0), 0); },
                pajakRate() { return this.pajakId ? (pajakRateMap[this.pajakId] || 0) : 0; },
                pajakAktif() { return this.pajakRate() > 0; },
                pajakNominal() { return this.total() * (this.pajakRate() / 100); },
                grandTotal() { return this.total() + this.pajakNominal(); },
                saldoRekening() {
                    return this.rekeningId ? (parseFloat(saldoMap[this.rekeningId]) || 0) : 0;
                },
                namaRekening() {
                    return this.rekeningId ? (namaMap[this.rekeningId] || '') : '';
                },
                saldoRekeningKurang() {
                    if (!this.rekeningId) return false;
                    return (this.saldoRekening() < this.grandTotal()) && this.total() > 0;
                },
                syncItems() {
                    var select = document.getElementById('pajak_id');
                    this.pajakId = select ? select.value : '';
                },
                formatRupiah(n) { return n.toLocaleString('id-ID'); }
            };
        }
    </script>
</x-app-layout>
