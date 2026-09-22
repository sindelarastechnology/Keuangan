<?php

namespace Tests\Feature;

use App\Models\Aset;
use App\Models\KasKeluar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_pengguna_terdaftar_dapat_mengakses_semua_area(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('barang.index'))->assertOk();
        $this->get(route('jurnal.index'))->assertOk();
        $this->get(route('kas-masuk.index'))->assertOk();
        $this->get(route('pengaturan.index'))->assertOk();
    }

    public function test_semua_pengguna_dapat_menyetujui_transaksi(): void
    {
        $this->actingAs(User::factory()->create());

        $this->assertTrue((new KasKeluar)->canApprove(auth()->user()));
    }

    public function test_aset_disposisi_dan_selesai_terbuka_untuk_semua_pengguna(): void
    {
        $this->actingAs(User::factory()->create());
        $aset = Aset::factory()->create();

        $this->post(route('aset.selesai', $aset))->assertSessionHas('success');
        $this->assertDatabaseHas('asets', ['id' => $aset->id, 'status' => 'selesai']);
    }
}
