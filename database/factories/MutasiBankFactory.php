<?php

namespace Database\Factories;

use App\Models\MutasiBank;
use App\Models\Rekening;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MutasiBank>
 */
class MutasiBankFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomor' => 'MB'.date('Ymd').'-'.str_pad($this->faker->unique()->numberBetween(1, 999), 4, '0', STR_PAD_LEFT),
            'tanggal' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'rekening_asal_id' => Rekening::factory(),
            'rekening_tujuan_id' => Rekening::factory(),
            'nominal' => $this->faker->randomFloat(2, 100000, 10000000),
            'keterangan' => $this->faker->optional()->sentence(),
        ];
    }
}
