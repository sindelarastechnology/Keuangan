<?php

namespace Database\Factories;

use App\Models\AkunPerkiraan;
use App\Models\Rekening;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rekening>
 */
class RekeningFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jenis = $this->faker->randomElement(['kas', 'bank']);

        return [
            'jenis' => $jenis,
            'nama' => $jenis === 'kas' ? 'Kas '.$this->faker->word() : $this->faker->randomElement(['BCA', 'Mandiri', 'BRI', 'BNI']).' - '.$this->faker->word(),
            'nomor_rekening' => $jenis === 'bank' ? $this->faker->numerify('##########') : null,
            'nama_pemilik' => $jenis === 'bank' ? $this->faker->name() : null,
            'akun_id' => AkunPerkiraan::factory(),
            'saldo_awal' => $this->faker->randomFloat(2, 0, 10000000),
            'is_aktif' => true,
        ];
    }

    public function kas(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis' => 'kas',
            'nama' => 'Kas '.$this->faker->word(),
            'nomor_rekening' => null,
            'nama_pemilik' => null,
        ]);
    }

    public function bank(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis' => 'bank',
            'nama' => $this->faker->randomElement(['BCA', 'Mandiri', 'BRI', 'BNI']).' - '.$this->faker->word(),
            'nomor_rekening' => $this->faker->numerify('##########'),
            'nama_pemilik' => $this->faker->name(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_aktif' => false,
        ]);
    }
}
