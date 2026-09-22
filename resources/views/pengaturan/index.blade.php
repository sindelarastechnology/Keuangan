<x-app-layout>
    <x-slot name="title">Pengaturan</x-slot>

    <x-page-header title="Pengaturan" subtitle="Kelola pengaturan aplikasi"></x-page-header>

    <div x-data="{ tab: '{{ request('tab', 'perusahaan') }}' }">

        {{-- Tab Navigation --}}
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex gap-6">
                <button type="button"
                    @click="tab = 'perusahaan'"
                    :class="tab === 'perusahaan' ? 'border-emerald-600 text-emerald-700 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="inline-flex items-center gap-2 py-3 border-b-2 text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Perusahaan
                </button>
                <button type="button"
                    @click="tab = 'satuan'"
                    :class="tab === 'satuan' ? 'border-emerald-600 text-emerald-700 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="inline-flex items-center gap-2 py-3 border-b-2 text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M3 10h18M3 14h18M3 18h18"/></svg>
                    Satuan
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 text-gray-600 text-xs font-medium">{{ $satuanList->count() }}</span>
                </button>
                <button type="button"
                    @click="tab = 'kategori'"
                    :class="tab === 'kategori' ? 'border-emerald-600 text-emerald-700 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="inline-flex items-center gap-2 py-3 border-b-2 text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Kategori
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 text-gray-600 text-xs font-medium">{{ $kategoriList->count() }}</span>
                </button>
            </nav>
        </div>

        {{-- Tab: Perusahaan --}}
        <div x-show="tab === 'perusahaan'" x-cloak>
            <div class="max-w-2xl">
                <x-card>
                    <form method="POST" action="{{ route('pengaturan.update') }}">
                        @csrf
                        <div class="p-5 space-y-5">
                            <div>
                                <x-input-label for="nama_perusahaan" value="Nama Perusahaan" />
                                <x-text-input id="nama_perusahaan" type="text" name="nama_perusahaan" value="{{ $pengaturan['nama_perusahaan'] ?? '' }}" class="mt-1 w-full" required />
                                <x-input-error :messages="$errors->get('nama_perusahaan')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="alamat_perusahaan" value="Alamat" />
                                <x-text-input id="alamat_perusahaan" type="text" name="alamat_perusahaan" value="{{ $pengaturan['alamat_perusahaan'] ?? '' }}" class="mt-1 w-full" />
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <x-input-label for="telepon_perusahaan" value="Telepon" />
                                    <x-text-input id="telepon_perusahaan" type="text" name="telepon_perusahaan" value="{{ $pengaturan['telepon_perusahaan'] ?? '' }}" class="mt-1 w-full" />
                                </div>
                                <div>
                                    <x-input-label for="email_perusahaan" value="Email" />
                                    <x-text-input id="email_perusahaan" type="email" name="email_perusahaan" value="{{ $pengaturan['email_perusahaan'] ?? '' }}" class="mt-1 w-full" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="kota_perusahaan" value="Kota" />
                                <x-text-input id="kota_perusahaan" type="text" name="kota_perusahaan" value="{{ $pengaturan['kota_perusahaan'] ?? '' }}" class="mt-1 w-full" />
                            </div>

                            <div class="flex justify-end pt-2">
                                <x-loading-button size="lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Simpan Pengaturan
                                </x-loading-button>
                            </div>
                        </div>
                    </form>
                </x-card>
            </div>
        </div>

        {{-- Tab: Satuan --}}
        <div x-show="tab === 'satuan'" x-cloak
             x-data="{
                editId: null,
                editNama: '',
                openEdit(id, nama) { this.editId = id; this.editNama = nama; },
                closeEdit() { this.editId = null; this.editNama = ''; }
             }">
            <div class="max-w-2xl space-y-4">
                {{-- Form tambah satuan --}}
                <x-card>
                    <div class="p-5">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Tambah Satuan Baru</h3>
                        <form method="POST" action="{{ route('pengaturan.satuan.store') }}" class="flex gap-3">
                            @csrf
                            <x-text-input name="nama" type="text" class="flex-1" placeholder="cth. pcs / kg / meter / lusin" maxlength="50" required />
                            <x-loading-button>Tambah</x-loading-button>
                        </form>
                        @if($errors->satuan->has('nama'))
                            <p class="mt-2 text-sm text-red-600">{{ $errors->satuan->first('nama') }}</p>
                        @endif
                    </div>
                </x-card>

                {{-- Daftar satuan --}}
                <x-card>
                    <div class="divide-y divide-gray-100">
                        @forelse($satuanList as $satuan)
                            <div class="flex items-center gap-3 px-5 py-3">
                                {{-- Mode tampil --}}
                                <div x-show="editId !== {{ $satuan->id }}" class="flex items-center justify-between w-full gap-3">
                                    <span class="text-sm text-gray-800">{{ $satuan->nama }}</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                            @click="openEdit({{ $satuan->id }}, '{{ addslashes($satuan->nama) }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('pengaturan.satuan.destroy', $satuan) }}"
                                            onsubmit="return confirm('Hapus satuan \'{{ $satuan->nama }}\'?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Mode edit --}}
                                <div x-show="editId === {{ $satuan->id }}" class="flex items-center gap-2 w-full">
                                    <form method="POST" action="{{ route('pengaturan.satuan.update', $satuan) }}" class="flex items-center gap-2 flex-1">
                                        @csrf @method('PUT')
                                        <x-text-input name="nama" type="text" x-model="editNama" class="flex-1 h-9 text-sm" maxlength="50" required />
                                        <x-button type="submit" size="sm">Simpan</x-button>
                                        <x-button type="button" variant="secondary" size="sm" @click="closeEdit">Batal</x-button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-8 text-center text-sm text-gray-400">Belum ada satuan.</div>
                        @endforelse
                    </div>
                </x-card>
            </div>
        </div>

        {{-- Tab: Kategori --}}
        <div x-show="tab === 'kategori'" x-cloak
             x-data="{
                editId: null,
                editNama: '',
                openEdit(id, nama) { this.editId = id; this.editNama = nama; },
                closeEdit() { this.editId = null; this.editNama = ''; }
             }">
            <div class="max-w-2xl space-y-4">
                {{-- Form tambah kategori --}}
                <x-card>
                    <div class="p-5">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Tambah Kategori Baru</h3>
                        <form method="POST" action="{{ route('pengaturan.kategori.store') }}" class="flex gap-3">
                            @csrf
                            <x-text-input name="nama" type="text" class="flex-1" placeholder="cth. Elektronik / Pakaian / Makanan" maxlength="100" required />
                            <x-loading-button>Tambah</x-loading-button>
                        </form>
                        @if($errors->kategori->has('nama'))
                            <p class="mt-2 text-sm text-red-600">{{ $errors->kategori->first('nama') }}</p>
                        @endif
                    </div>
                </x-card>

                {{-- Daftar kategori --}}
                <x-card>
                    <div class="divide-y divide-gray-100">
                        @forelse($kategoriList as $kategori)
                            <div class="flex items-center gap-3 px-5 py-3">
                                {{-- Mode tampil --}}
                                <div x-show="editId !== {{ $kategori->id }}" class="flex items-center justify-between w-full gap-3">
                                    <span class="text-sm text-gray-800">{{ $kategori->nama }}</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                            @click="openEdit({{ $kategori->id }}, '{{ addslashes($kategori->nama) }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('pengaturan.kategori.destroy', $kategori) }}"
                                            onsubmit="return confirm('Hapus kategori \'{{ $kategori->nama }}\'?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Mode edit --}}
                                <div x-show="editId === {{ $kategori->id }}" class="flex items-center gap-2 w-full">
                                    <form method="POST" action="{{ route('pengaturan.kategori.update', $kategori) }}" class="flex items-center gap-2 flex-1">
                                        @csrf @method('PUT')
                                        <x-text-input name="nama" type="text" x-model="editNama" class="flex-1 h-9 text-sm" maxlength="100" required />
                                        <x-button type="submit" size="sm">Simpan</x-button>
                                        <x-button type="button" variant="secondary" size="sm" @click="closeEdit">Batal</x-button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-8 text-center text-sm text-gray-400">Belum ada kategori.</div>
                        @endforelse
                    </div>
                </x-card>
            </div>
        </div>

    </div>
</x-app-layout>
