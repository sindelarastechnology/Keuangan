@props(['options' => [], 'aktif' => [], 'action' => '', 'wajib' => ['aksi']])

<div class="flex justify-end px-4 pt-3">
    <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'konfig-kolom' }))"
            class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
        Atur Kolom
    </button>
</div>

<x-modal name="konfig-kolom" :show="false" maxWidth="md">
    <form method="POST" action="{{ $action }}" class="p-6">
        @csrf
        <h3 class="text-lg font-semibold text-gray-800">Atur Kolom Tabel</h3>
        <p class="text-sm text-gray-500 mt-0.5">Pilih kolom yang ingin ditampilkan pada tabel ini.</p>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($options as $key => $label)
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="kolom[]" value="{{ $key }}"
                           class="rounded text-emerald-600"
                           {{ in_array($key, $aktif, true) ? 'checked' : '' }}
                           {{ in_array($key, $wajib, true) ? 'disabled' : '' }}>
                    {{ $label }}
                    @if(in_array($key, $wajib, true))
                        <span class="text-xs text-gray-400">(selalu tampil)</span>
                    @endif
                </label>
            @endforeach
        </div>

        <div class="flex justify-end gap-2 pt-4">
            <x-secondary-button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'konfig-kolom' }))">Batal</x-secondary-button>
            <x-primary-button type="submit">Simpan Pengaturan</x-primary-button>
        </div>
    </form>
</x-modal>