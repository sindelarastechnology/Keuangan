<x-app-layout>
    <x-slot name="title">Paket Langganan</x-slot>

    @php
        $isPro = \App\Services\PlanService::isPro();
    @endphp

    <x-page-header title="Paket Langganan" subtitle="Pilih paket yang sesuai dengan kebutuhan bisnis Anda"></x-page-header>

    <div class="max-w-5xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Paket Gratis --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">Gratis</h3>
                        @if($paket === 'free')
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">Paket aktif</span>
                        @endif
                    </div>
                    <p class="mt-1 text-2xl font-extrabold text-gray-900">Rp0 <span class="text-sm font-normal text-gray-400">/ bulan</span></p>
                </div>
                <div class="p-6">
                    <ul class="space-y-2.5 text-sm text-gray-600">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Dashboard & laporan Laba Rugi / Neraca (web)
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Data nama, rekening & hingga 50 produk
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Kas masuk/keluar, transfer rekening, penjualan, pembelian & tagihan
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Chat komunitas & notifikasi
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Pengaturan dasar
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Paket Pro --}}
            <div class="bg-white rounded-xl border-2 border-emerald-600 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-emerald-50/50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">Pro</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-600 text-white text-xs font-semibold">Paling lengkap</span>
                    </div>
                    <p class="mt-1 text-2xl font-extrabold text-gray-900">Hubungi admin</p>
                    @if($isPro)
                        <p class="mt-2 inline-flex items-center px-3 py-1 rounded-full bg-emerald-600 text-white text-xs font-semibold">Paket aktif</p>
                    @endif
                </div>
                <div class="p-6">
                    <ul class="space-y-2.5 text-sm text-gray-600">
                        @foreach($fitur as $key => $meta)
                            <li class="flex items-start gap-2">
                                @if(in_array($key, $terkunci, true))
                                    <svg class="w-4 h-4 mt-0.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <span>{{ $meta['label'] }} <span class="text-xs text-amber-600 font-medium">(hanya Pro)</span></span>
                                @else
                                    <svg class="w-4 h-4 mt-0.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>{{ $meta['label'] }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    @if(! $isPro)
                        <div class="mt-6 p-4 rounded-lg bg-gray-50 border border-gray-100">
                            <p class="text-xs text-gray-500 mb-2">Mau langsung diproses tim KasPro? Kirim permintaan upgrade.</p>
                            <form method="POST" action="{{ route('upgrade.request') }}" class="space-y-3">
                                @csrf
                                <textarea name="catatan" rows="2" maxlength="500" placeholder="Catatan singkat (opsional)" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                                <x-button type="submit" class="w-full justify-center">
                                    Ajukan Upgrade Pro
                                </x-button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(! $isPro && $sisaBarang !== null)
            <div class="mt-6 flex items-center gap-3 bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <p class="text-sm text-gray-600">Kuota produk paket Gratis tersisa <strong>{{ $sisaBarang }} dari 50</strong>.</p>
            </div>
        @endif
    </div>
</x-app-layout>