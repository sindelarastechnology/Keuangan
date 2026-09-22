<x-app-layout>
    <x-slot name="title">Detail Jurnal</x-slot>

    <x-page-header title="Detail Jurnal" :subtitle="$jurnal->nomor">
        <a href="{{ route('jurnal.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <div class="max-w-3xl">
        <x-card>
            <div class="p-5">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Nomor</dt><dd class="font-medium">{{ $jurnal->nomor }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Tanggal</dt><dd class="font-medium">{{ formatTanggal($jurnal->tanggal) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Tipe</dt><dd class="font-medium">{{ $jurnal->tipe_label }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Status</dt><dd class="font-medium">{{ $jurnal->is_posted ? 'Posted' : 'Void / Nonaktif' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Persetujuan</dt><dd class="font-medium">@if($jurnal->approval_status === 'approved')<span class="inline-flex px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700">Disetujui</span>@elseif($jurnal->approval_status === 'rejected')<span class="inline-flex px-2 py-1 text-xs rounded-full bg-rose-100 text-rose-700">Ditolak</span>@elseif($jurnal->approval_status === 'pending_review')<span class="inline-flex px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700">Menunggu</span>@else<span class="inline-flex px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-500">Draf</span>@endif</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Keterangan</dt><dd class="font-medium text-right">{{ $jurnal->keterangan ?? '-' }}</dd></div>
                    @if($jurnal->approval_reason)
                        <div class="flex justify-between"><dt class="text-gray-500">Alasan</dt><dd class="font-medium text-right text-gray-600">{{ $jurnal->approval_reason }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-gray-500">Dibuat oleh</dt><dd class="font-medium text-gray-600">{{ $jurnal->creator?->name ?? '-' }}@if($jurnal->approver)<span class="text-gray-400"> · disetujui {{ $jurnal->approver->name }}</span>@endif</dd></div>
                </dl>
            </div>
        </x-card>

        @if($jurnal->approval_status === 'pending_review')
            <div class="mt-4 flex flex-wrap gap-2">
                <form method="POST" action="{{ route('jurnal.approve', $jurnal) }}">
                    @csrf
                    <button class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-lg">Setujui</button>
                </form>
                <form method="POST" action="{{ route('jurnal.reject', $jurnal) }}" class="flex flex-wrap items-center gap-2">
                    @csrf
                    <input type="text" name="reason" placeholder="Alasan penolakan" required class="border-gray-300 rounded-lg text-sm focus:ring-rose-500 focus:border-rose-500 w-full sm:w-64">
                    <button class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm rounded-lg">Tolak</button>
                </form>
            </div>
        @elseif(in_array($jurnal->approval_status, ['approved', 'rejected']))
            <div class="mt-4">
                <form method="POST" action="{{ route('jurnal.request-approval', $jurnal) }}">
                    @csrf
                    <button class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm rounded-lg">Minta Persetujuan Lagi</button>
                </form>
            </div>
        @endif

        <x-card class="mt-4">
            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Akun</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Debit</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jurnal->items as $item)
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800">{{ $item->akun->nama }}</div>
                                <div class="text-xs text-gray-400">{{ $item->akun->kode }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $item->keterangan ?? '-' }}</td>
                            <td class="px-4 py-3 text-right text-gray-700">{{ $item->debit > 0 ? formatRupiah($item->debit) : '-' }}</td>
                            <td class="px-4 py-3 text-right text-gray-700">{{ $item->kredit > 0 ? formatRupiah($item->kredit) : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100">
                    <tr>
                        <td colspan="2" class="px-4 py-3 font-semibold">Total</td>
                        <td class="px-4 py-3 text-right font-bold">{{ formatRupiah($jurnal->total_debit) }}</td>
                        <td class="px-4 py-3 text-right font-bold">{{ formatRupiah($jurnal->total_kredit) }}</td>
                    </tr>
                </tfoot>
            </table>
            </div>
        </x-card>
    </div>
</x-app-layout>
