<?php

namespace Tests\Feature;

use App\Models\Donasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DonasiFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
        $authUser = User::factory()->create();
        $this->actingAs($authUser);
        $this->user = $authUser;
    }

    public function test_halaman_donasi_menampilkan_qris_dan_riwayat(): void
    {
        $donasi = Donasi::factory()->for($this->user)->create();

        $this->get(route('donasi.index'))
            ->assertOk()
            ->assertSee('qris.png')
            ->assertSee('Dukung KasPro')
            ->assertSee($donasi->keterangan);
    }

    public function test_donasi_dengan_bukti_tersimpan(): void
    {
        $bukti = UploadedFile::fake()->image('bukti.jpg');

        $response = $this->post(route('donasi.store'), [
            'nominal' => '250000',
            'keterangan' => 'Dukungan aplikasi',
            'bukti' => $bukti,
        ]);

        $response->assertRedirect(route('donasi.index'));

        $donasi = Donasi::firstOrFail();

        $this->assertSame('250000.00', $donasi->nominal);
        $this->assertSame('Dukungan aplikasi', $donasi->keterangan);
        $this->assertSame(Donasi::STATUS_PENDING, $donasi->status);
        $this->assertSame($this->user->id, $donasi->user_id);
        Storage::disk('public')->assertExists($donasi->bukti_path);
    }

    public function test_donasi_tanpa_detail_opsional_tetap_tersimpan(): void
    {
        $this->post(route('donasi.store'), [])
            ->assertRedirect(route('donasi.index'));

        $donasi = Donasi::firstOrFail();

        $this->assertNull($donasi->nominal);
        $this->assertNull($donasi->keterangan);
        $this->assertNull($donasi->bukti_path);
    }

    public function test_nominal_negatif_ditolak(): void
    {
        $this->post(route('donasi.store'), ['nominal' => '-1000'])
            ->assertSessionHasErrors('nominal');

        $this->assertDatabaseCount('donasis', 0);
    }

    public function test_riwayat_hanya_menampilkan_donasi_milik_sendiri(): void
    {
        Donasi::factory()->for($this->user)->create(['keterangan' => 'Donasi punya aku']);
        Donasi::factory()->create(['keterangan' => 'Donasi orang lain']);

        $this->get(route('donasi.index'))
            ->assertOk()
            ->assertSee('Donasi punya aku')
            ->assertDontSee('Donasi orang lain');
    }
}
