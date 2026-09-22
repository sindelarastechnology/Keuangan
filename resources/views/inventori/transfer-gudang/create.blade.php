<x-app-layout>
    <x-slot name="title">Transfer Barang Antar Gudang</x-slot>

    <x-page-header title="Transfer Barang Antar Gudang" subtitle="Pindahkan stok barang antar gudang tanpa jurnal">
        <a href="{{ route('transfer-gudang.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <form method="POST" action="{{ route('transfer-gudang.store') }}" x-data="transferGudangForm()">
        @csrf

        <div class="grid grid-cols-1 gap-4 mb-4">
            <x-card>
                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <x-input-label for="tanggal" value="Tanggal" />
                        <x-text-input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" class="mt-1" required />
                        <x-input-error :messages="$errors->get('tanggal')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="gudang_asal" value="Gudang Asal" />
                        <x-select id="gudang_asal" name="gudang_asal" class="mt-1" x-model.number="gudangAsal" @change="gantiAsal()" required>
                            <option value="">-- Pilih Gudang Asal --</option>
                            <template x-for="g in opsiAsal()" :key="'asal-'+g.id">
                                <option :value="g.id" x-text="g.nama + ' (' + g.kode + ')'"></option>
                            </template>
                        </x-select>
                        <x-input-error :messages="$errors->get('gudang_asal')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="gudang_tujuan" value="Gudang Tujuan" />
                        <x-select id="gudang_tujuan" name="gudang_tujuan" class="mt-1" x-model.number="gudangTujuan" @change="gantiTujuan()" required>
                            <option value="">-- Pilih Gudang Tujuan --</option>
                            <template x-for="g in opsiTujuan()" :key="'tujuan-'+g.id">
                                <option :value="g.id" x-text="g.nama + ' (' + g.kode + ')'"></option>
                            </template>
                        </x-select>
                        <x-input-error :messages="$errors->get('gudang_tujuan')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="keterangan" value="Keterangan (Opsional)" />
                        <textarea id="keterangan" name="keterangan" rows="2" class="mt-1 block w-full border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
                @if($errors->has('error') || session('error'))
                    <div class="px-4 pb-4">
                        <p class="text-red-500 text-sm bg-red-50 rounded-lg px-3 py-2">{{ $errors->first('error') ?? session('error') }}</p>
                    </div>
                @endif
            </x-card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
            <div class="lg:col-span-2">
                <x-card>
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-gray-800">Pilih Barang</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Klik kartu untuk menambah ke keranjang transfer. Stok ditampilkan per gudang asal terpilih.</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h10"/></svg>
                                <span x-text="hasilGaleri().length"></span> item
                            </span>
                        </div>
                        <div class="mt-3 relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" x-model="qGaleri" placeholder="Cari nama / kode barang..." class="w-full pl-9 pr-3 py-2 border-gray-300 rounded-lg text-sm">
                        </div>
                    </div>

                    <div class="p-3 max-h-[62vh] overflow-y-auto grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-3 gap-3">
                        <template x-for="b in hasilGaleri()" :key="'bg'+b.id">
                            <button type="button" @click="tambahGaleri(b.id)"
                                class="group text-left flex flex-col p-3 rounded-xl border border-gray-200 hover:border-emerald-300 hover:bg-emerald-50/60 hover:shadow-sm transition">
                                <span class="w-full flex items-start justify-between gap-2">
                                    <template x-if="b.foto">
                                        <img :src="b.foto" alt="Foto barang" class="shrink-0 w-10 h-10 rounded-lg object-cover">
                                    </template>
                                    <template x-if="!b.foto">
                                        <span class="shrink-0 w-10 h-10 rounded-lg flex items-center justify-center text-sm font-bold text-white" :class="b.warna" x-text="b.inisial"></span>
                                    </template>
                                    <span class="inline-flex items-center text-[10px] font-medium rounded px-1.5 py-0.5 bg-emerald-100 text-emerald-700">Barang</span>
                                </span>
                                <span class="mt-2 block text-sm font-medium text-gray-800 leading-snug line-clamp-2" x-text="b.label"></span>
                                <span class="block text-xs text-gray-500 mt-0.5" x-text="b.kode"></span>
                                <span class="mt-2 flex items-center gap-1 text-[11px] font-medium text-gray-600">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <span class="text-gray-400">Stok Asal</span>
                                    <b x-text="formatQty(stokAsal(b.id))"></b>
                                </span>
                                <span class="mt-2 pt-2 border-t border-gray-100 flex items-end justify-end gap-2">
                                    <span class="text-[10px] font-medium rounded px-1.5 py-0.5 bg-slate-100 text-slate-600">+ Keranjang</span>
                                </span>
                            </button>
                        </template>
                        <p x-show="hasilGaleri().length === 0" class="col-span-full py-10 text-center text-sm text-gray-500">Tidak ada barang cocok.</p>
                    </div>
                </x-card>
            </div>

            <div class="lg:col-span-1 lg:sticky lg:top-4">
                <x-card>
                    <div class="p-4 border-b border-gray-100 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-3-3m3 3l-3 3m0 6H4m0 0l3-3m-3 3l3 3"/></svg>
                            <h3 class="font-semibold text-gray-800" x-text="'Keranjang Transfer ' + (adaRute() ? '(' + namaGudang(gudangAsal) + ' → ' + namaGudang(gudangTujuan) + ')' : '')"></h3>
                            <span class="text-xs font-bold bg-emerald-100 text-emerald-700 rounded-full px-2 py-0.5" x-text="jumlahQty()"></span>
                        </div>
                    </div>

                    <div class="p-3 max-h-[44vh] overflow-y-auto divide-y divide-gray-100">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="py-3">
                                <div class="flex items-center gap-2">
                                    <template x-if="infoBarang(item.barang_id) && infoBarang(item.barang_id).foto">
                                        <img :src="infoBarang(item.barang_id).foto" alt="Foto barang" class="shrink-0 w-8 h-8 rounded-lg object-cover">
                                    </template>
                                    <template x-if="!(infoBarang(item.barang_id) && infoBarang(item.barang_id).foto)">
                                        <span class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center text-[11px] font-bold text-white" :class="infoBarang(item.barang_id) ? infoBarang(item.barang_id).warna : 'bg-gray-400'" x-text="infoBarang(item.barang_id) ? infoBarang(item.barang_id).inisial : ''"></span>
                                    </template>
                                    <span class="flex-1 min-w-0 text-sm font-medium text-gray-800 truncate" x-text="infoBarang(item.barang_id) ? infoBarang(item.barang_id).label : ''"></span>
                                    <input type="hidden" :name="'items['+index+'][barang_id]'" :value="item.barang_id" required>
                                    <button type="button" @click="hapusItem(index)" class="shrink-0 p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" :class="items.length === 1 ? 'opacity-30 cursor-not-allowed' : ''" :disabled="items.length === 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>

                                <div class="mt-2 flex items-center gap-2">
                                    <div class="flex items-center rounded-lg border border-gray-200">
                                        <button type="button" @click="turunQty(item)" class="px-2.5 py-1.5 text-gray-500 hover:bg-gray-50 rounded-l-lg text-lg leading-none">−</button>
                                        <input type="number" step="0.01" min="0.01" :name="'items['+index+'][jumlah]'" :value="item.jumlah" @input="ubahJumlah(item, $event.target.value)" class="w-16 border-0 text-center text-sm py-1.5 focus:ring-0" required>
                                        <button type="button" @click="naikQty(item)" class="px-2.5 py-1.5 text-gray-500 hover:bg-gray-50 rounded-r-lg text-lg leading-none">+</button>
                                    </div>
                                    <div class="flex-1 min-w-0 text-right text-[11px] text-gray-500">
                                        Stok asal tersedia: <b x-text="formatQty(stokAsal(item.barang_id))"></b>
                                    </div>
                                </div>
                                <div class="mt-1.5 flex items-center gap-2">
                                    <input type="text" :name="'items['+index+'][keterangan]'" x-model="item.keterangan" placeholder="Keterangan item (opsional)" class="w-full border-gray-200 rounded-lg text-xs py-1.5 focus:ring-0">
                                </div>
                            </div>
                        </template>
                        <p x-show="items.length === 0" class="py-8 text-center text-sm text-gray-400">Keranjang kosong — klik item di panel kiri untuk menambah.</p>
                    </div>

                    <div class="px-4 py-4 space-y-2">
                        <p x-show="!adaRute()" class="text-xs text-amber-600 bg-amber-50 rounded-lg px-3 py-2">Pilih gudang asal dan tujuan terlebih dahulu.</p>
                        <p x-show="adaRute() && items.length === 0" class="text-xs text-red-500 text-center">Pilih minimal 1 barang untuk menyimpan.</p>
                        <button type="submit" :disabled="!adaRute() || items.length === 0"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Transfer
                        </button>
                    </div>
                </x-card>
            </div>
        </div>
    </form>

    <script>
        function transferGudangForm() {
            var gudangs = @json($gudangs->map(fn ($g) => ['id' => (int) $g->id, 'nama' => $g->nama, 'kode' => $g->kode])->values());
            var stokMap = @json($stokGudang);
            var galeri = @json($galeri);
            var oldSkema = @json(old('gudang_asal')) ? { asal: @json(old('gudang_asal')), tujuan: @json(old('gudang_tujuan')) } : null;
            var oldItems = @json(old('items', []));

            return {
                items: oldItems.map(function (o) {
                    return {
                        barang_id: o.barang_id,
                        jumlah: parseFloat(o.jumlah) > 0 ? parseFloat(o.jumlah) : 1,
                        keterangan: o.keterangan || '',
                    };
                }),
                gudangAsal: oldSkema ? oldSkema.asal : '',
                gudangTujuan: oldSkema ? oldSkema.tujuan : '',
                qGaleri: '',
                gudangs: gudangs,
                galeri: galeri,
                stokMap: stokMap,
                adaRute() {
                    return this.gudangAsal && this.gudangTujuan && String(this.gudangAsal) !== String(this.gudangTujuan);
                },
                opsiAsal() {
                    var t = String(this.gudangTujuan);
                    return this.gudangs.filter(function (g) { return !t || String(g.id) !== t; });
                },
                opsiTujuan() {
                    var a = String(this.gudangAsal);
                    return this.gudangs.filter(function (g) { return !a || String(g.id) !== a; });
                },
                gantiAsal() {
                    if (this.gudangTujuan && String(this.gudangTujuan) === String(this.gudangAsal)) {
                        this.gudangTujuan = '';
                    }
                },
                gantiTujuan() {
                    if (this.gudangAsal && String(this.gudangAsal) === String(this.gudangTujuan)) {
                        this.gudangAsal = '';
                    }
                },
                hasilGaleri() {
                    var q = (this.qGaleri || '').toLowerCase();
                    if (!q) return this.galeri;
                    return this.galeri.filter(function (b) {
                        return (b.label || '').toLowerCase().indexOf(q) !== -1 || (b.kode || '').toLowerCase().indexOf(q) !== -1;
                    });
                },
                infoBarang(brgId) {
                    for (var i = 0; i < this.galeri.length; i++) {
                        if (this.galeri[i].id == brgId) return this.galeri[i];
                    }
                    return null;
                },
                namaGudang(id) {
                    if (!id) return '';
                    for (var i = 0; i < this.gudangs.length; i++) {
                        if (String(this.gudangs[i].id) === String(id)) return this.gudangs[i].nama;
                    }
                    return '';
                },
                stokAsal(brgId) {
                    if (!this.adaRute()) return 0;
                    var map = this.stokMap[this.gudangAsal];
                    return (map && map[brgId] !== undefined) ? map[brgId] : 0;
                },
                tambahGaleri(brgId) {
                    if (!this.adaRute()) return;
                    var existing = null;
                    for (var i = 0; i < this.items.length; i++) {
                        if (this.items[i].barang_id == brgId) { existing = this.items[i]; break; }
                    }
                    if (existing) {
                        existing.jumlah = this.clampQty(existing, (parseFloat(existing.jumlah) || 0) + 1);
                        return;
                    }
                    this.items.push({ barang_id: brgId, jumlah: 1, keterangan: '' });
                },
                clampQty(item, v) {
                    var stok = this.stokAsal(item.barang_id);
                    if (v < 0.01) return 0.01;
                    if (stok > 0 && v > stok) return stok;
                    return v;
                },
                ubahJumlah(item, raw) {
                    var v = (typeof raw === 'number') ? raw : (parseFloat(raw) || 0);
                    item.jumlah = this.clampQty(item, v);
                },
                naikQty(item) { this.ubahJumlah(item, (parseFloat(item.jumlah) || 0) + 1); },
                turunQty(item) { this.ubahJumlah(item, (parseFloat(item.jumlah) || 0) - 1); },
                hapusItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
                jumlahQty() {
                    var t = 0;
                    this.items.forEach(function (it) { if (it.barang_id) t += (parseFloat(it.jumlah) || 0); });
                    return this.formatQty(t);
                },
                formatQty(n) {
                    var v = parseFloat(n) || 0;
                    return v.toLocaleString('id-ID');
                }
            };
        }
    </script>
</x-app-layout>