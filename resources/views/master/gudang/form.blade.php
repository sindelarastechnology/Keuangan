<x-app-layout>
    <x-slot name="title">{{ isset($gudang) ? 'Edit' : 'Tambah' }} Gudang</x-slot>

    <x-page-header title="{{ isset($gudang) ? 'Edit Gudang' : 'Tambah Gudang' }}">
        <a href="{{ route('gudang.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <div class="max-w-3xl">
        <x-card>
            <form method="POST" action="{{ isset($gudang) ? route('gudang.update', $gudang) : route('gudang.store') }}" class="p-6 space-y-5">
                @csrf
                @if(isset($gudang)) @method('PUT') @endif

                @if(isset($gudang))
                    <div>
                        <x-input-label for="kode" value="Kode Gudang" />
                        <x-text-input id="kode" type="text" value="{{ $gudang->kode }}" class="mt-1 bg-gray-50 text-gray-500" disabled />
                        <p class="mt-1 text-xs text-gray-500">Kode tidak dapat diubah.</p>
                    </div>
                @endif

                <div>
                    <x-input-label for="nama" value="Nama Gudang" />
                    <x-text-input id="nama" type="text" name="nama" value="{{ old('nama', $gudang->nama ?? '') }}" class="mt-1" placeholder="cth. Gudang Jakarta" required />
                    <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="alamat" value="Alamat" />
                    <x-textarea id="alamat" name="alamat" class="mt-1" rows="2" placeholder="cth. Jl. Merdeka No. 1, Jakarta">{{ old('alamat', $gudang->alamat ?? '') }}</x-textarea>
                    <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="keterangan" value="Keterangan" />
                    <x-textarea id="keterangan" name="keterangan" class="mt-1" rows="3">{{ old('keterangan', $gudang->keterangan ?? '') }}</x-textarea>
                    <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="hidden" name="is_aktif" value="0">
                        <input type="checkbox" name="is_aktif" value="1" class="rounded text-emerald-600" {{ old('is_aktif', $gudang->is_aktif ?? true) ? 'checked' : '' }}>
                        Aktif
                    </label>

                    <x-loading-button>
                        {{ isset($gudang) ? 'Simpan Perubahan' : 'Simpan Gudang' }}
                    </x-loading-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>