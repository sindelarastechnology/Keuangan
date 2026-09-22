<x-app-layout>
    <x-slot name="title">{{ isset($aset) ? 'Edit' : 'Tambah' }} Aset Tetap</x-slot>

    <x-page-header title="{{ isset($aset) ? 'Edit Aset Tetap' : 'Tambah Aset Tetap' }}">
        <a href="{{ route('aset.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <div class="max-w-4xl space-y-4">
        <x-card>
            <form method="POST" action="{{ isset($aset) ? route('aset.update', $aset) : route('aset.store') }}" class="p-6 space-y-6" enctype="multipart/form-data" x-data="asetForm()">
                @csrf
                @if(isset($aset)) @method('PUT') @endif

                {{-- ===== SECTION 1: DATA BARANG ===== --}}
                <section>
                    <div class="flex items-start gap-3 mb-4">
                        <div class="shrink-0 w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-bold">1</div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">Data Barang</h2>
                            <p class="text-sm text-gray-500">Isi informasi barangnya saja.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <x-input-label for="nama" value="Nama Barang" />
                            <x-text-input id="nama" type="text" name="nama" value="{{ old('nama', $aset->nama ?? '') }}" class="mt-1" placeholder="cth: Mesin Jahit Portable" required autofocus />
                            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="kategori" value="Jenis Barang" />
                            <x-select id="kategori" name="kategori" class="mt-1" x-model="kategori" @change="pilihJenis()">
                                <option value="">-- Pilih Jenis Barang --</option>
                                @foreach($templates as $tpl)
                                    <option value="{{ $tpl->nama_kategori }}" @selected(old('kategori', $aset->kategori ?? '') === $tpl->nama_kategori)>{{ $tpl->nama_kategori }}</option>
                                @endforeach
                            </x-select>
                            <x-input-error :messages="$errors->get('kategori')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tanggal_perolehan" value="Tanggal Beli" />
                            <x-text-input id="tanggal_perolehan" type="date" name="tanggal_perolehan" value="{{ old('tanggal_perolehan', isset($aset) ? $aset->tanggal_perolehan->format('Y-m-d') : now()->format('Y-m-d')) }}" class="mt-1" required />
                            <x-input-error :messages="$errors->get('tanggal_perolehan')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="harga_perolehan" value="Harga Beli (Rp)" />
                            <x-text-input id="harga_perolehan" type="number" step="0.01" min="1" name="harga_perolehan" x-model="harga" @input="onHargaMasuk()" value="{{ old('harga_perolehan', $aset->harga_perolehan ?? '') }}" class="mt-1" placeholder="cth: 3500000" required />
                            <p class="mt-1 text-xs text-gray-500">Harga beli + ongkir</p>
                            <x-input-error :messages="$errors->get('harga_perolehan')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="masa_manfaat_bulan" value="Lama Dipakai" />
                            <x-select id="masa_manfaat_bulan" name="masa_manfaat_bulan" class="mt-1" x-model="masa" required>
                                <option value="">-- Pilih Lama Dipakai --</option>
                                @foreach($masaOptions as $m)
                                    <option value="{{ $m['bulan'] }}" @selected(old('masa_manfaat_bulan', $aset->masa_manfaat_bulan ?? '') == $m['bulan'])>{{ $m['tahun'] }} Tahun</option>
                                @endforeach
                            </x-select>
                            <x-input-error :messages="$errors->get('masa_manfaat_bulan')" class="mt-2" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-input-label for="nilai_residu" value="Perkiraan Harga Jual Nanti (Rp)" />
                            <x-text-input id="nilai_residu" type="number" step="0.01" min="0" name="nilai_residu" x-model="residu" @input="residuKustom = true" value="{{ old('nilai_residu', $aset->nilai_residu ?? '') }}" class="mt-1" placeholder="0" />
                            <p class="mt-1 text-xs text-gray-500">Berapa kira-kira dijual saat sudah rusak? Isi 0 jika tidak ada.</p>
                            <x-input-error :messages="$errors->get('nilai_residu')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="lokasi" value="Taruh di" />
                            <x-text-input id="lokasi" type="text" name="lokasi" x-model="lokasi" value="{{ old('lokasi', $aset->lokasi ?? '') }}" class="mt-1" placeholder="cth: Ruang Produksi" />
                            <p class="mt-1 text-xs text-gray-500">Lokasi barang disimpan</p>
                            <x-input-error :messages="$errors->get('lokasi')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="foto" value="Upload Foto (opsional)" />
                            @if(isset($aset) && $aset->foto)
                                <div class="mt-1 flex items-center gap-3">
                                    <img src="{{ asset('storage/'.$aset->foto) }}" alt="Foto {{ $aset->nama }}" class="w-14 h-14 object-cover rounded-lg border border-gray-200">
                                    <span class="text-xs text-gray-500">Foto saat ini. Ganti dengan file baru jika perlu.</span>
                                </div>
                            @endif
                            <input id="foto" type="file" name="foto" accept="image/jpeg,image/png,image/webp"
                                   class="mt-1 block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-emerald-700 hover:file:bg-emerald-100">
                            <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-input-label for="deskripsi" value="Catatan (opsional)" />
                            <x-textarea id="deskripsi" name="deskripsi" rows="2" class="mt-1" placeholder="Deskripsi singkat barang (opsional)">{{ old('deskripsi', $aset->deskripsi ?? '') }}</x-textarea>
                            <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        <span class="font-semibold">Info Penyusutan: </span>
                        <span x-show="bisaHitung()">
                            Akan jadi biaya <strong x-text="'Rp ' + bebanBulanan().toLocaleString('id-ID')"></strong>/bulan selama <strong x-text="tahunLamaDipakai() + ' tahun'"></strong>
                        </span>
                        <span x-show="!bisaHitung()" class="text-emerald-600">Isi Harga dan Lama Dipakai dulu</span>
                    </div>
                </section>

                @if(! isset($aset))
                    {{-- ===== SECTION 2: PEMBAYARAN ===== --}}
                    <section>
                        <div class="flex items-start gap-3 mb-4">
                            <div class="shrink-0 w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold">2</div>
                            <div>
                                <h2 class="text-base font-semibold text-gray-900">Pembayaran</h2>
                                <p class="text-sm text-gray-500">Opsional. Jika belum dibayar, tidak ada jurnal yang dibuat.</p>
                            </div>
                        </div>

                        <div class="rounded-lg border border-gray-200 p-4 space-y-4">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                <input type="hidden" name="catat_perolehan" :value="bayarSekarang ? '1' : '0'">
                                <input type="checkbox" x-model="bayarSekarang" class="rounded text-emerald-600">
                                Pembayaran sudah dilakukan sekarang
                            </label>

                            <div x-cloak x-show="bayarSekarang" class="border-t border-gray-100 pt-4 space-y-4">
                                <p class="text-sm text-gray-500">Bayar dari:</p>
                                <div class="space-y-2">
                                    @foreach($rekenings as $rek)
                                        <label class="flex items-center gap-2 text-sm text-gray-700">
                                            <input type="radio" name="sumber_dana" value="rekening:{{ $rek->id }}" x-model="bayarDari" class="text-emerald-600">
                                            <span class="font-medium">{{ $rek->nama }}</span>
                                            <span class="text-xs text-gray-500">{{ $rek->jenis === 'kas' ? 'Kas' : 'Bank' }}</span>
                                        </label>
                                    @endforeach
                                    <label class="flex items-center gap-2 text-sm text-gray-700">
                                        <input type="radio" name="sumber_dana" value="utang" x-model="bayarDari" class="text-emerald-600">
                                        <span class="font-medium">Bayar Nanti / Utang</span>
                                    </label>
                                </div>
                                <x-input-error :messages="$errors->get('sumber_dana')" class="mt-1" />

                                <div x-cloak x-show="bayarDari === 'utang'">
                                    <x-input-label for="supplier_id" value="Nama Supplier" />
                                    <x-select id="supplier_id" name="supplier_id" x-model="supplierId" x-bind:required="bayarDari === 'utang'" class="mt-1">
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach($suppliers as $sup)
                                            <option value="{{ $sup->id }}" @selected(old('supplier_id') == $sup->id)>{{ $sup->nama }}</option>
                                        @endforeach
                                    </x-select>
                                    <x-input-error :messages="$errors->get('supplier_id')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </section>
                @else
                    <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-700">
                        Pembelian barang dicatat di pembukuan saat aset dibuat dan tidak dapat diubah melalui form ini.
                    </div>
                @endif

                {{-- ===== ACCORDION: SETELAN LANJUTAN UNTUK KEUANGAN ===== --}}
                <section class="rounded-lg border border-gray-200">
                    <button type="button" @click="lanjutanTerbuka = !lanjutanTerbuka" class="w-full flex items-center justify-between px-4 py-3 text-left">
                        <span class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            Setelan Lanjutan Untuk Keuangan
                        </span>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="lanjutanTerbuka ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </button>

                    <div x-cloak x-show="lanjutanTerbuka" class="border-t border-gray-200 p-4 space-y-4">
                        <p class="text-sm text-gray-500">Akun dicarikan otomatis begitu Jenis Barang dipilih. Ubah bila perlu.</p>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div>
                                <x-input-label for="akun_aset_id" value="Akun Aset (Debit)" />
                                <x-select id="akun_aset_id" name="akun_aset_id" class="mt-1" x-model="akunAsetId" x-bind:disabled="! isKeuangan" required>
                                    @foreach($akunAsetList as $akun)
                                        <option value="{{ $akun->id }}" @selected(old('akun_aset_id', $aset->akun_aset_id ?? $defaultAkunAset) == $akun->id)>{{ $akun->kode }} - {{ $akun->nama }}</option>
                                    @endforeach
                                </x-select>
                                <x-input-error :messages="$errors->get('akun_aset_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="akun_akumulasi_id" value="Akun Akumulasi (Kredit)" />
                                <x-select id="akun_akumulasi_id" name="akun_akumulasi_id" class="mt-1" x-model="akunAkumulasiId" x-bind:disabled="! isKeuangan" required>
                                    @foreach($akunAsetList as $akun)
                                        <option value="{{ $akun->id }}" @selected(old('akun_akumulasi_id', $aset->akun_akumulasi_id ?? $defaultAkunAkumulasi) == $akun->id)>{{ $akun->kode }} - {{ $akun->nama }}</option>
                                    @endforeach
                                </x-select>
                                <x-input-error :messages="$errors->get('akun_akumulasi_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="akun_beban_id" value="Akun Beban Penyusutan" />
                                <x-select id="akun_beban_id" name="akun_beban_id" class="mt-1" x-model="akunBebanId" x-bind:disabled="! isKeuangan" required>
                                    @foreach($akunBebanList as $akun)
                                        <option value="{{ $akun->id }}" @selected(old('akun_beban_id', $aset->akun_beban_id ?? $defaultAkunBeban) == $akun->id)>{{ $akun->kode }} - {{ $akun->nama }}</option>
                                    @endforeach
                                </x-select>
                                <x-input-error :messages="$errors->get('akun_beban_id')" class="mt-2" />
                            </div>
                        </div>

                        @if(isset($aset))
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 border-t border-gray-100 pt-4">
                                <div>
                                    <x-input-label for="status" value="Status" />
                                    <x-select id="status" name="status" class="mt-1">
                                        <option value="aktif" @selected(old('status', $aset->status) === 'aktif')>Aktif</option>
                                        <option value="selesai" @selected(old('status', $aset->status) === 'selesai')>Selesai (hentikan penyusutan)</option>
                                    </x-select>
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="keterangan" value="Keterangan Internal" />
                                    <x-text-input id="keterangan" type="text" name="keterangan" value="{{ old('keterangan', $aset->keterangan ?? '') }}" class="mt-1" />
                                    <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="status" value="aktif">
                            <div>
                                <x-input-label for="keterangan" value="Keterangan Internal" />
                                <x-text-input id="keterangan" type="text" name="keterangan" value="{{ old('keterangan', '') }}" class="mt-1" />
                                <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                            </div>
                        @endif
                    </div>
                </section>

                <div class="flex justify-end pt-2">
                    <x-loading-button>
                        {{ isset($aset) ? 'Simpan Perubahan' : 'Simpan Aset' }}
                    </x-loading-button>
                </div>
            </form>
        </x-card>
    </div>

    <script>
        function asetForm() {
            return {
                templates: @json($templateOptions),
                isKeuangan: @json($isKeuangan),
                harga: @json(old('harga_perolehan', $aset->harga_perolehan ?? '')),
                residu: @json(old('nilai_residu', $aset->nilai_residu ?? '')),
                masa: @json(old('masa_manfaat_bulan', $aset->masa_manfaat_bulan ?? '')),
                kategori: @json(old('kategori', $aset->kategori ?? '')),
                lokasi: @json(old('lokasi', $aset->lokasi ?? '')),
                bayarSekarang: {{ old('catat_perolehan', false) ? 'true' : 'false' }},
                bayarDari: @json(old('sumber_dana', '')),
                supplierId: @json(old('supplier_id', '')),
                akunAsetId: @json(old('akun_aset_id', $aset->akun_aset_id ?? $defaultAkunAset)),
                akunAkumulasiId: @json(old('akun_akumulasi_id', $aset->akun_akumulasi_id ?? $defaultAkunAkumulasi)),
                akunBebanId: @json(old('akun_beban_id', $aset->akun_beban_id ?? $defaultAkunBeban)),
                residuKustom: false,
                lanjutanTerbuka: false,

                templateSekarang() {
                    return this.templates.find(t => t.nama === this.kategori) || null;
                },
                pilihJenis() {
                    const t = this.templateSekarang();
                    if (t) {
                        this.masa = t.masa;
                        this.residuKustom = false;
                        if (this.isKeuangan) {
                            this.akunAsetId = t.akunAset;
                            this.akunAkumulasiId = t.akunAkumulasi;
                            this.akunBebanId = t.akunBeban;
                        }
                    } else {
                        this.residuKustom = false;
                    }
                    this.hitungResidu();
                },
                onHargaMasuk() {
                    if (!this.residuKustom) this.hitungResidu();
                },
                hitungResidu() {
                    const t = this.templateSekarang();
                    const persen = t ? t.persen : 0;
                    const harga = parseFloat(this.harga) || 0;
                    this.residu = Math.round(harga * persen / 100);
                },
                bisaHitung() {
                    return (parseFloat(this.harga) || 0) > 0 && (parseInt(this.masa) || 0) > 0;
                },
                bebanBulanan() {
                    if (!this.bisaHitung()) return 0;
                    const nilai = (parseFloat(this.harga) || 0) - (parseFloat(this.residu) || 0);
                    return Math.max(0, Math.round(nilai / (parseInt(this.masa) || 1)));
                },
                tahunLamaDipakai() {
                    return Math.round((parseInt(this.masa) || 0) / 12);
                },
            };
        }
    </script>
</x-app-layout>