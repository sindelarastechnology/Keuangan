<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\Aset;
use App\Models\Barang;
use App\Models\BbHutang;
use App\Models\BbPiutang;
use App\Models\Customer;
use App\Models\DaftarHarga;
use App\Models\Rekening;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataKeamananTest extends TestCase
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

    private function buatAkun(string $kode, string $nama, ?int $parentId = null): AkunPerkiraan
    {
        return AkunPerkiraan::create([
            'kode' => $kode,
            'nama' => $nama,
            'jenis' => 'aset',
            'saldo_normal' => 'debit',
            'is_header' => false,
            'is_aktif' => true,
            'parent_id' => $parentId,
        ]);
    }

    private function buatBarang(): Barang
    {
        return Barang::create([
            'kode' => 'BRG-SEC'.substr((string) uniqid(), -5),
            'nama' => 'Barang Keamanan',
            'tipe' => 'barang',
            'stok' => 0,
            'harga_beli' => 1000,
            'harga_jual' => 2000,
            'is_aktif' => true,
        ]);
    }

    public function test_akun_perkiraan_dipakai_rekening_tidak_bisa_dihapus(): void
    {
        $this->login();
        $akun = $this->buatAkun('999', 'Akun Tes Hapus');
        Rekening::create([
            'jenis' => 'kas',
            'nama' => 'Kas Rekening Tes',
            'akun_id' => $akun->id,
            'saldo_awal' => 0,
            'is_aktif' => true,
        ]);

        $this->delete(route('akun-perkiraan.destroy', $akun))->assertSessionHas('error');

        $this->assertDatabaseHas('akun_perkiraan', ['id' => $akun->id]);
    }

    public function test_akun_perkiraan_menolak_parent_yang_menciptakan_siklus(): void
    {
        $this->login();
        $parent = $this->buatAkun('998', 'Induk Tes');
        $child = $this->buatAkun('997', 'Anak Tes', $parent->id);

        $response = $this->put(route('akun-perkiraan.update', $parent), [
            'kode' => $parent->kode,
            'nama' => $parent->nama,
            'jenis' => $parent->jenis,
            'saldo_normal' => $parent->saldo_normal,
            'is_header' => 0,
            'is_aktif' => 1,
            'parent_id' => $child->id,
        ]);

        $response->assertSessionHasErrors('parent_id');
        $this->assertNull($parent->fresh()->parent_id);
    }

    public function test_customer_dengan_piutang_tidak_bisa_dihapus(): void
    {
        $this->login();
        $customer = Customer::create(['kode' => 'C900', 'nama' => 'PT Terlindungi', 'is_aktif' => true]);
        BbPiutang::create([
            'customer_id' => $customer->id,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Piutang awal',
            'debit' => 0,
            'kredit' => 0,
            'saldo' => 0,
        ]);

        $this->delete(route('customer.destroy', $customer))->assertSessionHas('error');

        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }

    public function test_supplier_dengan_utang_tidak_bisa_dihapus(): void
    {
        $this->login();
        $supplier = Supplier::create(['kode' => 'S900', 'nama' => 'PT Terlindungi Supplier', 'is_aktif' => true]);
        BbHutang::create([
            'supplier_id' => $supplier->id,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Utang awal',
            'debit' => 0,
            'kredit' => 0,
            'saldo' => 0,
        ]);

        $this->delete(route('supplier.destroy', $supplier))->assertSessionHas('error');

        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id]);
    }

    public function test_edit_aset_nonaktif_tidak_mengaktifkan_lagi(): void
    {
        $this->login();
        $aset = Aset::factory()->nonaktif()->create();

        $response = $this->put(route('aset.update', $aset), [
            'nama' => 'Aset Diubah Nama',
            'kategori' => $aset->kategori,
            'tanggal_perolehan' => $aset->tanggal_perolehan,
            'harga_perolehan' => $aset->harga_perolehan,
            'nilai_residu' => $aset->nilai_residu,
            'masa_manfaat_bulan' => $aset->masa_manfaat_bulan,
            'status' => 'aktif',
            'keterangan' => '',
        ]);

        $response->assertRedirect();
        $this->assertSame('nonaktif', $aset->fresh()->status);
    }

    public function test_update_daftar_harga_menolak_tier_dari_kombinasi_lain(): void
    {
        $this->login();
        $supA = Supplier::create(['kode' => 'S-A'.substr((string) uniqid(), -5), 'nama' => 'Supplier A', 'is_aktif' => true]);
        $supB = Supplier::create(['kode' => 'S-B'.substr((string) uniqid(), -5), 'nama' => 'Supplier B', 'is_aktif' => true]);
        $barang = $this->buatBarang();

        $rowLuar = DaftarHarga::create([
            'entitas' => 'supplier',
            'supplier_id' => $supB->id,
            'barang_id' => $barang->id,
            'min_qty' => 1,
            'max_qty' => null,
            'harga' => 9000,
            'is_aktif' => true,
        ]);

        $response = $this->put(route('daftar-harga.update', $rowLuar), [
            'entitas' => 'supplier',
            'supplier_id' => $supA->id,
            'barang_id' => $barang->id,
            'tiers' => [
                [
                    'id' => $rowLuar->id,
                    'min_qty' => 1,
                    'max_qty' => null,
                    'harga' => 100000,
                    'keterangan' => '',
                ],
            ],
        ]);

        $response->assertSessionHas('error');

        $this->assertEquals(9000.0, (float) $rowLuar->fresh()->harga);
    }
}
