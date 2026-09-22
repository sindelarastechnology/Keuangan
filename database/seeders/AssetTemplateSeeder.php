<?php

namespace Database\Seeders;

use App\Models\AkunPerkiraan;
use App\Models\AssetTemplate;
use Illuminate\Database\Seeder;

class AssetTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $akunAset = AkunPerkiraan::where('kode', '121')->value('id');
        $akunAkumulasi = AkunPerkiraan::where('kode', '122')->value('id');
        $akunBeban = AkunPerkiraan::where('kode', '526')->value('id');

        $templates = [
            ['nama_kategori' => 'Peralatan Kantor', 'masa_manfaat_bulan' => 60, 'persen_residu' => 0],
            ['nama_kategori' => 'Laptop/Komputer', 'masa_manfaat_bulan' => 48, 'persen_residu' => 10],
            ['nama_kategori' => 'Kendaraan', 'masa_manfaat_bulan' => 96, 'persen_residu' => 20],
            ['nama_kategori' => 'Mesin Produksi', 'masa_manfaat_bulan' => 60, 'persen_residu' => 0],
            ['nama_kategori' => 'Inventaris', 'masa_manfaat_bulan' => 60, 'persen_residu' => 0],
        ];

        foreach ($templates as $item) {
            AssetTemplate::updateOrCreate(
                ['nama_kategori' => $item['nama_kategori']],
                [
                    'masa_manfaat_bulan' => $item['masa_manfaat_bulan'],
                    'persen_residu' => $item['persen_residu'],
                    'akun_aset_id' => $akunAset,
                    'akun_akumulasi_id' => $akunAkumulasi,
                    'akun_beban_id' => $akunBeban,
                ]
            );
        }
    }
}
