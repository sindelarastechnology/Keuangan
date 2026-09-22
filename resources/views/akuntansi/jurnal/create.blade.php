<x-app-layout>
    <x-slot name="title">Jurnal Manual Baru</x-slot>

    <x-page-header title="Jurnal Manual Baru" subtitle="Buat jurnal double-entry secara manual">
        <a href="{{ route('jurnal.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <form method="POST" action="{{ route('jurnal.store') }}" x-data="jurnalForm()">
        @csrf

        <x-card>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <x-input-label for="tanggal" value="Tanggal" />
                    <x-text-input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" class="mt-1" required />
                </div>
                <div>
                    <x-input-label for="keterangan" value="Keterangan" />
                    <x-text-input id="keterangan" type="text" name="keterangan" value="{{ old('keterangan') }}" class="mt-1" placeholder="Deskripsi jurnal" />
                </div>
            </div>
            <x-input-error :messages="$errors->get('keterangan')" class="px-5" />
        </x-card>

        <x-card class="mt-4">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Baris Jurnal</h3>
                <button type="button" @click="tambahItem()" class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Tambah Baris
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Akun</th>
                            <th class="px-2 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Debit</th>
                            <th class="px-2 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Kredit</th>
                            <th class="px-2 py-3 w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="px-2 py-2 min-w-[200px]">
                                    <select x-model="item.akun_id" :name="'items['+index+'][akun_id]'" class="border-gray-300 rounded-lg text-sm w-full" required>
                                        <option value="">Pilih Akun...</option>
                                        @foreach($akunList as $a)
                                            <option value="{{ $a->id }}">{{ $a->kode }} - {{ $a->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="number" step="0.01" :name="'items['+index+'][debit]'" x-model.number="item.debit" @input="update" placeholder="0" class="border-gray-300 rounded-lg text-sm w-28 text-right">
                                </td>
                                <td class="px-2 py-2">
                                    <input type="number" step="0.01" :name="'items['+index+'][kredit]'" x-model.number="item.kredit" @input="update" placeholder="0" class="border-gray-300 rounded-lg text-sm w-28 text-right">
                                </td>
                                <td class="px-2 py-2">
                                    <button type="button" @click="hapusItem(index)" class="p-1 text-red-500 hover:bg-red-50 rounded" :class="items.length === 1 ? 'opacity-30 cursor-not-allowed' : ''" :disabled="items.length === 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-4 bg-gray-50 border-t border-gray-100">
                <div class="flex flex-col sm:flex-row sm:justify-between gap-3 text-sm">
                    <div class="flex gap-6">
                        <div>Total Debit: <span class="font-semibold" x-text="'Rp ' + formatRupiah(totalDebit())"></span></div>
                        <div>Total Kredit: <span class="font-semibold" x-text="'Rp ' + formatRupiah(totalKredit())"></span></div>
                    </div>
                    <div>
                        Selisih: <span class="font-bold" :class="seimbang() ? 'text-emerald-600' : 'text-red-600'" x-text="'Rp ' + formatRupiah(selisih())"></span>
                    </div>
                </div>
                <div x-show="!seimbang()" class="mt-2 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    Jurnal belum seimbang. Debit harus sama dengan Kredit.
                </div>
            </div>

            <div class="px-4 py-4 flex justify-end">
                <x-loading-button size="lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Posting Jurnal
                </x-loading-button>
            </div>
        </x-card>
    </form>

    <script>
        function jurnalForm() {
            return {
                items: [{}],
                tambahItem() { this.items.push({}); },
                hapusItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
                update() {},
                totalDebit() { return this.items.reduce((s, it) => s + (parseFloat(it.debit)||0), 0); },
                totalKredit() { return this.items.reduce((s, it) => s + (parseFloat(it.kredit)||0), 0); },
                selisih() { return this.totalDebit() - this.totalKredit(); },
                seimbang() { return Math.abs(this.selisih()) < 0.01; },
                formatRupiah(n) { return (Math.round(n) ).toLocaleString('id-ID'); }
            };
        }
    </script>
</x-app-layout>
