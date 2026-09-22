<x-app-layout>
    <x-slot name="title">{{ isset($rekening) ? 'Edit' : 'Tambah' }} Rekening</x-slot>

    <x-page-header title="{{ isset($rekening) ? 'Edit Rekening' : 'Tambah Rekening' }}">
        <a href="{{ route('rekening.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <div class="max-w-3xl">
        <x-card>
            <form method="POST" action="{{ isset($rekening) ? route('rekening.update', $rekening) : route('rekening.store') }}" class="p-6 space-y-5" x-data="rekeningForm()">
                @csrf
                @if(isset($rekening)) @method('PUT') @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="jenis" value="Jenis" />
                        <x-select id="jenis" name="jenis" class="mt-1" x-model="jenis" required>
                            <option value="kas" @selected(old('jenis', $rekening->jenis ?? '') === 'kas')>Kas</option>
                            <option value="bank" @selected(old('jenis', $rekening->jenis ?? '') === 'bank')>Bank</option>
                        </x-select>
                        <x-input-error :messages="$errors->get('jenis')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="nama" value="Nama" />
                        <x-text-input id="nama" type="text" name="nama" value="{{ old('nama', $rekening->nama ?? '') }}" class="mt-1" placeholder="cth. Kas Kecil / Bank BCA" required />
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5" x-show="jenis === 'bank'">
                    <div>
                        <x-input-label for="nomor_rekening" value="Nomor Rekening" />
                        <x-text-input id="nomor_rekening" type="text" name="nomor_rekening" value="{{ old('nomor_rekening', $rekening->nomor_rekening ?? '') }}" class="mt-1" placeholder="cth. 1234567890" />
                        <x-input-error :messages="$errors->get('nomor_rekening')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="nama_pemilik" value="Nama Pemilik" />
                        <x-text-input id="nama_pemilik" type="text" name="nama_pemilik" value="{{ old('nama_pemilik', $rekening->nama_pemilik ?? '') }}" class="mt-1" placeholder="Atas nama" />
                        <x-input-error :messages="$errors->get('nama_pemilik')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="saldo_awal" value="Saldo Awal" />
                        <x-text-input id="saldo_awal" type="number" step="0.01" name="saldo_awal" value="{{ old('saldo_awal', $rekening->saldo_awal ?? 0) }}" class="mt-1" required />
                        <x-input-error :messages="$errors->get('saldo_awal')" class="mt-2" />
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="hidden" name="is_aktif" value="0">
                            <input type="checkbox" name="is_aktif" value="1" class="rounded text-emerald-600" {{ old('is_aktif', $rekening->is_aktif ?? true) ? 'checked' : '' }}>
                            Aktif
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="akun_id" value="Akun Perkiraan" />
                        @if(isset($akunTerkunci) && $akunTerkunci && isset($rekening))
                            <input type="hidden" name="akun_id" value="{{ $rekening->akun_id }}">
                            <div class="mt-1 px-3 py-2 rounded-md border border-gray-200 bg-gray-50 text-sm text-gray-600">
                                {{ $rekening->akun?->kode }} — {{ $rekening->akun?->nama }}
                                <span class="text-xs text-gray-400">(terkunci: sudah dipakai transaksi)</span>
                            </div>
                        @else
                            <x-select id="akun_id" name="akun_id" class="mt-1">
                                @unless(isset($rekening))
                                    <option value="">— Buat otomatis —</option>
                                @endunless
                                @foreach($akunList as $id => $nama)
                                    <option value="{{ $id }}" {{ old('akun_id', $rekening->akun_id ?? '') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </x-select>
                            @unless(isset($rekening))
                                <p class="mt-1 text-xs text-gray-500">Kosongkan untuk memakai akun kas/bank baru ({{ empty($akunList) ? 'dibuat otomatis' : 'opsional' }}).</p>
                            @endunless
                        @endif
                        <x-input-error :messages="$errors->get('akun_id')" class="mt-2" />
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <x-loading-button>
                        {{ isset($rekening) ? 'Simpan Perubahan' : 'Simpan Rekening' }}
                    </x-loading-button>
                </div>
            </form>
        </x-card>
    </div>

    <script>
        function rekeningForm() {
            return { jenis: @json(old('jenis', $rekening->jenis ?? 'kas')) };
        }
    </script>
</x-app-layout>
