<x-app-layout>
    <x-slot name="title">{{ isset($supplier) ? 'Edit' : 'Tambah' }} Supplier</x-slot>

    <x-page-header title="{{ isset($supplier) ? 'Edit Supplier' : 'Tambah Supplier' }}">
        <a href="{{ route('supplier.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <div class="max-w-3xl">
        <x-card>
            <form method="POST" action="{{ isset($supplier) ? route('supplier.update', $supplier) : route('supplier.store') }}" class="p-6 space-y-5">
                @csrf
                @if(isset($supplier)) @method('PUT') @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="nama" value="Nama Supplier" />
                        <x-text-input id="nama" type="text" name="nama" value="{{ old('nama', $supplier->nama ?? '') }}" class="mt-1" placeholder="cth. PT Maju Jaya" required />
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="telepon" value="Telepon" />
                        <x-text-input id="telepon" type="text" name="telepon" value="{{ old('telepon', $supplier->telepon ?? '') }}" class="mt-1" placeholder="cth. 08123456789" />
                        <x-input-error :messages="$errors->get('telepon')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" type="email" name="email" value="{{ old('email', $supplier->email ?? '') }}" class="mt-1" placeholder="cth. info@majujaya.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="npwp" value="NPWP" />
                        <x-text-input id="npwp" type="text" name="npwp" value="{{ old('npwp', $supplier->npwp ?? '') }}" class="mt-1" placeholder="cth. 00.123.456.7-091.000" />
                        <x-input-error :messages="$errors->get('npwp')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="alamat" value="Alamat" />
                    <x-textarea id="alamat" name="alamat" class="mt-1" rows="3">{{ old('alamat', $supplier->alamat ?? '') }}</x-textarea>
                    <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="keterangan" value="Keterangan" />
                    <x-textarea id="keterangan" name="keterangan" class="mt-1" rows="3">{{ old('keterangan', $supplier->keterangan ?? '') }}</x-textarea>
                    <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="hidden" name="is_aktif" value="0">
                            <input type="checkbox" name="is_aktif" value="1" class="rounded text-emerald-600" {{ old('is_aktif', $supplier->is_aktif ?? true) ? 'checked' : '' }}>
                            Aktif
                        </label>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <x-loading-button>
                        {{ isset($supplier) ? 'Simpan Perubahan' : 'Simpan Supplier' }}
                    </x-loading-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
