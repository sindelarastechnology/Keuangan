<x-app-layout>
    <x-slot name="title">{{ isset($barang) ? 'Edit' : 'Tambah' }} Jasa</x-slot>

    <x-page-header title="{{ isset($barang) ? 'Edit Jasa' : 'Tambah Jasa' }}">
        <a href="{{ route('jasa.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <div class="max-w-3xl">
        <x-card>
            <form method="POST" action="{{ isset($barang) ? route('jasa.update', $barang) : route('jasa.store') }}"
                  class="p-6 space-y-5" enctype="multipart/form-data"
                  x-data="jasaForm({
                    fotoPreview: {{ isset($barang) && $barang->foto ? '\'' . $barang->foto_url . '\'' : 'null' }}
                })">
                @csrf
                @if(isset($barang)) @method('PUT') @endif

                @if(isset($barang))
                    <div>
                        <x-input-label for="kode" value="Kode Jasa" />
                        <x-text-input id="kode" type="text" value="{{ $barang->kode }}" class="mt-1 bg-gray-50 text-gray-500" disabled />
                        <p class="mt-1 text-xs text-gray-500">Kode tidak dapat diubah.</p>
                    </div>
                @endif

                <div>
                    <x-input-label value="Foto Jasa" />
                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-4">
                        <div class="w-40 h-40 rounded-lg overflow-hidden border border-gray-200 bg-gray-50 flex items-center justify-center">
                            <template x-if="fotoPreview">
                                <img :src="fotoPreview" alt="Preview foto" class="w-full h-full object-cover">
                            </template>
                            <template x-if="! fotoPreview">
                                <div class="text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="text-xs mt-1">Belum ada foto</p>
                                </div>
                            </template>
                        </div>
                        <div class="flex flex-col justify-center gap-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium text-emerald-700 hover:text-emerald-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                Pilih Foto
                                <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg" class="sr-only" @change="previewFoto($event)">
                            </label>
                            <p class="text-xs text-gray-500">Format: JPG/PNG, maks. 2MB.</p>
                            @if(isset($barang) && $barang->foto)
                                <label class="inline-flex items-center gap-2 text-sm text-red-600 hover:text-red-700 cursor-pointer">
                                    <input type="checkbox" name="hapus_foto" value="1" class="rounded text-red-600" @change="hapusFotoCheck($event)" x-model="hapusFoto">
                                    Hapus foto saat disimpan
                                </label>
                            @endif
                            <x-input-error :messages="$errors->get('foto')" class="mt-1" />
                        </div>
                    </div>
                </div>

                <div>
                    <x-input-label for="nama" value="Nama Jasa" />
                    <x-text-input id="nama" type="text" name="nama" value="{{ old('nama', $barang->nama ?? '') }}" class="mt-1" placeholder="cth. Jasa Instalasi / Jasa Jahit" required />
                    <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="satuan" value="Satuan" />
                        <div class="flex gap-2">
                            @php($satuanTerpilih = old('satuan', $barang->satuan ?? ''))
                            <x-select id="satuan" name="satuan" class="mt-1">
                                <option value="">-- Pilih Satuan --</option>
                                @foreach($satuanList as $s)
                                    <option value="{{ $s->nama }}" {{ $satuanTerpilih === $s->nama ? 'selected' : '' }}>{{ $s->nama }}</option>
                                @endforeach
                                @if($satuanTerpilih && ! $satuanList->pluck('nama')->contains($satuanTerpilih))
                                    <option value="{{ $satuanTerpilih }}" selected>{{ $satuanTerpilih }}</option>
                                @endif
                            </x-select>
                            <button type="button" @click="bukaModalSatuan()"
                                    title="Tambah satuan baru"
                                    class="mt-1 shrink-0 inline-flex items-center justify-center w-10 h-10 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('satuan')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="kategori" value="Kategori" />
                        <div class="flex gap-2">
                            @php($kategoriTerpilih = old('kategori', $barang->kategori ?? ''))
                            <x-select id="kategori" name="kategori" class="mt-1">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoriList as $k)
                                    <option value="{{ $k->nama }}" {{ $kategoriTerpilih === $k->nama ? 'selected' : '' }}>{{ $k->nama }}</option>
                                @endforeach
                                @if($kategoriTerpilih && ! $kategoriList->pluck('nama')->contains($kategoriTerpilih))
                                    <option value="{{ $kategoriTerpilih }}" selected>{{ $kategoriTerpilih }}</option>
                                @endif
                            </x-select>
                            <button type="button" @click="bukaModalKategori()"
                                    title="Tambah kategori baru"
                                    class="mt-1 shrink-0 inline-flex items-center justify-center w-10 h-10 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('kategori')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="harga_jual" value="Harga Jual" />
                    <x-text-input id="harga_jual" type="number" step="0.01" name="harga_jual" value="{{ old('harga_jual', $barang->harga_jual ?? '') }}" class="mt-1" />
                    <x-input-error :messages="$errors->get('harga_jual')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="keterangan" value="Keterangan" />
                    <x-textarea id="keterangan" name="keterangan" class="mt-1" rows="3">{{ old('keterangan', $barang->keterangan ?? '') }}</x-textarea>
                    <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="hidden" name="is_aktif" value="0">
                        <input type="checkbox" name="is_aktif" value="1" class="rounded text-emerald-600" {{ old('is_aktif', $barang->is_aktif ?? true) ? 'checked' : '' }}>
                        Aktif
                    </label>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('jasa.index') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800">Batal</a>
                        <x-loading-button>
                            {{ isset($barang) ? 'Simpan Perubahan' : 'Simpan Jasa' }}
                        </x-loading-button>
                    </div>
                </div>

                {{-- Modal tambah satuan --}}
                <x-modal name="tambah-satuan" :show="false" maxWidth="sm">
                    <div class="p-6" x-data="satuanModalForm('{{ str_replace(url('/'), '', route('barang.tambah-satuan')) }}', 'satuan')">
                        <h3 class="text-lg font-semibold text-gray-800">Tambah Satuan Baru</h3>
                        <div class="mt-4 space-y-3">
                            <div>
                                <x-input-label for="satuan_baru" value="Nama Satuan" />
                                <x-text-input id="satuan_baru" type="text" x-model="nama" class="mt-1" placeholder="cth. pcs / kg / meter" @keydown.enter.prevent="simpan()" />
                            </div>
                            <p x-show="err" x-text="err" class="text-sm text-red-600"></p>
                            <div class="flex justify-end gap-2 pt-2">
                                <x-secondary-button type="button" @click="$dispatch('close-modal', 'tambah-satuan')">Batal</x-secondary-button>
                                <button type="button" @click="simpan()" x-bind:disabled="loading"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 disabled:opacity-60 disabled:cursor-not-allowed transition">
                                    <span x-show="!loading">Simpan Satuan</span>
                                    <span x-show="loading" class="inline-flex items-center gap-2">
                                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-6.219-8.56"/></svg>
                                        Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </x-modal>

                {{-- Modal tambah kategori --}}
                <x-modal name="tambah-kategori" :show="false" maxWidth="sm">
                    <div class="p-6" x-data="satuanModalForm('{{ str_replace(url('/'), '', route('barang.tambah-kategori')) }}', 'kategori')">
                        <h3 class="text-lg font-semibold text-gray-800">Tambah Kategori Baru</h3>
                        <div class="mt-4 space-y-3">
                            <div>
                                <x-input-label for="kategori_baru" value="Nama Kategori" />
                                <x-text-input id="kategori_baru" type="text" x-model="nama" class="mt-1" placeholder="cth. Servis / Instalasi" @keydown.enter.prevent="simpan()" />
                            </div>
                            <p x-show="err" x-text="err" class="text-sm text-red-600"></p>
                            <div class="flex justify-end gap-2 pt-2">
                                <x-secondary-button type="button" @click="$dispatch('close-modal', 'tambah-kategori')">Batal</x-secondary-button>
                                <button type="button" @click="simpan()" x-bind:disabled="loading"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 disabled:opacity-60 disabled:cursor-not-allowed transition">
                                    <span x-show="!loading">Simpan Kategori</span>
                                    <span x-show="loading" class="inline-flex items-center gap-2">
                                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-6.219-8.56"/></svg>
                                        Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </x-modal>
            </form>
        </x-card>
    </div>

    @push('scripts')
        <script>
            function satuanModalForm(url, selectId) {
                return {
                    nama: '',
                    err: '',
                    loading: false,
                    async simpan() {
                        this.err = '';
                        if (!this.nama.trim()) { this.err = 'Nama tidak boleh kosong.'; return; }
                        this.loading = true;
                        try {
                            var res = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({ nama: this.nama.trim() })
                            });
                            var data = await res.json();
                            if (!res.ok) { this.err = (data.errors && data.errors.nama && data.errors.nama[0]) || 'Gagal menyimpan.'; return; }
                            var sel = document.getElementById(selectId);
                            if (sel) { var o = new Option(data.nama, data.nama, true, true); sel.add(o); }
                            this.nama = '';
                            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'tambah-' + selectId }));
                        } catch(e) {
                            this.err = 'Terjadi kesalahan koneksi.';
                        } finally {
                            this.loading = false;
                        }
                    }
                };
            }

            function jasaForm({ fotoPreview }) {
                return {
                    fotoPreview: fotoPreview ? fotoPreview.replace(/&amp;/g, '&') : null,
                    hapusFoto: false,

                    previewFoto(event) {
                        const file = event.target.files?.[0];
                        if (! file) return;
                        if (! ['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
                            event.target.value = '';
                            alert('Format foto harus JPG atau PNG.');
                            return;
                        }
                        this.hapusFoto = false;
                        const reader = new FileReader();
                        reader.onload = (e) => { this.fotoPreview = e.target.result; };
                        reader.readAsDataURL(file);
                    },

                    hapusFotoCheck(event) {
                        if (event.target.checked) {
                            this.fotoPreview = null;
                        } else {
                            this.fotoPreview = {{ isset($barang) && $barang->foto ? '\'' . $barang->foto_url . '\'' : 'null' }};
                        }
                    },

                    bukaModalSatuan() {
                        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'tambah-satuan' }));
                    },

                    bukaModalKategori() {
                        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'tambah-kategori' }));
                    },
                };
            }
        </script>
    @endpush
</x-app-layout>