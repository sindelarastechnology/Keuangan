<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Gudang;
use App\Models\StokGudang;
use App\Models\TransferGudang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GudangTransferTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function login(): void
    {
        $this->actingAs(User::where('email', 'admin@keuangan.test')->firstOrFail());
    }

    private function gudangUtama(): Gudang
    {
        return Gudang::utama();
    }

    private function buatCabang(): Gudang
    {
        return Gudang::create(['kode' => 'GDG-0002', 'nama' => 'Gudang Cabang', 'is_aktif' => true]);
    }

    private function buatBarang(float $stok = 10): Barang
    {
        return Barang::create([
            'kode' => 'BRG-8001',
            'nama' => 'Produk Tes',
            'tipe' => 'barang',
            'satuan' => 'pcs',
            'stok' => $stok,
            'harga_beli' => 5000,
            'harga_jual' => 10000,
            'is_aktif' => true,
        ]);
    }

    public function test_index_gudang_menampilkan_gudang_utama_dari_migration(): void
    {
        $this->login();

        $this->get(route('gudang.index'))
            ->assertOk()
            ->assertSee('Gudang Umum')
            ->assertSee('GDG-0001')
            ->assertSee('Utama');
    }

    public function test_store_gudang_membuat_kode_dan_redirect(): void
    {
        $this->login();

        $this->post(route('gudang.store'), [
            'nama' => 'Gudang Baru',
            'alamat' => 'Jl. Contoh 1',
            'is_aktif' => '1',
        ])->assertSessionHasNoErrors()->assertRedirect(route('gudang.index'))->assertSessionHas('success');

        $gudang = Gudang::where('nama', 'Gudang Baru')->firstOrFail();
        $this->assertStringStartsWith('GDG-', $gudang->kode);
        $this->assertTrue($gudang->is_aktif);
        $this->assertNotEquals($this->gudangUtama()->id, $gudang->id);
    }

    public function test_update_gudang(): void
    {
        $this->login();
        $cabang = $this->buatCabang();

        $this->put(route('gudang.update', $cabang), ['nama' => 'Gudang Renovasi', 'is_aktif' => '0'])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('gudang.index'));

        $this->assertEquals('Gudang Renovasi', $cabang->fresh()->nama);
        $this->assertFalse($cabang->fresh()->is_aktif);
    }

    public function test_destroy_gudang_utama_ditolak(): void
    {
        $this->login();
        $utama = $this->gudangUtama();

        $this->delete(route('gudang.destroy', $utama))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('gudangs', ['id' => $utama->id]);
    }

    public function test_destroy_gudang_dengan_produk_ditolak(): void
    {
        $this->login();
        $cabang = $this->buatCabang();
        Barang::create([
            'kode' => 'BRG-8002',
            'nama' => 'Produk Cabang',
            'tipe' => 'barang',
            'gudang_id' => $cabang->id,
            'stok' => 2,
            'harga_beli' => 1000,
            'harga_jual' => 2000,
            'is_aktif' => true,
        ]);

        $this->delete(route('gudang.destroy', $cabang))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('gudangs', ['id' => $cabang->id]);
    }

    public function test_destroy_gudang_dengan_stok_ditolak(): void
    {
        $this->login();
        $cabang = $this->buatCabang();
        $barang = $this->buatBarang();
        StokGudang::create(['barang_id' => $barang->id, 'gudang_id' => $cabang->id, 'qty' => 2]);

        $this->delete(route('gudang.destroy', $cabang))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('gudangs', ['id' => $cabang->id]);
    }

    public function test_destroy_gudang_kosong_berhasil(): void
    {
        $this->login();
        $cabang = $this->buatCabang();

        $this->delete(route('gudang.destroy', $cabang))
            ->assertRedirect(route('gudang.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('gudangs', ['id' => $cabang->id]);
    }

    public function test_halaman_index_dan_create_transfer_merender(): void
    {
        $this->login();
        $this->buatCabang();
        $this->buatBarang();

        $this->get(route('transfer-gudang.index'))->assertOk()->assertSee('Transfer Barang');
        $this->get(route('transfer-gudang.create'))->assertOk()->assertSee('Gudang Asal');
    }

    public function test_transfer_gudang_memindahkan_stok_tetap_invarian(): void
    {
        $this->login();
        $utama = $this->gudangUtama();
        $cabang = $this->buatCabang();
        $barang = $this->buatBarang(10);
        $this->assertEquals(10, (float) $barang->stok);

        $response = $this->post(route('transfer-gudang.store'), [
            'tanggal' => now()->toDateString(),
            'gudang_asal' => $utama->id,
            'gudang_tujuan' => $cabang->id,
            'keterangan' => 'Penambahan stok cabang',
            'items' => [
                ['barang_id' => $barang->id, 'jumlah' => 3, 'keterangan' => null],
            ],
        ]);

        $response->assertSessionHasNoErrors()->assertSessionHas('success');

        $transfer = TransferGudang::firstOrFail();
        $this->assertStringStartsWith('TG/', $transfer->nomor);
        $this->assertEquals($utama->id, $transfer->gudang_asal);
        $this->assertEquals($cabang->id, $transfer->gudang_tujuan);
        $this->assertCount(1, $transfer->items);

        $this->assertEquals(7, (float) StokGudang::where('barang_id', $barang->id)->where('gudang_id', $utama->id)->value('qty'));
        $this->assertEquals(3, (float) StokGudang::where('barang_id', $barang->id)->where('gudang_id', $cabang->id)->value('qty'));
        $this->assertEquals(10, (float) $barang->fresh()->stok);
    }

    public function test_transfer_gudang_stok_tidak_cukup_ditolak(): void
    {
        $this->login();
        $utama = $this->gudangUtama();
        $cabang = $this->buatCabang();
        $barang = $this->buatBarang(5);

        $this->post(route('transfer-gudang.store'), [
            'tanggal' => now()->toDateString(),
            'gudang_asal' => $utama->id,
            'gudang_tujuan' => $cabang->id,
            'items' => [
                ['barang_id' => $barang->id, 'jumlah' => 10],
            ],
        ])->assertSessionHas('error');

        $this->assertDatabaseCount('transfer_gudang', 0);
        $this->assertEquals(5, (float) StokGudang::where('barang_id', $barang->id)->where('gudang_id', $utama->id)->value('qty'));
        $this->assertNull(StokGudang::where('barang_id', $barang->id)->where('gudang_id', $cabang->id)->first());
    }

    public function test_transfer_gudang_asal_sama_tujuan_ditolak(): void
    {
        $this->login();
        $utama = $this->gudangUtama();
        $barang = $this->buatBarang(5);

        $this->post(route('transfer-gudang.store'), [
            'tanggal' => now()->toDateString(),
            'gudang_asal' => $utama->id,
            'gudang_tujuan' => $utama->id,
            'items' => [
                ['barang_id' => $barang->id, 'jumlah' => 2],
            ],
        ])->assertSessionHas('error');

        $this->assertDatabaseCount('transfer_gudang', 0);
    }

    public function test_index_gudang_menampilkan_galeri_stok_per_gudang(): void
    {
        $this->login();
        $this->gudangUtama();
        $barang = $this->buatBarang(10);

        $this->get(route('gudang.index'))
            ->assertOk()
            ->assertSee('Stok Barang — Gudang Umum')
            ->assertSee($barang->nama)
            ->assertSee('Stok 10 pcs');
    }

    public function test_index_gudang_tidak_menampilkan_item_stok_nol(): void
    {
        $this->login();
        $this->gudangUtama();
        $barangKosong = $this->buatBarang(0);

        $this->get(route('gudang.index'))
            ->assertOk()
            ->assertDontSee($barangKosong->nama);
    }

    public function test_halaman_show_transfer_merender(): void
    {
        $this->login();
        $utama = $this->gudangUtama();
        $cabang = $this->buatCabang();
        $barang = $this->buatBarang(5);

        $this->post(route('transfer-gudang.store'), [
            'tanggal' => now()->toDateString(),
            'gudang_asal' => $utama->id,
            'gudang_tujuan' => $cabang->id,
            'items' => [['barang_id' => $barang->id, 'jumlah' => 2]],
        ])->assertSessionHas('success');

        $transfer = TransferGudang::firstOrFail();
        $this->get(route('transfer-gudang.show', $transfer))
            ->assertOk()
            ->assertSee($transfer->nomor)
            ->assertSee('Gudang Cabang');
    }
}
