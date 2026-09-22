<?php

namespace Database\Factories;

use App\Models\Donasi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Donasi>
 */
class DonasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nominal' => fake()->randomFloat(2, 10000, 1000000),
            'keterangan' => fake()->sentence(),
            'bukti_path' => null,
            'status' => Donasi::STATUS_PENDING,
        ];
    }

    /**
     * Donasi tanpa nominal (seikhlasnya, tanpa bukti & keterangan).
     */
    public function tanpaDetail(): static
    {
        return $this->state(fn (array $attributes) => [
            'nominal' => null,
            'keterangan' => null,
            'bukti_path' => null,
        ]);
    }

    /**
     * Donasi yang sudah dikonfirmasi admin.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Donasi::STATUS_CONFIRMED,
        ]);
    }
}
