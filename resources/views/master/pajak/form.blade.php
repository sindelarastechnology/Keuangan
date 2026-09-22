<x-app-layout>
    <x-slot name="title">{{ isset($pajak) ? 'Edit' : 'Tambah' }} Pajak</x-slot>

    <x-page-header title="{{ isset($pajak) ? 'Edit Pajak' : 'Tambah Pajak' }}">
        <a href="{{ route('pajak.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <div class="max-w-3xl">
        <x-card>
            <form method="POST" action="{{ isset($pajak) ? route('pajak.update', $pajak) : route('pajak.store') }}" class="p-6 space-y-5">
                @csrf
                @if(isset($pajak)) @method('PUT') @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="nama" value="Nama" />
                        <x-text-input id="nama" type="text" name="nama" value="{{ old('nama', $pajak->nama ?? '') }}" class="mt-1" placeholder="cth. PPN" required />
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="rate" value="Rate" />
                        <x-text-input id="rate" type="number" step="0.01" name="rate" value="{{ old('rate', $pajak->rate ?? '') }}" class="mt-1" placeholder="cth. 11" required />
                        <p class="mt-1 text-xs text-gray-500">Persentase pajak, mis. 11</p>
                        <x-input-error :messages="$errors->get('rate')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="hidden" name="is_aktif" value="0">
                            <input type="checkbox" name="is_aktif" value="1" class="rounded text-emerald-600" {{ old('is_aktif', $pajak->is_aktif ?? true) ? 'checked' : '' }}>
                            Aktif
                        </label>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <x-loading-button>
                        {{ isset($pajak) ? 'Simpan Perubahan' : 'Simpan Pajak' }}
                    </x-loading-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
