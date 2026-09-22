<x-app-layout>
    <x-slot name="title">{{ isset($daftarHarga) ? 'Edit' : 'Tambah' }} Daftar Harga</x-slot>

    <x-page-header title="{{ isset($daftarHarga) ? 'Edit Daftar Harga' : 'Tambah Daftar Harga' }}">
        <a href="{{ route('daftar-harga.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <div class="max-w-4xl">
        <x-card>
            <form method="POST" action="{{ isset($daftarHarga) ? route('daftar-harga.update', $daftarHarga) : route('daftar-harga.store') }}" x-data="hargaForm()" class="p-6 space-y-6">
                @csrf
                @if(isset($daftarHarga)) @method('PUT') @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="entitas" value="Tipe Harga" />
                        <template x-if="!{{ isset($daftarHarga) ? 'true' : 'false' }}">
                            <x-select id="entitas" name="entitas" class="mt-1" @change="ubahEntitas()">
                                <option value="supplier" {{ old('entitas', $entitas ?? 'supplier') === 'supplier' ? 'selected' : '' }}>Harga Beli (Supplier)</option>
                                <option value="customer" {{ old('entitas', $entitas ?? 'supplier') === 'customer' ? 'selected' : '' }}>Harga Jual (Customer)</option>
                            </x-select>
                        </template>
                        <template x-if="{{ isset($daftarHarga) ? 'true' : 'false' }}">
                            <input type="hidden" name="entitas" :value="entitas">
                        </template>
                        <p class="text-xs text-gray-400 mt-1">Beli = harga dari supplier; Jual = harga untuk customer.</p>
                        <x-input-error :messages="$errors->get('entitas')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="barang_id" value="Barang" />
                        <x-select id="barang_id" name="barang_id" class="mt-1" required :disabled="isset($daftarHarga)">
                            @foreach($barang as $b)
                                <option value="{{ $b->id }}" {{ old('barang_id', $daftarHarga->barang_id ?? '') == $b->id ? 'selected' : '' }}>{{ $b->label }}</option>
                            @endforeach
                        </x-select>
                        @if(isset($daftarHarga)) <input type="hidden" name="barang_id" value="{{ $daftarHarga->barang_id }}"> @endif
                        <x-input-error :messages="$errors->get('barang_id')" class="mt-1" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5" x-show="entitas === 'supplier'">
                    <div>
                        <x-input-label for="supplier_id" value="Supplier" />
                        <x-select id="supplier_id" name="supplier_id" class="mt-1" :disabled="isset($daftarHarga)">
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ old('supplier_id', $daftarHarga->supplier_id ?? '') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                            @endforeach
                        </x-select>
                        @if(isset($daftarHarga)) <input type="hidden" name="supplier_id" value="{{ $daftarHarga->supplier_id }}"> @endif
                        <x-input-error :messages="$errors->get('supplier_id')" class="mt-1" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5" x-show="entitas === 'customer'">
                    <div>
                        <x-input-label for="customer_id" value="Customer" />
                        <x-select id="customer_id" name="customer_id" class="mt-1" :disabled="isset($daftarHarga)">
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id', $daftarHarga->customer_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                            @endforeach
                        </x-select>
                        @if(isset($daftarHarga)) <input type="hidden" name="customer_id" value="{{ $daftarHarga->customer_id }}"> @endif
                        <x-input-error :messages="$errors->get('customer_id')" class="mt-1" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="tanggal_mulai" value="Tanggal Mulai (opsional)" />
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', isset($daftarHarga) ? ($daftarHarga->tanggal_mulai?->format('Y-m-d') ?? '') : '') }}" class="mt-1 border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500 w-full">
                        <p class="text-xs text-gray-400 mt-1">Kosongkan jika berlaku terus-menerus</p>
                    </div>
                    <div>
                        <x-input-label for="tanggal_selesai" value="Tanggal Selesai (opsional)" />
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', isset($daftarHarga) ? ($daftarHarga->tanggal_selesai?->format('Y-m-d') ?? '') : '') }}" class="mt-1 border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500 w-full">
                        <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ada batas waktu</p>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-3">
                        <x-input-label value="Tier Harga" />
                        <button type="button" @click="tambahTier()" class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Tambah Tier
                        </button>
                    </div>

                    <div class="border border-gray-200 rounded-lg divide-y divide-gray-200">
                        <template x-for="(tier, index) in tiers" :key="index">
                            <div class="flex flex-wrap items-end gap-3 p-3 bg-white">
                                <input type="hidden" :name="'tiers['+index+'][id]'" :value="tier.id || ''">
                                <div class="flex-shrink-0 w-28">
                                    <label class="text-xs text-gray-500">Min Qty</label>
                                    <input type="number" step="1" min="1" :name="'tiers['+index+'][min_qty]'" x-model.number="tier.min_qty" class="mt-0.5 border-gray-300 rounded-lg text-sm w-full text-right" required>
                                </div>
                                <div class="flex-shrink-0 w-28">
                                    <label class="text-xs text-gray-500">Max Qty</label>
                                    <input type="number" step="1" min="1" :name="'tiers['+index+'][max_qty]'" x-model.number="tier.max_qty" placeholder="Tanpa batas" class="mt-0.5 border-gray-300 rounded-lg text-sm w-full text-right">
                                </div>
                                <div class="flex-shrink-0 w-40">
                                    <label class="text-xs text-gray-500">Harga (Rp)</label>
                                    <input type="number" step="1" min="0" :name="'tiers['+index+'][harga]'" x-model.number="tier.harga" class="mt-0.5 border-gray-300 rounded-lg text-sm w-full text-right font-semibold" required>
                                </div>
                                <div class="flex-grow min-w-[140px]">
                                    <label class="text-xs text-gray-500">Keterangan</label>
                                    <input type="text" :name="'tiers['+index+'][keterangan]'" x-model="tier.keterangan" placeholder="Opsional" class="mt-0.5 border-gray-300 rounded-lg text-sm w-full">
                                </div>
                                <button type="button" @click="hapusTier(index)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0" :class="tiers.length <= 1 ? 'opacity-30 cursor-not-allowed' : ''" :disabled="tiers.length <= 1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    <x-input-error :messages="$errors->get('tiers')" class="mt-2" />
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('daftar-harga.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-lg">Batal</a>
                    <x-loading-button>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ isset($daftarHarga) ? 'Simpan Perubahan' : 'Simpan Harga' }}
                    </x-loading-button>
                </div>
            </form>
        </x-card>
    </div>

    <script>
        function hargaForm() {
            var existingTiers = @json($formTiers);
            return {
                entitas: '{{ old('entitas', $entitas ?? 'supplier') }}',
                tiers: existingTiers,
                ubahEntitas() {
                    var el = document.getElementById('entitas');
                    if (el) { this.entitas = el.value; }
                },
                tambahTier() {
                    var lastTier = this.tiers[this.tiers.length - 1];
                    var nextMin = (lastTier.max_qty ? lastTier.max_qty : lastTier.min_qty) + 1;
                    this.tiers.push({ id: null, min_qty: nextMin, max_qty: null, harga: 0, keterangan: '' });
                },
                hapusTier(i) {
                    if (this.tiers.length > 1) this.tiers.splice(i, 1);
                }
            };
        }
    </script>
</x-app-layout>
