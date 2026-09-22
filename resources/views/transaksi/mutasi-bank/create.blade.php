<x-app-layout>
    <x-slot name="title">Transfer Rekening Baru</x-slot>

    <x-page-header title="Transfer Rekening Baru" subtitle="Transfer antar rekening (kas / bank)">
        <a href="{{ route('mutasi-bank.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <div class="max-w-3xl">
        <x-card>
            <form method="POST" action="{{ route('mutasi-bank.store') }}" class="p-6 space-y-5" x-data="mutasiBankForm()">
                @csrf

                <div x-show="saldoAsalKurang()" x-cloak x-transition class="px-4 py-3 bg-amber-50 border border-amber-300 rounded-lg text-sm text-amber-800">
                    Saldo rekening asal tidak mencukupi: saldo saat ini <span class="font-semibold" x-text="'Rp ' + formatRupiah(saldoAsal())"></span>,
                    sedangkan nominal transfer <span class="font-semibold" x-text="'Rp ' + formatRupiah(nominal())"></span>.
                    Transfer tetap dapat disimpan, namun saldo rekening asal menjadi minus.
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="tanggal" value="Tanggal" />
                        <x-text-input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" class="mt-1" required />
                        <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="nominal" value="Nominal Transfer" />
                        <x-text-input id="nominal" type="number" step="0.01" name="nominal" value="{{ old('nominal') }}" class="mt-1" placeholder="0" required x-model.number="nominalValue" />
                        <x-input-error :messages="$errors->get('nominal')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="rekening_asal_id" value="Rekening Asal" />
                        <x-select id="rekening_asal_id" name="rekening_asal_id" class="mt-1" required x-model="asalId">
                            <option value="">Pilih Rekening Asal...</option>
                            @foreach($rekenings as $b)
                                <option value="{{ $b->id }}" {{ old('rekening_asal_id') == $b->id ? 'selected' : '' }}>{{ $b->nama }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error :messages="$errors->get('rekening_asal_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="rekening_tujuan_id" value="Rekening Tujuan" />
                        <x-select id="rekening_tujuan_id" name="rekening_tujuan_id" class="mt-1" required>
                            <option value="">Pilih Rekening Tujuan...</option>
                            @foreach($rekenings as $b)
                                <option value="{{ $b->id }}" {{ old('rekening_tujuan_id') == $b->id ? 'selected' : '' }}>{{ $b->nama }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error :messages="$errors->get('rekening_tujuan_id')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="keterangan" value="Keterangan" />
                    <x-textarea id="keterangan" name="keterangan" class="mt-1" rows="3">{{ old('keterangan') }}</x-textarea>
                    <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                </div>

                <div class="flex justify-end pt-2">
                    <x-loading-button>
                        Simpan Transfer
                    </x-loading-button>
                </div>
            </form>
        </x-card>
    </div>

    <script>
        function mutasiBankForm() {
            var saldoMap = @json($saldoMap);
            return {
                asalId: '',
                nominalValue: 0,
                init() {
                    var sel = document.getElementById('rekening_asal_id');
                    this.asalId = sel ? sel.value : '';
                    var inp = document.getElementById('nominal');
                    this.nominalValue = inp ? parseFloat(inp.value) || 0 : 0;
                },
                nominal() { return parseFloat(this.nominalValue) || 0; },
                saldoAsal() {
                    return this.asalId ? (parseFloat(saldoMap[this.asalId]) || 0) : 0;
                },
                saldoAsalKurang() {
                    return this.asalId ? (this.saldoAsal() < this.nominal()) : false;
                },
                formatRupiah(n) { return n.toLocaleString('id-ID'); }
            };
        }
    </script>
</x-app-layout>
