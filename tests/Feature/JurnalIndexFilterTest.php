<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\JurnalUmum;
use App\Models\User;
use App\Services\JournalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JurnalIndexFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function login(): User
    {
        $user = User::where('email', 'admin@keuangan.test')->firstOrFail();
        $this->actingAs($user);

        return $user;
    }

    private function postJurnal(string $tipe): JurnalUmum
    {
        $akun = AkunPerkiraan::where('kode', '115')->firstOrFail()->id;

        return JournalService::post($tipe, now()->toDateString(), [
            ['akun_id' => $akun, 'debit' => 100000, 'kredit' => 0],
            ['akun_id' => $akun, 'debit' => 0, 'kredit' => 100000],
        ], 'Jurnal '.$tipe);
    }

    public function test_index_menampilkan_filters_retur_penjualan_retur_pembelian_penyesuaian_stok(): void
    {
        $this->login();

        $response = $this->get(route('jurnal.index'));

        $response->assertOk()
            ->assertSee('Retur Penjualan')
            ->assertSee('Retur Pembelian')
            ->assertSee('Penyesuaian Stok');
    }

    public function test_filter_tipe_retur_penjualan_hanya_menampilkan_jurnal_tersebut(): void
    {
        $this->login();
        $retur = $this->postJurnal('retur_penjualan');
        $manual = $this->postJurnal('manual');

        $response = $this->get(route('jurnal.index', ['tipe' => 'retur_penjualan']));

        $response->assertOk()
            ->assertSee($retur->nomor)
            ->assertDontSee($manual->nomor);
    }
}
