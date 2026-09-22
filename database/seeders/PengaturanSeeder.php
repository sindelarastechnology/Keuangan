<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        $pengaturan = [
            'nama_perusahaan' => 'Perusahaan Saya',
            'alamat_perusahaan' => '',
            'telepon_perusahaan' => '',
            'email_perusahaan' => '',
            'kota_perusahaan' => '',
            'pajak' => 'PPN',
        ];

        foreach ($pengaturan as $key => $value) {
            Pengaturan::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
