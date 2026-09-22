<?php

namespace Database\Seeders;

use App\Models\PeriodeAkuntansi;
use Illuminate\Database\Seeder;

class PeriodeSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = now()->year;

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            PeriodeAkuntansi::updateOrCreate(
                ['bulan' => str_pad((string) $bulan, 2, '0', STR_PAD_LEFT), 'tahun' => (string) $tahun],
                ['kode' => $tahun.str_pad((string) $bulan, 2, '0', STR_PAD_LEFT)]
            );
        }

        // Buka periode berjalan
        $aktif = PeriodeAkuntansi::where('bulan', now()->format('m'))
            ->where('tahun', now()->format('Y'))
            ->first();

        if ($aktif) {
            PeriodeAkuntansi::where('is_open', true)->update(['is_open' => false]);
            $aktif->update(['is_open' => true, 'tanggal_buka' => now()->toDateString()]);
        }
    }
}
