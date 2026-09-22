@props([
    'name' => 'confirm-dialog',
    'title' => 'Konfirmasi',
    'message' => null,
    'confirmLabel' => 'Ya, Lanjutkan',
    'cancelLabel' => 'Batal',
    'confirmVariant' => 'danger',
])

<x-modal :name="$name">
    <div class="p-6">
        <div class="flex items-start gap-3">
            <div class="shrink-0 w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
                @if ($message)
                    <p class="mt-1 text-sm text-gray-600">{{ $message }}</p>
                @endif
                {{ $slot }}
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <x-button variant="secondary" type="button" x-on:click="$dispatch('close')">
                {{ $cancelLabel }}
            </x-button>
            <x-button :variant="$confirmVariant" type="submit">
                {{ $confirmLabel }}
            </x-button>
        </div>
    </div>
</x-modal>