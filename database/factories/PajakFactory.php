<?php

namespace Database\Factories;

use App\Models\Pajak;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pajak>
 */
class PajakFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => 'PPN '.$this->faker->randomElement(['Masukan', 'Keluaran']),
            'rate' => 11.00,
            'is_aktif' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_aktif' => false,
        ]);
    }
}
