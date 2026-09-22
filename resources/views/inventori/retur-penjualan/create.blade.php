<x-app-layout>
    <x-slot name="title">Retur Penjualan Baru</x-slot>

    <x-page-header title="Retur Penjualan Baru" subtitle="Pilih penjualan yang akan diretur — stok & jurnal dikembalikan otomatis">
        <a href="{{ route('retur-penjualan.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <form method="POST" action="{{ route('retur-penjualan.store') }}" x-data="returPenjualanForm()" @submit.prevent="submitForm()">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
            <x-card>
                <div class="p-4 space-y-4">
                    <div>
                        <x-input-label for="tanggal" value="Tanggal" />
                        <x-text-input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" class="mt-1" required />
                        <x-input-error :messages="$errors->get('tanggal')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="penjualan_id" value="Pilih Penjualan" />
                        <select id="penjualan_id" name="penjualan_id" x-model="transaksiId" @change="pilihTransaksi()"
                            class="mt-1 block w-full border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">— Pilih penjualan yang akan diretur —</option>
                            @foreach ($daftarPenjualan as $dh)
                                <option value="{{ $dh['id'] }}">{{ $dh['label'] }} — {{ formatRupiah($dh['total']) }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Hanya transaksi berstatus posted yang bisa diretur; transaksi yang sudah dibatalkan tidak muncul.</p>
                        <x-input-error :messages="$errors->get('penjualan_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="keterangan" value="Keterangan (Opsional)" />
                        <textarea id="keterangan" name="keterangan" rows="3" class="mt-1 block w-full border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">{{ old('keterangan') }}</textarea>
                    </div>
                    @if($errors->any())
                        <div class="p-3 bg-red-50 text-red-600 text-sm rounded-lg">{{ $errors->first() }}</div>
                    @endif
                    <div x-show="pesanError" x-transition class="p-3 bg-red-50 text-red-600 text-sm rounded-lg" x-text="pesanError"></div>
                </div>
            </x-card>

            <div class="lg:col-span-2">
                <x-card>
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800">Item Retur</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Item penjualan terpilih dimuat otomatis. Isi jumlah yang diretur pada tiap barang yang diinginkan. Harga yang ditampilkan sudah neto setelah diskon; PPN ikut dikembalikan proporsional.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Barang</th>
                                    <th class="px-2 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Sisa Diretur</th>
                                    <th class="px-2 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-2 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Harga</th>
                                    <th class="px-2 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="!transaksi">
                                    <tr>
                                        <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-400">Pilih penjualan terlebih dahulu — item akan muncul di sini.</td>
                                    </tr>
                                </template>
                                <template x-if="transaksi && memuatItem">
                                    <tr>
                                        <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-400">Memuat item...</td>
                                    </tr>
                                </template>
                                <template x-for="(item, index) in items" :key="item.penjualan_item_id">
                                    <tr>
                                        <td class="px-2 py-2 min-w-[180px]">
                                            <div class="font-medium text-gray-700" x-text="namaBarang(item)"></div>
                                        </td>
                                        <td class="px-2 py-2 text-right text-gray-600" x-text="formatAngka(sisa(item))"></td>
                                        <td class="px-2 py-2 text-right">
                                            <input type="hidden" :name="'items['+index+'][penjualan_item_id]'" :value="item.penjualan_item_id">
                                            <input type="number" step="0.01" min="0.01" :max="sisa(item)" :name="'items['+index+'][jumlah]'" x-model.number="item.jumlah" placeholder="0" :disabled="sisa(item) <= 0" class="border-gray-300 rounded-lg text-sm w-24 text-right disabled:bg-gray-100 disabled:text-gray-400">
                                        </td>
                                        <td class="px-2 py-2 text-right">
                                            <div class="text-gray-600" x-text="formatRupiahUnformatted(harga(item))"></div>
                                            <div class="text-[10px] text-gray-400" x-show="hargaBruto(item) !== harga(item)" x-text="'sebelum diskon ' + formatRupiahUnformatted(hargaBruto(item))"></div>
                                        </td>
                                        <td class="px-2 py-2 text-right font-semibold text-emerald-600" x-text="formatRupiahUnformatted(subtotal(item))"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="px-4 py-4 border-t border-gray-100 bg-gray-50 space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Total Nilai Retur</span>
                            <span class="font-bold text-emerald-600 text-lg" x-text="formatRupiahUnformatted(total())"></span>
                        </div>
                    </div>

                    <div class="px-4 py-4 flex justify-end">
                        <button type="submit" :disabled="submitting" class="inline-flex items-center gap-2 px-5 py-2.5 text-base font-medium rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-60 disabled:cursor-not-allowed bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500">
                            <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <svg x-show="submitting" x-cloak class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-6.219-8.56"/></svg>
                            <span x-show="!submitting">Posting Retur</span>
                            <span x-show="submitting" x-cloak>Menyimpan...</span>
                        </button>
                    </div>
                </x-card>
            </div>
        </div>
    </form>

    <script>
        @php
            $urlSumberRelatif = str_replace(url('/'), '', route('retur-penjualan.sumber-items', ['penjualan' => '__ID__']));
        @endphp
        function returPenjualanForm() {
            var urlSumber = @json($urlSumberRelatif);
            return {
                transaksiId: @json((string) old('penjualan_id')),
                transaksi: null,
                memuatItem: false,
                pesanError: '',
                submitting: false,
                items: [],
                sumberItems: [],

                init() {
                    if (this.transaksiId) {
                        this.transaksi = { id: this.transaksiId, label: 'Penjualan #' + this.transaksiId };
                        this.muatSumber(this.transaksiId);
                    }
                },
                pilihTransaksi() {
                    if (!this.transaksiId) {
                        this.transaksi = null;
                        this.items = [];
                        this.sumberItems = [];
                        this.pesanError = '';
                        return;
                    }
                    this.transaksi = { id: this.transaksiId };
                    this.pesanError = '';
                    this.muatSumber(this.transaksiId);
                },
                muatSumber(id) {
                    var self = this;
                    this.memuatItem = true;
                    this.items = [];
                    this.sumberItems = [];
                    this.pesanError = '';
                    fetch(urlSumber.replace('__ID__', id))
                        .then(r => {
                            if (!r.ok) {
                                throw new Error('Gagal memuat item (HTTP ' + r.status + ').');
                            }
                            return r.json();
                        })
                        .then(data => {
                            if (!Array.isArray(data)) {
                                throw new Error('Respons item tidak valid.');
                            }
                            self.sumberItems = data;
                            self.items = data.map(o => ({
                                penjualan_item_id: o.penjualan_item_id,
                                jumlah: null,
                            }));
                            self.memuatItem = false;
                        })
                        .catch(e => {
                            self.memuatItem = false;
                            self.pesanError = e.message || 'Gagal memuat item sumber transaksi.';
                        });
                },
                submitForm() {
                    var isi = this.items.filter(i => parseFloat(i.jumlah) > 0);
                    if (!this.transaksiId || isi.length === 0) {
                        this.pesanError = 'Pilih penjualan dan isi jumlah minimal satu barang yang akan diretur.';
                        return;
                    }
                    this.items = isi;
                    this.pesanError = '';
                    this.submitting = true;
                    this.$el.submit();
                },
                opt(id) { return this.sumberItems.find(o => String(o.penjualan_item_id) === String(id)); },
                namaBarang(item) { var o = this.opt(item.penjualan_item_id); return o ? o.nama : ''; },
                sisa(item) { var o = this.opt(item.penjualan_item_id); return o ? o.sisa : 0; },
                harga(item) { var o = this.opt(item.penjualan_item_id); return o ? (parseFloat(o.harga) || 0) : 0; },
                hargaBruto(item) { var o = this.opt(item.penjualan_item_id); return o ? (parseFloat(o.harga_satuan) || 0) : 0; },
                subtotal(item) { return (parseFloat(item.jumlah) || 0) * this.harga(item); },
                total() { return this.items.reduce((s, it) => s + this.subtotal(it), 0); },
                formatAngka(n) { n = parseFloat(n) || 0; return (Math.round(n * 100) / 100).toString().replace('.', ','); },
                formatRupiahUnformatted(n) { return 'Rp ' + n.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}); }
            };
        }
    </script>
</x-app-layout>