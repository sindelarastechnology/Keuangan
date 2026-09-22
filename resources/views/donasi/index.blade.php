<x-app-layout>
    <x-slot name="title">Dukung KasPro</x-slot>

    <x-page-header title="Dukung KasPro" subtitle="Dukung pengembangan KasPro secara sukarela"></x-page-header>

    <div x-data="{ qris: false }" class="max-w-4xl grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card title="Dukung KasPro via QRIS">
            <div class="p-5 space-y-5">
                <button type="button" @click="qris = true"
                        class="block mx-auto w-56 cursor-zoom-in" title="Klik untuk memperbesar">
                    <img src="{{ asset('qris.png') }}" alt="QRIS KasPro"
                         class="w-full aspect-[705/992] object-cover rounded-xl border border-gray-200 bg-white shadow-sm transition hover:shadow-lg" loading="lazy">
                </button>

                <p class="text-sm text-gray-600 leading-relaxed">
                    Dukung <strong>seikhlasnya</strong> untuk mengembangkan KasPro.
                    Setelah transfer, silakan unggah bukti pembayaran dan keterangan (opsional)
                    agar pihak pengelola dapat memverifikasi.
                </p>

                <form method="POST" action="{{ route('donasi.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="nominal" value="Nominal (Rp)" />
                        <input id="nominal" type="number" name="nominal" min="0" step="0.01"
                               value="{{ old('nominal') }}" inputmode="decimal"
                               class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                               placeholder="Kosongkan bila tidak mengisi nominal" />
                        <x-input-error :messages="$errors->get('nominal')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="bukti" value="Bukti Pembayaran (opsional)" />
                        <input id="bukti" type="file" name="bukti" accept="image/jpeg,image/png,image/webp"
                               class="mt-1 block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-emerald-700 hover:file:bg-emerald-100" />
                        <x-input-error :messages="$errors->get('bukti')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="keterangan" value="Keterangan (opsional)" />
                        <textarea id="keterangan" name="keterangan" rows="3" maxlength="500"
                                  class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                  placeholder="Catatan untuk donasi Anda (opsional)">{{ old('keterangan') }}</textarea>
                        <x-input-error :messages="$errors->get('keterangan')" class="mt-1" />
                    </div>

                    <x-button type="submit" class="w-full justify-center">
                        Kirim Dukungan
                    </x-button>
                </form>
            </div>
        </x-card>

        <x-card title="Riwayat Dukungan Saya">
            <div class="divide-y divide-gray-100">
                @forelse($riwayat as $donasi)
                    <div class="flex items-start gap-3 px-5 py-3.5">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $donasi->nominal !== null ? formatRupiah((float) $donasi->nominal) : 'Seikhlasnya' }}
                            </p>
                            @if($donasi->keterangan !== null)
                                <p class="text-sm text-gray-600 mt-0.5 break-words">{{ $donasi->keterangan }}</p>
                            @endif
                            @if($donasi->bukti_path !== null)
                                <a href="{{ asset('storage/'.$donasi->bukti_path) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-xs text-emerald-700 hover:underline mt-0.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Bukti
                                </a>
                            @endif
                            <p class="text-xs text-gray-400 mt-1">{{ $donasi->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $donasi->isConfirmed() ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $donasi->isConfirmed() ? 'Dikonfirmasi' : 'Menunggu verifikasi' }}
                        </span>
                    </div>
                @empty
                    <div class="px-5 py-12 text-center">
                        <p class="text-sm text-gray-400">Belum ada dukungan dari Anda.</p>
                    </div>
                @endforelse
            </div>
        </x-card>
    </div>

    <div x-show="qris" x-cloak @click="qris = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
         @keydown.escape.window="qris = false">
        <div @click.stop class="relative max-w-[90vw] max-h-[90vh]">
            <img src="{{ asset('qris.png') }}" alt="QRIS KasPro"
                 class="max-w-[90vw] max-h-[90vh] w-auto h-auto object-contain rounded-xl shadow-2xl">
            <button type="button" @click="qris = false"
                    class="absolute -top-3 -right-3 w-9 h-9 rounded-full bg-white shadow-lg text-gray-600 hover:text-gray-900 text-xl leading-none flex items-center justify-center"
                    title="Tutup">&times;</button>
        </div>
    </div>
</x-app-layout>