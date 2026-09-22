<?php

namespace Database\Factories;

use App\Models\Pajak;
use App\Models\Pembelian;
use App\Models\Rekening;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pembelian>
 */
class PembelianFactory extends Factory
{
    /**
     * Atribut non-fillable yang akan di-forceFill saat penyimpanan.
     *
     * @var array<string, mixed>
     */
    protected array $approvalForced = ['approval_status' => 'pending_review'];

    protected function configure(): void
    {
        $this->afterMaking(function (Pembelian $model): void {
            $model->forceFill($this->approvalForced);
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 100000, 10000000);
        $diskonNominal = $this->faker->boolean(40) ? $subtotal * 0.05 : 0;
        $afterDiskon = $subtotal - $diskonNominal;
        $pajakNominal = $this->faker->boolean(70) ? $afterDiskon * 0.11 : 0;
        $total = $afterDiskon + $pajakNominal;

        return [
            'nomor' => 'PB'.date('Ymd').'-'.str_pad($this->faker->unique()->numberBetween(1, 999), 4, '0', STR_PAD_LEFT),
            'tanggal' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'supplier_id' => Supplier::factory(),
            'metode_bayar' => $this->faker->randomElement(['tunai', 'kredit']),
            'rekening_id' => $this->faker->boolean(50) ? Rekening::factory() : null,
            'subtotal' => $subtotal,
            'diskon' => $diskonNominal > 0 ? 5 : 0,
            'diskon_tipe' => 'persen',
            'diskon_nominal' => $diskonNominal,
            'pajak_id' => $pajakNominal > 0 ? Pajak::factory() : null,
            'pajak_nominal' => $pajakNominal,
            'total' => $total,
            'status' => 'posted',
            'keterangan' => $this->faker->optional()->sentence(),
        ];
    }

    public function tunai(): static
    {
        return $this->state(fn (array $attributes) => [
            'metode_bayar' => 'tunai',
            'rekening_id' => Rekening::factory(),
        ]);
    }

    public function kredit(): static
    {
        return $this->state(fn (array $attributes) => [
            'metode_bayar' => 'kredit',
            'rekening_id' => null,
        ]);
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
