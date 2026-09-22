<?php

namespace Tests\Feature;

use App\Services\NomorGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NomorGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_nomor_generator_berurutan_dalam_bulan_yang_sama(): void
    {
        $this->assertSame('KM/09/2026/0001', NomorGenerator::generate('KM', '2026-09-05'));
        $this->assertSame('KM/09/2026/0002', NomorGenerator::generate('KM', '2026-09-06'));
        $this->assertSame('KM/09/2026/0003', NomorGenerator::generate('KM', '2026-09-30'));
    }

    public function test_nomor_generator_reset_di_bulan_baru(): void
    {
        NomorGenerator::generate('KM', '2026-09-05');

        $this->assertSame('KM/10/2026/0001', NomorGenerator::generate('KM', '2026-10-01'));
    }

    public function test_nomor_generator_prefix_berbeda_punya_counter_terpisah(): void
    {
        $this->assertSame('KM/09/2026/0001', NomorGenerator::generate('KM', '2026-09-05'));
        $this->assertSame('KK/09/2026/0001', NomorGenerator::generate('KK', '2026-09-05'));
        $this->assertSame('KM/09/2026/0002', NomorGenerator::generate('KM', '2026-09-06'));
        $this->assertSame('KK/09/2026/0002', NomorGenerator::generate('KK', '2026-09-06'));
    }

    public function test_nomor_generator_melanjutkan_nomor_terakhir_ketika_key_kosong(): void
    {
        DB::table('jurnal_umum')->insert([
            ['nomor' => 'MAN/09/2026/0007', 'tanggal' => '2026-09-04'],
            ['nomor' => 'MAN/09/2026/0001', 'tanggal' => '2026-09-01'],
            ['nomor' => 'MAN/10/2026/0003', 'tanggal' => '2026-10-02'],
        ]);

        $this->assertSame('MAN/09/2026/0008', NomorGenerator::generate('MAN', '2026-09-20'));
        $this->assertSame('MAN/09/2026/0009', NomorGenerator::generate('MAN', '2026-09-21'));
    }

    public function test_nomor_generator_prefix_berbeda_di_tabel_yang_sama_tidak_saling_mengganggu(): void
    {
        DB::table('jurnal_umum')->insert([
            ['nomor' => 'MAN/09/2026/0005', 'tanggal' => '2026-09-02'],
        ]);

        $this->assertSame('HPP/09/2026/0001', NomorGenerator::generate('HPP', '2026-09-10'));
    }

    public function test_nomor_generator_tanpa_tanggal_menggunakan_hari_ini(): void
    {
        $nomor = NomorGenerator::generate('SO');

        $this->assertMatchesRegularExpression('#^SO/\d{2}/\d{4}/0001$#', $nomor);
    }
}
