<x-app-layout>
    <x-slot name="title">Perubahan Stok Baru</x-slot>

    <x-page-header title="Perubahan Stok Baru" subtitle="Catat stok rusak, hilang, salah isi, atau bertambah (lebih)">
        <a href="{{ route('perubahan-stok.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <form method="POST" action="{{ route('perubahan-stok.store') }}" x-data="perubahanStokForm()">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
            <x-card>
                <div class="p-4 space-y-4">
                    <div>
                        <x-input-label for="tanggal" value="Tanggal" />
                        <x-text-input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" class="mt-1" required />
                        <x-input-error :messages="$errors->get('tanggal')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="jenis" value="Jenis Penyesuaian" />
                        <x-select id="jenis" name="jenis" class="mt-1" required>
                            <option value="rusak" {{ old('jenis') === 'rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="hilang" {{ old('jenis') === 'hilang' ? 'selected' : '' }}>Hilang</option>
                            <option value="salah" {{ old('jenis') === 'salah' ? 'selected' : '' }}>Salah Catat</option>
                            <option value="lebih" {{ old('jenis') === 'lebih' ? 'selected' : '' }}>Stok Lebih</option>
                            <option value="lainnya" {{ old('jenis', 'rusak') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </x-select>
                        <x-input-error :messages="$errors->get('jenis')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="keterangan" value="Keterangan (Opsional)" />
                        <textarea id="keterangan" name="keterangan" rows="3" class="mt-1 block w-full border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">{{ old('keterangan') }}</textarea>
                    </div>
                    @if($errors->has('error') || session('error'))
                        <p class="text-red-500 text-sm">{{ $errors->first('error') ?? session('error') }}</p>
                    @endif
                </div>
            </x-card>

            <div class="lg:col-span-2">
                <x-card>
                    <div class="p-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h3 class="font-semibold text-gray-800">Item Perubahan</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Arah <span class="text-emerald-600 font-medium">Bertambah (+)</span> atau <span class="text-red-500 font-medium">Berkurang (−)</span>. Nilai memakai harga rata-rata stok (ratt).</p>
                        </div>
                        <button type="button" @click="tambahItem()" class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Tambah Baris
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Barang</th>
                                    <th class="px-2 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Arah</th>
                                    <th class="px-2 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-2 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Harga Rata-rata</th>
                                    <th class="px-2 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Nilai</th>
                                    <th class="px-2 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                                    <th class="px-2 py-3 w-10"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="index">
                                    <tr>
                                        <td class="px-2 py-2 min-w-[180px]">
                                            <select x-model="item.barang_id" :name="'items['+index+'][barang_id]'" class="border-gray-300 rounded-lg text-sm w-full" required>
                                                <option value="">Pilih Barang...</option>
                                                @foreach($barang as $br)
                                                    <option value="{{ $br->id }}">{{ $br->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-2 py-2">
                                            <select x-model="item.arah" :name="'items['+index+'][arah]'" class="border-gray-300 rounded-lg text-sm w-full">
                                                <option value="masuk">Bertambah (+)</option>
                                                <option value="keluar">Berkurang (−)</option>
                                            </select>
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="number" step="0.01" :name="'items['+index+'][jumlah]'" x-model.number="item.jumlah" placeholder="0" class="border-gray-300 rounded-lg text-sm w-24 text-right" required>
                                        </td>
                                        <td class="px-2 py-2 text-right text-gray-600" x-text="'Rp ' + formatAngka(avgBarang(item.barang_id))"></td>
                                        <td class="px-2 py-2 text-right font-semibold" x-text="'Rp ' + formatAngka(subItem(item))"></td>
                                        <td class="px-2 py-2">
                                            <input type="text" :name="'items['+index+'][keterangan]'" x-model="item.keterangan" placeholder="Opsional" class="border-gray-300 rounded-lg text-sm w-full">
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

                    <div class="px-4 py-4 border-t border-gray-100 bg-gray-50 space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Total Nilai</span><span class="font-bold text-emerald-700 text-xl" x-text="'Rp ' + formatAngka(total())"></span>
                        </div>
                    </div>

                    <div class="px-4 py-4 flex justify-end">
                        <x-loading-button size="lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Perubahan Stok
                        </x-loading-button>
                    </div>
                </x-card>
            </div>
        </div>
    </form>

    <script>
        function perubahanStokForm() {
            var avgMap = @json($barang->pluck('harga_avg', 'id')->map(fn ($h) => (float) $h));
            return {
                items: [{}],
                tambahItem() { this.items.push({ arah: 'masuk' }); },
                hapusItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
                avgBarang(id) { return avgMap[id] || 0; },
                subItem(it) { return ((parseFloat(it.jumlah) || 0) * (avgMap[it.barang_id] || 0)); },
                total() { return this.items.reduce((s, it) => s + this.subItem(it), 0); },
                formatAngka(n) { return Math.round(n).toLocaleString('id-ID'); }
            };
        }
    </script>
</x-app-layout>