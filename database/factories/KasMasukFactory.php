<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\KasMasuk;
use App\Models\Pajak;
use App\Models\Rekening;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KasMasuk>
 */
class KasMasukFactory extends Factory
{
    /**
     * Atribut non-fillable yang akan di-forceFill saat penyimpanan.
     *
     * @var array<string, mixed>
     */
    protected array $approvalForced = ['approval_status' => 'pending_review'];

    protected function configure(): void
    {
        $this->afterMaking(function (KasMasuk $model): void {
            $model->forceFill($this->approvalForced);
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $total = $this->faker->randomFloat(2, 50000, 5000000);
        $pajakNominal = $this->faker->boolean(30) ? $total * 0.11 : 0;

        return [
            'nomor' => 'KM'.date('Ymd').'-'.str_pad($this->faker->unique()->numberBetween(1, 999), 4, '0', STR_PAD_LEFT),
            'tanggal' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'rekening_id' => Rekening::factory(),
            'customer_id' => $this->faker->boolean(60) ? Customer::factory() : null,
            'keterangan' => $this->faker->optional()->sentence(),
            'total' => $total,
            'pajak_id' => $pajakNominal > 0 ? Pajak::factory() : null,
            'pajak_nominal' => $pajakNominal,
            'grand_total' => $total + $pajakNominal,
        ];
    }

    public function approved(): static
    {
        $this->approvalForced = ['approval_status' => 'approved', 'approved_at' => now()];

        return $this;
    }

    public function rejected(): static
    {
        $this->approvalForced = ['approval_status' => 'rejected', 'approval_reason' => $this->faker->sentence()];

        return $this;
    }
}
