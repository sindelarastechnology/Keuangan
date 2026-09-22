<section class="space-y-6">
    <header>
        <h2 class="text-lg font-semibold text-gray-900">Hapus Akun</h2>

        <p class="mt-1 text-sm text-gray-600">
            Setelah akun dihapus, seluruh data dan sumber dayanya akan terhapus permanen. Sebelum menghapus akun, unduh data atau informasi yang ingin Anda simpan.
        </p>
    </header>

    <x-button
        variant="danger"
        type="button"
        x-data=""
        x-on:click="$dispatch('open-modal', 'confirm-user-deletion')"
    >Hapus Akun</x-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-gray-900">
                Yakin ingin menghapus akun Anda?
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Setelah akun dihapus, seluruh data dan sumber dayanya akan terhapus permanen. Masukkan kata sandi untuk mengonfirmasi penghapusan permanen akun Anda.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Kata Sandi" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    placeholder="Kata Sandi"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-button variant="secondary" type="button" x-on:click="$dispatch('close')">
                    Batal
                </x-button>

                <x-button variant="danger" type="submit">
                    Hapus Akun
                </x-button>
            </div>
        </form>
    </x-modal>
</section>