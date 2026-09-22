<?php

namespace Database\Factories;

use App\Models\AkunPerkiraan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AkunPerkiraan>
 */
class AkunPerkiraanFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jenis = $this->faker->randomElement(['aset', 'kewajiban', 'modal', 'pendapatan', 'beban']);
        $saldoNormal = in_array($jenis, ['aset', 'beban']) ? 'debit' : 'kredit';

        return [
            'kode' => $this->faker->unique()->numerify('###'),
            'nama' => $this->faker->words(3, true),
            'jenis' => $jenis,
            'saldo_normal' => $saldoNormal,
            'is_header' => false,
            'parent_id' => null,
            'keterangan' => $this->faker->optional()->sentence(),
            'is_aktif' => true,
        ];
    }

    public function header(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_header' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_aktif' => false,
        ]);
    }
}
