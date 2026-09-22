<?php

namespace Database\Factories;

use App\Models\AkunPerkiraan;
use App\Models\Aset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Aset>
 */
class AsetFactory extends Factory
{
    protected $model = Aset::class;

    public function definition(): array
    {
        return [
            'kode' => Aset::generateKode(),
            'nama' => $this->faker->words(3, true),
            'kategori' => $this->faker->optional()->word(),
            'lokasi' => $this->faker->optional()->city(),
            'tanggal_perolehan' => $this->faker->date(),
            'harga_perolehan' => $this->faker->numberBetween(1000000, 50000000),
            'nilai_residu' => 0,
            'masa_manfaat_bulan' => 60,
            'akun_aset_id' => AkunPerkiraan::where('kode', '121')->value('id') ?: 1,
            'akun_akumulasi_id' => AkunPerkiraan::where('kode', '122')->value('id') ?: 1,
            'akun_beban_id' => AkunPerkiraan::where('kode', '526')->value('id') ?: 1,
            'sumber_dana_id' => null,
            'catat_perolehan' => false,
            'status' => 'aktif',
            'keterangan' => $this->faker->optional()->sentence(),
        ];
    }

    public function aktif(): static
    {
        return $this->state(fn () => ['status' => 'aktif']);
    }

    public function nonaktif(): static
    {
        return $this->state(fn () => ['status' => 'nonaktif']);
    }

    public function selesai(): static
    {
        return $this->state(fn () => ['status' => 'selesai']);
    }

    public function garisLurus(int $harga, int $masaManfaatBulan, string $tanggalPerolehan): static
    {
        return $this->state(fn () => [
            'harga_perolehan' => $harga,
            'nilai_residu' => 0,
            'masa_manfaat_bulan' => $masaManfaatBulan,
            'tanggal_perolehan' => $tanggalPerolehan,
        ]);
    }
}
