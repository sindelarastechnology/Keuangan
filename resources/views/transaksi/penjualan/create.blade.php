@php($editing = isset($penjualan))
@php($def = $editData ?? [])

<x-app-layout>
    <x-slot name="title">{{ $editing ? 'Edit Draft Penjualan' : 'Penjualan Baru' }}</x-slot>

    <x-page-header title="{{ $editing ? 'Edit Draft Penjualan' : 'Penjualan Baru' }}" subtitle="{{ $editing ? $penjualan->nomor : 'Catat penjualan barang / jasa kepada customer' }}">
        <a href="{{ route('penjualan.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <form id="form-penjualan" method="POST" action="{{ $editing ? route('penjualan.update', $penjualan) : route('penjualan.store') }}" x-data="penjualanForm(penjualanEdit)" x-init="init()" @submit="syncAll()">
        @csrf
        @if($editing) @method('PUT') @endif
        <input type="hidden" name="aksi" value="posted" x-ref="aksiInput">

        <div class="grid grid-cols-1 gap-4 mb-4">
            <x-card>
                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <x-input-label for="tanggal" value="Tanggal" />
                        <x-text-input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal', $def['tanggal'] ?? now()->toDateString()) }}" class="mt-1" required />
                        <x-input-error :messages="$errors->get('tanggal')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="customer_id" value="Customer" />
                        <x-select id="customer_id" name="customer_id" class="mt-1" @change="gantiPembeli()" required>
                            <option value="">Pilih Customer...</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ (old('customer_id') ? old('customer_id') == $c->id : ($def['customer_id'] ?? $customerDefaultId) == $c->id) ? 'selected' : '' }}>{{ $c->nama }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error :messages="$errors->get('customer_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="metode_bayar" value="Metode Pembayaran" />
                        <x-select id="metode_bayar" name="metode_bayar" class="mt-1" @change="ubahMetodeBayar()" required>
                            <option value="tunai" {{ old('metode_bayar', $def['metode_bayar'] ?? 'tunai') === 'tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="kredit" {{ old('metode_bayar', $def['metode_bayar'] ?? '') === 'kredit' ? 'selected' : '' }}>Kredit (Piutang)</option>
                        </x-select>
                    </div>
                    <div x-show="metodeBayar === 'tunai'">
                        <x-input-label for="rekening_id" value="Rekening (Kas / Bank)" />
                        <x-select id="rekening_id" name="rekening_id" class="mt-1">
                            <option value="">Pilih Rekening...</option>
                            @foreach($rekeningList as $k)
                                <option value="{{ $k->id }}" {{ old('rekening_id', $def['rekening_id'] ?? '') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error :messages="$errors->get('rekening_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="pajak_id" value="Pajak (Opsional)" />
                        <x-select id="pajak_id" name="pajak_id" class="mt-1" @change="syncPajak()">
                            <option value="">— Tanpa Pajak —</option>
                            @foreach($pajakList as $p)
                                <option value="{{ $p->id }}" {{ old('pajak_id', $def['pajak_id'] ?? '') == $p->id ? 'selected' : '' }}>{{ $p->nama }} ({{ $p->rate }}%)</option>
                            @endforeach
                        </x-select>
                    </div>
                    <div>
                        <x-input-label for="gudang_id" value="Gudang Penjualan" />
                        <x-select id="gudang_id" name="gudang_id" class="mt-1" @change="gantiGudang()">
                            <option value="">— Ikuti Pengaturan —</option>
                            @foreach($gudangList as $g)
                                <option value="{{ $g->id }}" {{ old('gudang_id', $def['gudang_id'] ?? $gudangPenjualanId) == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                            @endforeach
                        </x-select>
                        <p class="mt-1 text-xs text-gray-500">Stok item mengikuti gudang ini.</p>
                        <x-input-error :messages="$errors->get('gudang_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="diskon_global" value="Diskon Global" />
                        <div class="mt-1 flex items-center gap-2">
                            <x-select id="diskon_tipe" name="diskon_tipe" class="w-28" @change="syncDiskonTipe()">
                                <option value="nominal" {{ old('diskon_tipe', $def['diskon_tipe'] ?? 'nominal') === 'nominal' ? 'selected' : '' }}>Nominal</option>
                                <option value="persen" {{ old('diskon_tipe', $def['diskon_tipe'] ?? '') === 'persen' ? 'selected' : '' }}>Persen</option>
                            </x-select>
                            <input type="number" step="0.01" name="diskon" :value="diskon"
                                @input="validasiDiskonGlobal($event.target.value)" id="diskon_global" value="{{ old('diskon', $def['diskon'] ?? 0) }}"
                                class="w-full border-gray-300 rounded-lg text-sm text-right">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Potongan untuk seluruh transaksi.</p>
                    </div>
                    <div>
                        <x-input-label for="ongkir_input" value="Ongkir (Rp)" />
                        <input type="number" step="0.01" name="ongkir" x-model.number="ongkir"
                            @input="ongkir=$event.target.value" id="ongkir_input" value="{{ old('ongkir', $def['ongkir'] ?? 0) }}"
                            class="mt-1 w-full border-gray-300 rounded-lg text-sm text-right">
                        <p class="mt-1 text-xs text-gray-500">Biaya pengiriman, jika ada.</p>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
            <div class="lg:col-span-2">
                <x-card>
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-gray-800">Pilih Item</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Klik kartu untuk menambah ke keranjang. Klik lagi pada item yang sama untuk menambah jumlah.</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h10"/></svg>
                                <span x-text="hasilGaleri().length"></span> item
                            </span>
                        </div>
                        <div class="mt-3 relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" x-model="qGaleri" placeholder="Cari nama / kode barang atau jasa..." class="w-full pl-9 pr-3 py-2 border-gray-300 rounded-lg text-sm" x-ref="qGaleri">
                        </div>
                    </div>
                    <div class="p-3 max-h-[62vh] overflow-y-auto grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
                        <template x-for="b in hasilGaleri()" :key="'bg'+b.id">
                            <button type="button" @click="tambahGaleri(b.id)"
                                class="group text-left flex flex-col p-3 rounded-xl border transition"
                                :class="b.tipe === 'jasa' ? 'border-violet-200 hover:border-violet-300 hover:bg-violet-50/60' : (stokBarang(b) > 0 ? 'border-gray-200 hover:border-emerald-300 hover:bg-emerald-50/60' : 'border-gray-100 bg-gray-50 opacity-60 cursor-not-allowed')"
                                :disabled="b.tipe === 'barang' && stokBarang(b) <= 0">
                                <span class="w-full flex items-start justify-between gap-2">
                                    <template x-if="b.foto">
                                        <img :src="b.foto" alt="Foto barang" class="shrink-0 w-10 h-10 rounded-lg object-cover">
                                    </template>
                                    <template x-if="!b.foto">
                                        <span class="shrink-0 w-10 h-10 rounded-lg flex items-center justify-center text-sm font-bold text-white" :class="b.warna" x-text="b.inisial"></span>
                                    </template>
                                    <span class="inline-flex items-center text-[10px] font-medium rounded px-1.5 py-0.5" :class="b.tipe === 'jasa' ? 'bg-violet-100 text-violet-700' : 'bg-emerald-100 text-emerald-700'" x-text="b.tipe === 'jasa' ? 'Jasa' : 'Barang'"></span>
                                </span>
                                <span class="mt-2 block text-sm font-medium text-gray-800 leading-snug line-clamp-2" x-text="b.label"></span>
                                <span class="block text-xs text-gray-500 mt-0.5" x-text="b.kode"></span>
                                <span class="mt-2 block text-[11px] font-medium"
                                    :class="b.tipe === 'jasa' ? 'text-violet-500' : (stokBarang(b) > 0 ? 'text-emerald-600' : 'text-red-500')"
                                    x-text="b.tipe === 'jasa' ? 'Tanpa stok' : ('Stok ' + stokBarang(b) + (stokBarang(b) > 0 ? '' : ' · Habis'))"></span>
                                <span class="mt-2 pt-2 border-t border-gray-100 flex items-end justify-between gap-2">
                                    <span class="text-sm font-semibold text-gray-800" x-text="'Rp ' + formatRupiah(hargaGaleri(b.id))"></span>
                                    <span class="text-[10px] font-medium rounded px-1.5 py-0.5" :class="badgeClass(sumberGaleri(b.id).k)" x-text="sumberGaleri(b.id).label"></span>
                                </span>
                            </button>
                        </template>
                        <p x-show="hasilGaleri().length === 0" class="col-span-full py-10 text-center text-sm text-gray-500">Tidak ada item cocok.</p>
                    </div>
                </x-card>
            </div>

            <div class="lg:col-span-1 lg:sticky lg:top-4">
                <x-card>
                    <div class="p-4 border-b border-gray-100 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <h3 class="font-semibold text-gray-800">Keranjang Penjualan</h3>
                            <span class="text-xs font-bold bg-emerald-100 text-emerald-700 rounded-full px-2 py-0.5" x-text="jumlahQty()"></span>
                        </div>
                    </div>
                    <p x-show="stokHabis" class="px-4 py-2 text-[11px] text-red-600 bg-red-50 border-b border-red-100" x-text="stokHabis"></p>

                    <div class="p-3 max-h-[50vh] overflow-y-auto divide-y divide-gray-100">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="py-3">
                                <div class="flex items-center gap-2">
                                    <template x-if="item.barang_id && infoBarang(item.barang_id) && infoBarang(item.barang_id).foto">
                                        <img :src="infoBarang(item.barang_id).foto" alt="Foto barang" class="shrink-0 w-8 h-8 rounded-lg object-cover">
                                    </template>
                                    <template x-if="!(item.barang_id && infoBarang(item.barang_id) && infoBarang(item.barang_id).foto)">
                                        <span class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center text-[11px] font-bold text-white" :class="infoBarang(item.barang_id) ? infoBarang(item.barang_id).warna : 'bg-gray-400'" x-text="infoBarang(item.barang_id) ? infoBarang(item.barang_id).inisial : ''"></span>
                                    </template>
                                    <span class="flex-1 min-w-0 text-sm font-medium text-gray-800 truncate" x-text="infoBarang(item.barang_id) ? infoBarang(item.barang_id).label : ''"></span>
                                    <input type="hidden" :name="'items['+index+'][barang_id]'" :value="item.barang_id" required>
                                    <button type="button" @click="hapusItem(index)" class="shrink-0 p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" :class="items.length === 1 ? 'opacity-30 cursor-not-allowed' : ''" :disabled="items.length === 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>

                                <div x-show="item.barang_id" class="mt-2">
                                    <span class="block text-[11px] text-gray-500 mb-1">Sumber Harga</span>
                                    <select x-model="item.sumber" :name="'items['+index+'][sumber]'" @change="pilihSumber(item)" class="w-full border-gray-200 bg-gray-50 rounded-lg text-xs py-1.5">
                                        <template x-for="(o, oi) in optsBarang(item.barang_id)" :key="oi">
                                            <option :value="o.k" x-text="o.label"></option>
                                        </template>
                                    </select>
                                </div>

                                <div class="mt-2 flex items-center gap-2">
                                    <div class="flex items-center rounded-lg border border-gray-200">
                                        <button type="button" @click="turunQty(item)" class="px-2.5 py-1.5 text-gray-500 hover:bg-gray-50 rounded-l-lg text-lg leading-none">−</button>
                                        <input type="number" step="0.01" min="0.01" :name="'items['+index+'][jumlah]'" :value="item.jumlah" @input="ubahJumlah(item, $event.target.value)" class="w-14 border-0 text-center text-sm py-1.5 focus:ring-0" required>
                                        <button type="button" @click="naikQty(item)" class="px-2.5 py-1.5 text-gray-500 hover:bg-gray-50 rounded-r-lg text-lg leading-none">+</button>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center text-sm text-gray-500">
                                            <span class="pr-1">@</span>
                                            <input type="number" step="0.01" :name="'items['+index+'][harga_satuan]'" x-model.number="item.harga_satuan" @input="update" placeholder="0" class="w-full border-gray-200 rounded-lg text-sm text-right py-1.5 focus:ring-0">
                                        </div>
                                    </div>
                                    <div class="shrink-0 min-w-[70px] text-right">
                                        <span class="block text-sm font-bold text-gray-800" x-text="'Rp ' + formatRupiah(subItem(item))"></span>
                                    </div>
                                </div>

                                <div class="mt-1.5 flex items-center justify-between gap-2">
                                    <span class="text-[11px] text-gray-500">Diskon item</span>
                                    <input type="number" step="0.01" :name="'items['+index+'][diskon]'" :value="item.diskon" @input="clampDiskonItem(item, $event.target.value)" placeholder="0" class="w-24 border-gray-200 rounded-lg text-right text-sm py-1 focus:ring-0">
                                </div>
                                <p x-show="item.barang_id && stokError[item.barang_id]" class="mt-1 text-[11px] text-red-600">Melebihi stok tersedia.</p>
                            </div>
                        </template>
                        <p x-show="items.length === 0" class="py-8 text-center text-sm text-gray-400">Keranjang kosong — klik item di panel kiri untuk menambah.</p>
                    </div>

                    <div class="px-4 py-4 border-t border-gray-100 bg-gray-50 space-y-2.5">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Subtotal</span><span class="font-semibold" x-text="'Rp ' + formatRupiah(subtotal())"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm" x-show="diskonNominal() > 0">
                            <span class="text-gray-500" x-text="'Diskon (' + diskonLabel() + ')'"></span>
                            <span class="font-semibold text-red-500">- <span x-text="'Rp ' + formatRupiah(diskonNominal())"></span></span>
                        </div>
                        <div class="flex justify-between items-center text-sm" x-show="pajakAktif()">
                            <span class="text-gray-500 text-sm" x-text="'Pajak (' + pajakRate() + '%)'"></span>
                            <span class="font-semibold text-gray-700 text-sm" x-text="'Rp ' + formatRupiah(pajakNominal())"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm" x-show="ongkir > 0">
                            <span class="text-gray-500">Ongkir</span>
                            <span class="font-semibold text-gray-700" x-text="'Rp ' + formatRupiah(ongkir)"></span>
                        </div>
                        <div class="flex justify-between items-center border-t border-gray-200 pt-2.5">
                            <span class="font-semibold text-gray-700">Grand Total</span>
                            <span class="font-bold text-emerald-700 text-xl" x-text="'Rp ' + formatRupiah(grandTotal())"></span>
                        </div>
                        <label class="flex items-center gap-1.5 text-[11px] text-gray-600 pt-1" title="Harga aktual transaksi ini disinkronkan ke Daftar Harga (harga lama tetap tercatat di riwayat)">
                            <input type="checkbox" name="sync_harga" value="1" {{ old('sync_harga', $def['sync_harga'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            Jadikan harga ini acuan (perbarui Daftar Harga)
                        </label>
                    </div>

                    <div class="px-4 py-4 space-y-2">
                        <p x-show="items.length === 0" class="text-xs text-red-500 text-center">Pilih minimal 1 item untuk menyimpan.</p>
                        <button type="button" @click="bukaPreview()" :disabled="items.length === 0"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Preview & Simpan
                        </button>
                        <button type="button" @click="kirim('pending')" :disabled="items.length === 0"
                                title="Simpan sebagai draft tanpa memengaruhi stok & jurnal; posting dari daftar transaksi nanti"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-300 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed rounded-lg transition">
                            Simpan Draft
                        </button>
                    </div>
                </x-card>
            </div>
        </div>
    <div x-show="preview" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @keydown.escape.window="preview = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto" @click.outside="preview = false">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-800">Preview Penjualan</h2>
                <button type="button" @click="preview = false" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <div class="p-5 text-sm space-y-3">
                <template x-if="items.length">
                    <div class="space-y-2">
                        <template x-for="(it, i) in items" :key="i">
                            <div class="flex justify-between gap-3">
                                <span class="text-gray-700 truncate">
                                    <template x-if="infoBarang(it.barang_id)?.foto">
                                        <img :src="infoBarang(it.barang_id).foto" class="inline w-6 h-6 rounded object-cover mr-1.5 align-middle" alt="">
                                    </template>
                                    <span class="inline-block align-middle" x-text="(infoBarang(it.barang_id)?.label || 'Item') + ' × ' + it.jumlah"></span>
                                </span>
                                <span class="font-medium shrink-0" x-text="'Rp ' + formatRupiah(subItem(it))"></span>
                            </div>
                        </template>
                    </div>
                </template>
                <div class="border-t border-gray-100 pt-2 space-y-1 text-gray-600">
                    <div class="flex justify-between"><span>Subtotal</span><span x-text="'Rp ' + formatRupiah(subtotal())"></span></div>
                    <div class="flex justify-between" x-show="diskonNominal() > 0"><span x-text="'Diskon (' + diskonLabel() + ')'"></span><span class="text-red-500" x-text="'- Rp ' + formatRupiah(diskonNominal())"></span></div>
                    <div class="flex justify-between" x-show="pajakAktif()"><span x-text="'Pajak (' + pajakRate() + '%)'"></span><span x-text="'Rp ' + formatRupiah(pajakNominal())"></span></div>
                    <div class="flex justify-between" x-show="ongkir > 0"><span>Ongkir</span><span x-text="'Rp ' + formatRupiah(ongkir)"></span></div>
                    <div class="flex justify-between font-bold text-gray-900 pt-1 border-t border-gray-100">
                        <span>Grand Total</span><span class="text-emerald-700" x-text="'Rp ' + formatRupiah(grandTotal())"></span>
                    </div>
                </div>
            </div>
            <div class="px-5 py-4 border-t border-gray-100 flex flex-col sm:flex-row gap-2">
                <button type="button" @click="preview = false" class="sm:flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg">Batal</button>
                <button type="button" @click="kirim('pending')" class="sm:flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-300 hover:bg-gray-50 rounded-lg">Simpan Draft</button>
                <button type="button" @click="kirim('posted')" class="sm:flex-1 px-4 py-2.5 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg">Posting</button>
            </div>
        </div>
    </div>
    </form>

    <script>
        var penjualanEdit = @json($editData ?? null);
        function penjualanForm(initial) {
            var pajakRateMap = @json($pajakList->pluck('rate', 'id'));
            var hargaMap = @json($hargaMap);
            var barangHargaMap = @json($barangHargaMap);
            var priceOpts = @json($priceOpts);
            var edit = initial && initial.items ? initial : null;
            function tierLabel(t) {
                var min = parseFloat(t.min_qty);
                var max = (t.max_qty === null || t.max_qty === undefined) ? null : parseFloat(t.max_qty);
                return (max === null) ? min + '+ pcs' : (min === max ? min + ' pcs' : 'qty ' + min + '-' + max);
            }
            return {
                items: edit ? edit.items.map(function (it) {
                    return {
                        barang_id: it.barang_id,
                        jumlah: parseFloat(it.jumlah) || 0,
                        harga_satuan: parseFloat(it.harga_satuan) || 0,
                        diskon: parseFloat(it.diskon) || 0,
                        sumber: it.sumber || 'standar',
                    };
                }) : [],
                customerId: edit ? String(edit.customer_id || '') : '',
                metodeBayar: edit ? (edit.metode_bayar || 'tunai') : @json(old('metode_bayar', 'tunai')),
                pajakId: edit ? (edit.pajak_id ? String(edit.pajak_id) : '') : @json(old('pajak_id', '')),
                diskon: edit ? (parseFloat(edit.diskon) || 0) : (parseFloat(@json(old('diskon', '0'))) || 0),
                ongkir: edit ? (parseFloat(edit.ongkir) || 0) : (parseFloat(@json(old('ongkir', '0'))) || 0),
                qGaleri: '',
                galeri: @json($galeri),
                gudangId: '',
                stokById: @json($stokById ?? []),
                stokError: {},
                stokHabis: '',
                preview: false,
                init() {
                    var c = document.getElementById('customer_id');
                    this.customerId = c ? c.value : '';
                    this.gantiGudang();
                },
                gantiGudang() {
                    var el = document.getElementById('gudang_id');
                    this.gudangId = el ? (el.value || '') : '';
                    this.stokError = {};
                },
                stokBarang(b) {
                    if (!b || b.tipe === 'jasa') return Infinity;
                    var map = this.stokById[this.gudangId] || null;
                    if (map && map[b.id] !== undefined && map[b.id] !== null) return parseFloat(map[b.id]);
                    return parseFloat(b.stok) || 0;
                },
                bukaPreview() {
                    this.syncAll();
                    if (this.items.length === 0) return;
                    this.preview = true;
                },
                kirim(aksi) {
                    this.syncAll();
                    if (this.items.length === 0) return;
                    this.preview = false;
                    this.$refs.aksiInput.value = aksi;
                    this.$el.closest('form').submit();
                },
                pajakRate() { return this.pajakId ? (pajakRateMap[this.pajakId] || 0) : 0; },
                hapusItem(i) {
                    if (this.items.length > 1) {
                        var id = this.items[i].barang_id;
                        this.items.splice(i, 1);
                        if (id) delete this.stokError[id];
                    }
                },
                optsBarang(brgId) {
                    if (!brgId) return [];
                    var cust = this.customerId || (document.getElementById('customer_id') ? document.getElementById('customer_id').value : '');
                    return (cust && priceOpts[cust] && priceOpts[cust][brgId]) ? priceOpts[cust][brgId] : [];
                },
                badgeClass(k) {
                    if (!k || k === 'standar') return 'bg-gray-100 text-gray-500';
                    if (k === 'auto' || k.indexOf('dh') === 0) return 'bg-emerald-100 text-emerald-700';
                    if (k.indexOf('hist') === 0) return 'bg-sky-100 text-sky-700';
                    if (k === 'last') return 'bg-amber-100 text-amber-700';
                    return 'bg-gray-100 text-gray-500';
                },
                pilihBarang(item) {
                    var opts = this.optsBarang(item.barang_id);
                    var hasLast = false;
                    for (var i = 0; i < opts.length; i++) { if (opts[i].k === 'last') { hasLast = true; break; } }
                    item.sumber = hasLast ? 'last' : (opts.length ? 'auto' : 'standar');
                    this.setHarga(item, true);
                },
                pilihSumber(item) {
                    if (!item.barang_id) return;
                    if (item.sumber === 'auto') { this.setHargaAuto(item); return; }
                    var o = null;
                    var opts = this.optsBarang(item.barang_id);
                    for (var i = 0; i < opts.length; i++) {
                        if (opts[i].k === item.sumber) { o = opts[i]; break; }
                    }
                    var p = (o && o.harga !== null && o.harga !== undefined) ? parseFloat(o.harga) : null;
                    if (item.sumber === 'standar' && barangHargaMap[item.barang_id] !== undefined) {
                        p = parseFloat(barangHargaMap[item.barang_id]);
                    }
                    if (p !== null && p !== undefined) {
                        item.harga_satuan = p;
                        item.auto = null;
                    }
                    item.sumberLabel = o ? o.label : (item.sumber === 'standar' ? 'Standar barang' : (item.sumber || ''));
                    item.dhTier = null;
                },
                setHarga(item, force) {
                    if (!item.barang_id) { item.sumber = ''; item.sumberLabel = null; item.dhTier = null; return; }
                    if (item.sumber === 'auto') { this.setHargaAuto(item, force); return; }
                    if (item.sumber && item.sumber !== '') { this.pilihSumber(item); return; }

                    var opts = this.optsBarang(item.barang_id);
                    var hasLast = false;
                    for (var i = 0; i < opts.length; i++) { if (opts[i].k === 'last') { hasLast = true; break; } }
                    item.sumber = hasLast ? 'last' : (opts.length ? 'auto' : 'standar');
                    if (item.sumber === 'auto') { this.setHargaAuto(item, force); return; }
                    this.pilihSumber(item);
                },
                autoHarga(item) {
                    var cust = this.customerId || (document.getElementById('customer_id') ? document.getElementById('customer_id').value : '');
                    var qty = parseFloat(item.jumlah) || 0;
                    var tiers = (cust && hargaMap[cust] && hargaMap[cust][item.barang_id])
                        ? hargaMap[cust][item.barang_id]
                        : null;

                    // Harga dari daftar_harga customer (tiered) — prefer range tersempit
                    var matched = null;
                    if (tiers && tiers.length > 0) {
                        var best = null, bestRange = null;
                        for (var i = 0; i < tiers.length; i++) {
                            var t = tiers[i];
                            if (qty >= t.min_qty && (t.max_qty === null || qty <= t.max_qty)) {
                                var range = (t.max_qty === null) ? 999999999 : (t.max_qty - t.min_qty);
                                if (bestRange === null || range < bestRange) { best = t; bestRange = range; }
                            }
                        }
                        if (best === null && qty === 0) best = tiers[0];
                        matched = best;
                    }

                    var dh = (matched !== null && matched !== undefined) ? parseFloat(matched.harga) : null;

                    // Fallback ke harga_jual barang jika tidak ada di daftar_harga
                    var p = dh;
                    if (p === null || p === undefined) {
                        p = barangHargaMap[item.barang_id] !== undefined ? barangHargaMap[item.barang_id] : null;
                    }

                    return {
                        p: p,
                        dhTier: matched ? tierLabel(matched) : null,
                        sumberLabel: matched ? ('Daftar Harga · ' + tierLabel(matched)) : 'Standar barang',
                    };
                },
                setHargaAuto(item, force) {
                    var res = this.autoHarga(item);
                    item.dhTier = res.dhTier;
                    item.sumberLabel = res.sumberLabel;
                    if (res.p === null || res.p === undefined) return;
                    var cur = parseFloat(item.harga_satuan) || 0;
                    var av = (item.auto !== undefined) ? item.auto : null;
                    if (force || cur === 0 || (av !== null && cur === av)) {
                        item.harga_satuan = res.p;
                        item.auto = res.p;
                    }
                },
                gantiPembeli() {
                    var cust = document.getElementById('customer_id') ? document.getElementById('customer_id').value : '';
                    this.customerId = cust;
                    this.items.forEach(it => {
                        if (it.barang_id) this.pilihBarang(it, true);
                    });
                },
                hasilGaleri() {
                    var q = (this.qGaleri || '').toLowerCase();
                    if (!q) return this.galeri;
                    return this.galeri.filter(function (b) {
                        return (b.label || '').toLowerCase().indexOf(q) !== -1 || (b.kode || '').toLowerCase().indexOf(q) !== -1;
                    });
                },
                sumberGaleri(brgId) {
                    var opts = this.optsBarang(brgId);
                    var hasLast = false;
                    for (var i = 0; i < opts.length; i++) { if (opts[i].k === 'last') { hasLast = true; break; } }
                    if (hasLast) return { k: 'last', label: 'Harga terakhir' };
                    return opts.length ? { k: 'auto', label: 'Daftar Harga' } : { k: 'standar', label: 'Standar' };
                },
                hargaGaleri(brgId) {
                    var res = this.autoHarga({ barang_id: brgId, jumlah: 1 });
                    return (res.p !== null && res.p !== undefined) ? res.p : 0;
                },
                maxStok(brgId) {
                    var b = brgId ? this.infoBarang(brgId) : null;
                    if (b && b.tipe === 'barang') return this.stokBarang(b);
                    return Infinity;
                },
                ubahJumlah(item, raw) {
                    var v = (typeof raw === 'number') ? raw : (parseFloat(raw) || 0);
                    var cap = this.maxStok(item.barang_id);
                    if (v > cap) {
                        v = cap;
                        this.stokError[item.barang_id] = true;
                    } else {
                        this.stokError[item.barang_id] = false;
                    }
                    if (v < 0.01) v = 0.01;
                    item.jumlah = v;
                    if (item.sumber === 'auto') this.setHarga(item);
                    this.update();
                    return v;
                },
                clampDiskonItem(item, raw) {
                    var v = (typeof raw === 'number') ? raw : (parseFloat(raw) || 0);
                    var max = (parseFloat(item.jumlah) || 0) * (parseFloat(item.harga_satuan) || 0);
                    if (v > max) v = max;
                    if (v < 0) v = 0;
                    item.diskon = v;
                    this.update();
                },
                validasiDiskonGlobal(raw) {
                    var d = parseFloat(raw !== undefined ? raw : this.diskon) || 0;
                    var tipe = document.getElementById('diskon_tipe') ? document.getElementById('diskon_tipe').value : 'nominal';
                    if (tipe === 'persen') {
                        if (d > 100) d = 100;
                    } else {
                        var max = this.subtotal();
                        if (d > max) d = max;
                    }
                    this.diskon = d;
                    this.update();
                },
                tambahGaleri(brgId) {
                    if (!brgId) return;
                    var existing = null;
                    for (var i = 0; i < this.items.length; i++) {
                        if (this.items[i].barang_id == brgId) {
                            existing = this.items[i];
                            break;
                        }
                    }
                    if (existing) {
                        existing.jumlah = this.ubahJumlah(existing, (parseFloat(existing.jumlah) || 0) + 1);
                        if (existing.sumber === 'auto' || existing.sumber === 'last') this.setHarga(existing);
                        return;
                    }
                    var cap = this.maxStok(brgId);
                    if (cap <= 0) {
                        var info = this.infoBarang(brgId);
                        this.stokHabis = (info ? info.label : 'Item') + ' tidak tersedia — stok habis.';
                        return;
                    }
                    this.items.push({ barang_id: brgId, jumlah: Math.min(1, cap), diskon: 0, harga_satuan: 0 });
                    this.pilihBarang(this.items[this.items.length - 1]);
                },
                update() {},
                jumlahQty() {
                    var t = 0;
                    this.items.forEach(function (it) { if (it.barang_id) t += (parseFloat(it.jumlah) || 0); });
                    return t;
                },
                infoBarang(brgId) {
                    for (var i = 0; i < this.galeri.length; i++) { if (this.galeri[i].id == brgId) return this.galeri[i]; }
                    return null;
                },
                naikQty(item) {
                    this.ubahJumlah(item, (parseFloat(item.jumlah) || 0) + 1);
                },
                turunQty(item) {
                    this.ubahJumlah(item, (parseFloat(item.jumlah) || 0) - 1);
                },
                subItem(it) { return ((parseFloat(it.jumlah)||0) * (parseFloat(it.harga_satuan)||0)) - (parseFloat(it.diskon)||0); },
                subtotal() { return this.items.reduce((s, it) => s + this.subItem(it), 0); },
                diskonNominal() {
                    if (this.diskon <= 0) return 0;
                    var tipe = document.getElementById('diskon_tipe').value;
                    if (tipe === 'persen') return this.subtotal() * (this.diskon / 100);
                    return Math.min(this.diskon, this.subtotal());
                },
                diskonLabel() {
                    var tipe = document.getElementById('diskon_tipe').value;
                    return tipe === 'persen' ? this.diskon + '%' : this.formatRupiah(this.diskon || 0);
                },
                dasarPajak() { return this.subtotal() - this.diskonNominal(); },
                pajakAktif() { return this.pajakRate() > 0; },
                pajakNominal() { return this.dasarPajak() * (this.pajakRate() / 100); },
                grandTotal() { return this.dasarPajak() + this.pajakNominal() + this.ongkir; },
                ubahMetodeBayar() { this.metodeBayar = document.getElementById('metode_bayar').value; },
                syncPajak() { this.pajakId = document.getElementById('pajak_id').value; },
                syncDiskonTipe() {},
                syncAll() {
                    this.ubahMetodeBayar(); this.syncPajak(); this.gantiGudang();
                    var d = document.getElementById('diskon_global'); this.diskon = parseFloat(d ? d.value : 0) || 0;
                    var og = document.getElementById('ongkir_input'); this.ongkir = parseFloat(og ? og.value : 0) || 0;
                },
                formatRupiah(n) { return (Math.round(n) ).toLocaleString('id-ID'); }
            };
        }
    </script>
</x-app-layout>