<x-app-layout>
    <x-slot name="title">{{ isset($customer) ? 'Edit' : 'Tambah' }} Customer</x-slot>

    <x-page-header title="{{ isset($customer) ? 'Edit Customer' : 'Tambah Customer' }}">
        <a href="{{ route('customer.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <div class="max-w-3xl">
        <x-card>
            <form method="POST" action="{{ isset($customer) ? route('customer.update', $customer) : route('customer.store') }}" class="p-6 space-y-5">
                @csrf
                @if(isset($customer)) @method('PUT') @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="nama" value="Nama Customer" />
                        <x-text-input id="nama" type="text" name="nama" value="{{ old('nama', $customer->nama ?? '') }}" class="mt-1" placeholder="cth. PT Sejahtera" required />
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="telepon" value="Telepon" />
                        <x-text-input id="telepon" type="text" name="telepon" value="{{ old('telepon', $customer->telepon ?? '') }}" class="mt-1" placeholder="cth. 08123456789" />
                        <x-input-error :messages="$errors->get('telepon')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" type="email" name="email" value="{{ old('email', $customer->email ?? '') }}" class="mt-1" placeholder="cth. info@sejahtera.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="npwp" value="NPWP" />
                        <x-text-input id="npwp" type="text" name="npwp" value="{{ old('npwp', $customer->npwp ?? '') }}" class="mt-1" placeholder="cth. 00.123.456.7-091.000" />
                        <x-input-error :messages="$errors->get('npwp')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="alamat" value="Alamat" />
                    <x-textarea id="alamat" name="alamat" class="mt-1" rows="3">{{ old('alamat', $customer->alamat ?? '') }}</x-textarea>
                    <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="keterangan" value="Keterangan" />
                    <x-textarea id="keterangan" name="keterangan" class="mt-1" rows="3">{{ old('keterangan', $customer->keterangan ?? '') }}</x-textarea>
                    <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="hidden" name="is_aktif" value="0">
                            <input type="checkbox" name="is_aktif" value="1" class="rounded text-emerald-600" {{ old('is_aktif', $customer->is_aktif ?? true) ? 'checked' : '' }}>
                            Aktif
                        </label>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <x-loading-button>
                        {{ isset($customer) ? 'Simpan Perubahan' : 'Simpan Customer' }}
                    </x-loading-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
