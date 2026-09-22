<x-app-layout>
    <x-slot name="title">Notifikasi</x-slot>

    @php
        $label = static function (string $type): string {
            return match ($type) {
                'chat.mention' => 'Sebutan di chat',
                'chat.dm' => 'Pesan baru',
                'chat.moderated' => 'Moderasi chat',
                'admin.message' => 'Pengumuman',
                'account.status' => 'Status akun',
                'plan.change' => 'Perubahan paket',
                'upgrade.request' => 'Permintaan upgrade',
                'donation.status' => 'Donasi',
                default => 'Notifikasi',
            };
        };
    @endphp

    <x-page-header title="Notifikasi" subtitle="Pusat pemberitahuan dalam aplikasi"></x-page-header>

    <div class="max-w-3xl">
        <x-card>
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100">
                <p class="text-sm text-gray-500">
                    {{ $notifikasi->total() }} notifikasi
                    @if($notifikasi->total() > 0)
                        · {{ $notifikasi->whereNull('read_at')->count() }} belum dibaca
                    @endif
                </p>
                @if($notifikasi->whereNull('read_at')->count() > 0)
                    <form method="POST" action="{{ route('notifikasi.baca-semua') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 hover:text-emerald-800">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Tandai semua dibaca
                        </button>
                    </form>
                @endif
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($notifikasi as $notif)
                    @php
                        $data = $notif->data;
                        $judul = $data['title'] ?? $label($data['type'] ?? $notif->type);
                        $isi = $data['body'] ?? '';
                    @endphp
                    <div class="flex items-start gap-3 px-5 py-3.5 {{ $notif->read_at ? '' : 'bg-emerald-50/40' }}">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                @if(! $notif->read_at)
                                    <span class="w-2 h-2 rounded-full bg-emerald-600 shrink-0"></span>
                                @endif
                                <p class="text-sm font-semibold text-gray-800 {{ $notif->read_at ? 'text-gray-600' : '' }}">{{ $judul }}</p>
                            </div>
                            @if($isi !== '')
                                <p class="text-sm text-gray-600 mt-0.5 break-words">{{ $isi }}</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>
                        @if(! $notif->read_at)
                            <form method="POST" action="{{ route('notifikasi.baca', $notif) }}">
                                @csrf
                                <button type="submit" class="shrink-0 text-xs text-emerald-700 hover:underline mt-1">Tandai dibaca</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="px-5 py-12 text-center">
                        <p class="text-sm text-gray-400">Belum ada notifikasi.</p>
                    </div>
                @endforelse
            </div>
        </x-card>

        <div class="mt-4">
            {{ $notifikasi->links() }}
        </div>
    </div>
</x-app-layout>