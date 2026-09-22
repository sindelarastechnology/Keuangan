<x-app-layout>
    <x-slot name="title">Kalkulator Penyusutan</x-slot>

    <x-page-header title="Kalkulator Penyusutan" subtitle="Simulasi penyusutan metode garis lurus">
        <a href="{{ route('penyusutan.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <x-card title="Parameter">
            <div class="p-5 space-y-5" x-data="kalkulatorPenyusutan()">
                <div>
                    <x-input-label for="k_harga" value="Harga Beli (Rp)" />
                    <x-text-input id="k_harga" type="number" step="0.01" min="0" x-model="harga" class="mt-1" placeholder="Contoh: 10000000" required />
                </div>
                <div>
                    <x-input-label for="k_residu" value="Perkiraan Harga Jual Nanti (Rp)" />
                    <x-text-input id="k_residu" type="number" step="0.01" min="0" x-model="residu" class="mt-1" placeholder="0" />
                </div>
                <div>
                    <x-input-label for="k_masa" value="Lama Dipakai (Bulan)" />
                    <x-text-input id="k_masa" type="number" step="1" min="1" x-model="masa" class="mt-1" placeholder="Contoh: 60" required />
                </div>
                <div>
                    <x-input-label for="k_mulai" value="Mulai Penyusutan (Bulan)" />
                    <input type="month" id="k_mulai" x-model="mulai" class="mt-1 border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500 w-full">
                </div>

                <div class="rounded-lg bg-slate-50 border border-gray-200 px-4 py-3 space-y-1.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Nilai Disusutkan</span>
                        <strong x-text="rupiah(nilaiDisusutkan)">-</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Biaya / Bulan</span>
                        <strong class="text-emerald-700" x-text="rupiah(bebanBulanan)">-</strong>
                    </div>
                    <p x-show="!masa" class="pt-1 text-xs text-gray-400">Isi Harga dan Lama Dipakai dulu untuk melihat perkiraan.</p>
                </div>
            </div>
        </x-card>

        <div class="lg:col-span-2">
            <x-card title="Jadwal Penyusutan">
                <div class="overflow-x-auto" x-data="kalkulatorPenyusutan()">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Periode</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Biaya</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total Penyusutan</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Sisa Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="row in jadwal" :key="row.periode">
                                <tr>
                                    <td class="px-4 py-3 text-gray-600" x-text="row.periode"></td>
                                    <td class="px-4 py-3 text-right text-gray-800" x-text="rupiah(row.beban)"></td>
                                    <td class="px-4 py-3 text-right text-gray-600" x-text="rupiah(row.akumulasi)"></td>
                                    <td class="px-4 py-3 text-right font-medium text-emerald-700" x-text="rupiah(row.nilaiBuku)"></td>
                                </tr>
                            </template>
                            <tr x-show="!masa">
                                <td colspan="4" class="px-4 py-16 text-center text-gray-500">Isi Harga dan Lama Dipakai dulu untuk melihat jadwal penyusutan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>

    <script>
        function kalkulatorPenyusutan() {
            return {
                harga: '',
                residu: '',
                masa: '',
                mulai: (() => {
                    const d = new Date();
                    d.setMonth(d.getMonth() + 1);
                    return d.toISOString().slice(0, 7);
                })(),
                nilaiDisusutkan() {
                    return Math.max(0, (parseFloat(this.harga) || 0) - (parseFloat(this.residu) || 0));
                },
                bebanBulanan() {
                    return Math.max(0, Math.round(this.nilaiDisusutkan() / (parseInt(this.masa) || 1)));
                },
                rupiah(v) {
                    v = Math.round(v);
                    if (! isFinite(v)) return '';
                    return 'Rp ' + v.toLocaleString('id-ID');
                },
                get jadwal() {
                    const n = parseInt(this.masa) || 0;
                    const beban = this.bebanBulanan();
                    if (n <= 0 || beban <= 0 || !/^\d{4}-\d{2}$/.test(this.mulai || '')) {
                        return [];
                    }
                    const [y, m] = this.mulai.split('-').map(Number);
                    const rows = [];
                    let akumulasi = 0;
                    for (let i = 0; i < n; i++) {
                        let periode;
                        const total = y * 12 + (m - 1) + i;
                        const py = Math.floor(total / 12);
                        const pm = (total % 12) + 1;
                        periode = py + '-' + String(pm).padStart(2, '0');
                        akumulasi = Math.min(this.nilaiDisusutkan(), akumulasi + beban);
                        rows.push({ periode, beban, akumulasi: Math.round(akumulasi), nilaiBuku: this.nilaiDisusutkan() - akumulasi });
                    }
                    return rows;
                },
            };
        }
    </script>
</x-app-layout>