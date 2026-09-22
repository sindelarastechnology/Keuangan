<x-app-layout>
    <x-slot name="title">{{ $akun ? 'Edit' : 'Tambah' }} Akun Perkiraan</x-slot>

    <x-page-header title="{{ $akun ? 'Edit Akun Perkiraan' : 'Tambah Akun Perkiraan' }}">
        <a href="{{ route('akun-perkiraan.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <div class="max-w-3xl">
        <x-card>
            <form method="POST" action="{{ $akun ? route('akun-perkiraan.update', $akun) : route('akun-perkiraan.store') }}" class="p-6 space-y-5">
                @csrf
                @if($akun) @method('PUT') @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="kode" value="Kode Akun" />
                        <x-text-input id="kode" type="text" name="kode" value="{{ old('kode', $akun->kode ?? '') }}" class="mt-1" placeholder="cth. 114" required />
                        <x-input-error :messages="$errors->get('kode')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="nama" value="Nama Akun" />
                        <x-text-input id="nama" type="text" name="nama" value="{{ old('nama', $akun->nama ?? '') }}" class="mt-1" placeholder="cth. Persediaan Bahan Baku" required />
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="jenis" value="Jenis Akun" />
                        <x-select id="jenis" name="jenis" class="mt-1" required>
                            @foreach(\App\Models\AkunPerkiraan::listJenis() as $key => $label)
                                <option value="{{ $key }}" {{ old('jenis', $akun->jenis ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error :messages="$errors->get('jenis')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="saldo_normal" value="Saldo Normal" />
                        <x-select id="saldo_normal" name="saldo_normal" class="mt-1" required>
                            <option value="debit" {{ old('saldo_normal', $akun->saldo_normal ?? '') == 'debit' ? 'selected' : '' }}>Debit</option>
                            <option value="kredit" {{ old('saldo_normal', $akun->saldo_normal ?? '') == 'kredit' ? 'selected' : '' }}>Kredit</option>
                        </x-select>
                        <x-input-error :messages="$errors->get('saldo_normal')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="parent_id" value="Akun Induk (Opsional)" />
                        <x-select id="parent_id" name="parent_id" class="mt-1">
                            <option value="">— Tidak ada —</option>
                            @foreach($parents as $p)
                                <option value="{{ $p->id }}" {{ old('parent_id', $akun->parent_id ?? '') == $p->id ? 'selected' : '' }}>
                                    {{ $p->kode }} - {{ $p->nama }}
                                </option>
                            @endforeach
                        </x-select>
                        <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />
                    </div>
                    <div class="flex items-end gap-6 pb-1">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="is_header" value="1" class="rounded text-emerald-600" {{ old('is_header', $akun->is_header ?? false) ? 'checked' : '' }}>
                            Akun Kelompok (Header)
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="hidden" name="is_aktif" value="0">
                            <input type="checkbox" name="is_aktif" value="1" class="rounded text-emerald-600" {{ old('is_aktif', $akun->is_aktif ?? true) ? 'checked' : '' }}>
                            Aktif
                        </label>
                    </div>
                </div>

                <div>
                    <x-input-label for="keterangan" value="Keterangan" />
                    <x-textarea id="keterangan" name="keterangan" class="mt-1" rows="3">{{ old('keterangan', $akun->keterangan ?? '') }}</x-textarea>
                </div>

                <div class="flex justify-end pt-2">
                    <x-loading-button>
                        {{ $akun ? 'Simpan Perubahan' : 'Simpan Akun' }}
                    </x-loading-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
