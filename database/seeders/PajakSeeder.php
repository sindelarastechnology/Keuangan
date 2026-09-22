<?php

namespace Database\Seeders;

use App\Models\Pajak;
use Illuminate\Database\Seeder;

class PajakSeeder extends Seeder
{
    public function run(): void
    {
        Pajak::updateOrCreate(
            ['nama' => 'PPN'],
            ['rate' => 11, 'is_aktif' => true]
        );
    }
}
