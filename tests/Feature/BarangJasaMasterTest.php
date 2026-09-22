<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\BbPersediaan;
use App\Models\Customer;
use App\Models\DaftarHarga;
use App\Models\Kategori;
use App\Models\Pengaturan;
use App\Models\Satuan;
use App\Models\StokGudang;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BarangJasaMasterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function login(): void
    {
        $this->actingAs(User::where('email', 'admin@keuangan.test')->first());
    }

    private function makeBarang(array $overrides = []): Barang
    {
        return Barang::create(array_merge([
            'kode' => 'BRG-9001',
            'nama' => 'Tepung Terigu',
            'tipe' => 'barang',
            'satuan' => 'kg',
            'kategori' => 'Sembako',
            'stok' => 10,
            'harga_avg' => 8000,
            'harga_beli' => 8000,
            'harga_jual' => 10000,
            'min_stok' => 2,
            'is_aktif' => true,
        ], $overrides));
    }

    private function makeJasa(array $overrides = []): Barang
    {
        return Barang::create(array_merge([
            'kode' => 'JAS-9001',
            'nama' => 'Jasa Instalasi',
            'tipe' => 'jasa',
            'satuan' => 'paket',
            'kategori' => 'Jasa',
            'harga_jual' => 150000,
            'is_aktif' => true,
        ], $overrides));
    }

    public function test_index_barang_hanya_menampilkan_tipe_barang(): void
    {
        $this->login();
        $this->makeBarang();
        $this->makeJasa();

        $this->get(route('barang.index'))
            ->assertOk()
            ->assertSee('Tepung Terigu')
            ->assertDontSee('Jasa Instalasi')
            ->assertSee('Atur Kolom Tabel')
            ->assertSee("detail: 'konfig-kolom'", false);
    }

    public function test_index_jasa_hanya_menampilkan_tipe_jasa(): void
    {
        $this->login();
        $this->makeBarang();
        $this->makeJasa();

        $this->get(route('jasa.index'))
            ->assertOk()
            ->assertSee('Jasa Instalasi')
            ->assertDontSee('Tepung Terigu');
    }

    public function test_index_barang_dapat_difilter_kategori_satuan_dan_stok(): void
    {
        $this->login();
        $this->makeBarang();
        $this->makeBarang(['kode' => 'BRG-9002', 'nama' => 'Minyak Goreng', 'satuan' => 'liter', 'kategori' => 'Sembako', 'stok' => 1]);

        $this->get(route('barang.index', ['kategori' => 'Sembako', 'satuan' => 'liter']))
            ->assertOk()
            ->assertSee('Minyak Goreng')
            ->assertDontSee('Tepung Terigu');

        $this->get(route('barang.index', ['stok' => 'rendah']))
            ->assertOk()
            ->assertSee('Minyak Goreng');
    }

    public function test_store_barang_membuat_kode_brg_dan_tipe_barang(): void
    {
        $this->login();

        $this->post(route('barang.store'), [
            'nama' => 'Kaos Polos',
            'satuan' => 'pcs',
            'kategori' => 'Kain',
            'harga_avg' => 15000,
            'harga_beli' => 15000,
            'harga_jual' => 25000,
            'stok' => 5,
            'min_stok' => 1,
            'barcode' => '8991234567890',
            'is_aktif' => '1',
        ])->assertSessionHasNoErrors()->assertRedirect(route('barang.index'));

        $barang = Barang::where('nama', 'Kaos Polos')->first();
        $this->assertNotNull($barang);
        $this->assertSame('barang', $barang->tipe);
        $this->assertStringStartsWith('BRG-', $barang->kode);
        $this->assertSame('8991234567890', $barang->barcode);
        $this->assertEqualsWithDelta(5, (float) $barang->stok, 0.01);
    }

    public function test_store_barang_membuat_saldo_awal_persediaan(): void
    {
        $this->login();

        $this->post(route('barang.store'), [
            'nama' => 'Beras Premium',
            'satuan' => 'karung',
            'harga_avg' => 12000,
            'harga_beli' => 12000,
            'harga_jual' => 15000,
            'stok' => 10,
            'min_stok' => 1,
            'is_aktif' => '1',
        ])->assertSessionHasNoErrors();

        $barang = Barang::where('nama', 'Beras Premium')->first();
        $this->assertSame(1, BbPersediaan::where('barang_id', $barang->id)->count());
    }

    public function test_foto_barang_dapat_diunggah(): void
    {
        Storage::fake('public');
        $this->login();

        $this->post(route('barang.store'), [
            'nama' => 'Gula Pasir',
            'satuan' => 'kg',
            'harga_avg' => 13000,
            'harga_beli' => 13000,
            'harga_jual' => 16000,
            'stok' => 0,
            'min_stok' => 1,
            'foto' => UploadedFile::fake()->image('foto.jpg'),
            'is_aktif' => '1',
        ])->assertSessionHasNoErrors();

        $barang = Barang::where('nama', 'Gula Pasir')->first();
        $this->assertNotNull($barang);
        $this->assertNotNull($barang->foto);
        Storage::disk('public')->assertExists($barang->foto);
    }

    public function test_barang_dapat_dinonaktifkan(): void
    {
        $this->login();
        $barang = $this->makeBarang();

        $this->put(route('barang.update', $barang), [
            'nama' => $barang->nama,
            'satuan' => $barang->satuan,
            'kategori' => $barang->kategori,
            'harga_jual' => $barang->harga_jual,
            'harga_beli' => $barang->harga_beli,
            'harga_avg' => $barang->harga_avg,
            'stok' => $barang->stok,
            'min_stok' => $barang->min_stok,
            'keterangan' => '',
            'is_aktif' => '0',
        ])->assertSessionHasNoErrors();

        $this->assertFalse(Barang::find($barang->id)->is_aktif);
    }

    public function test_jasa_dapat_dinonaktifkan(): void
    {
        $this->login();
        $jasa = $this->makeJasa();

        $this->put(route('jasa.update', $jasa), [
            'nama' => $jasa->nama,
            'satuan' => $jasa->satuan,
            'kategori' => $jasa->kategori,
            'harga_jual' => $jasa->harga_jual,
            'keterangan' => '',
            'is_aktif' => '0',
        ])->assertSessionHasNoErrors();

        $this->assertFalse(Barang::find($jasa->id)->is_aktif);
    }

    public function test_edit_barang_menolak_jasa(): void
    {
        $this->login();
        $barang = $this->makeBarang();
        $jasa = $this->makeJasa();

        $this->get(route('barang.edit', $barang))->assertOk();
        $this->get(route('barang.edit', $jasa))->assertNotFound();
    }

    public function test_edit_jasa_menolak_barang(): void
    {
        $this->login();
        $barang = $this->makeBarang();
        $jasa = $this->makeJasa();

        $this->get(route('jasa.edit', $jasa))->assertOk();
        $this->get(route('jasa.edit', $barang))->assertNotFound();
    }

    private function buatHargaUmum(Barang $barang): void
    {
        DaftarHarga::create([
            'entitas' => 'supplier',
            'supplier_id' => Supplier::umum()->id,
            'customer_id' => null,
            'barang_id' => $barang->id,
            'harga' => 8000,
            'is_aktif' => true,
            'min_qty' => 1,
            'max_qty' => null,
            'tanggal_mulai' => now()->toDateString(),
        ]);

        DaftarHarga::create([
            'entitas' => 'customer',
            'supplier_id' => null,
            'customer_id' => Customer::umum()->id,
            'barang_id' => $barang->id,
            'harga' => 10000,
            'is_aktif' => true,
            'min_qty' => 1,
            'max_qty' => null,
            'tanggal_mulai' => now()->toDateString(),
        ]);
    }

    public function test_barang_tidak_dipakai_dengan_daftar_harga_dapat_dihapus_permanen(): void
    {
        $this->login();
        $barang = $this->makeBarang(['stok' => 0]);
        $this->buatHargaUmum($barang);

        $this->delete(route('barang.destroy', $barang))
            ->assertRedirect(route('barang.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('barang', ['id' => $barang->id]);
        $this->assertSame(0, DaftarHarga::query()->withoutGlobalScopes()->where('barang_id', $barang->id)->count());
        $this->assertSame(0, StokGudang::query()->where('barang_id', $barang->id)->count());
    }

    public function test_barang_yang_sudah_dipakai_diarsipkan_bukan_dihapus(): void
    {
        $this->login();
        $barang = $this->makeBarang(['stok' => 10]);
        $this->buatHargaUmum($barang);

        BbPersediaan::create([
            'barang_id' => $barang->id,
            'ref_type' => null,
            'ref_id' => null,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Saldo Awal',
            'masuk_qty' => 10,
            'masuk_harga' => 8000,
            'keluar_qty' => 0,
            'keluar_harga' => 0,
            'saldo_qty' => 10,
            'saldo_harga' => 80000,
            'ratt' => 8000,
        ]);

        $this->delete(route('barang.destroy', $barang))
            ->assertRedirect(route('barang.index'))
            ->assertSessionHas('success');

        $this->assertNotNull(Barang::find($barang->id));
        $this->assertFalse(Barang::find($barang->id)->is_aktif);
        $this->assertSame(1, BbPersediaan::where('barang_id', $barang->id)->count());
        $this->assertSame(0, DaftarHarga::query()->where('barang_id', $barang->id)->where('is_aktif', true)->count());
    }

    public function test_barang_diarsipkan_tidak_memunculkan_daftar_harga_kembali_saat_diupdate(): void
    {
        $this->login();
        $barang = $this->makeBarang(['stok' => 5]);
        $this->buatHargaUmum($barang);

        BbPersediaan::create([
            'barang_id' => $barang->id,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Saldo Awal',
            'masuk_qty' => 5,
            'masuk_harga' => 8000,
            'keluar_qty' => 0,
            'keluar_harga' => 0,
            'saldo_qty' => 5,
            'saldo_harga' => 40000,
            'ratt' => 8000,
        ]);

        $this->delete(route('barang.destroy', $barang))->assertSessionHas('success');
        $this->assertSame(0, DaftarHarga::query()->where('barang_id', $barang->id)->where('is_aktif', true)->count());

        $this->put(route('barang.update', $barang), [
            'nama' => $barang->nama,
            'satuan' => $barang->satuan,
            'kategori' => $barang->kategori,
            'harga_jual' => 11000,
            'harga_beli' => 8500,
            'stok' => 5,
            'min_stok' => 1,
            'keterangan' => '',
            'is_aktif' => '0',
        ])->assertSessionHasNoErrors();

        $this->assertSame(0, DaftarHarga::query()->where('barang_id', $barang->id)->where('is_aktif', true)->count());
    }

    public function test_jasa_tidak_dipakai_dengan_daftar_harga_dapat_dihapus_permanen(): void
    {
        $this->login();
        $jasa = $this->makeJasa();

        DaftarHarga::create([
            'entitas' => 'customer',
            'supplier_id' => null,
            'customer_id' => Customer::umum()->id,
            'barang_id' => $jasa->id,
            'harga' => 150000,
            'is_aktif' => true,
            'min_qty' => 1,
            'max_qty' => null,
            'tanggal_mulai' => now()->toDateString(),
        ]);

        $this->delete(route('jasa.destroy', $jasa))
            ->assertRedirect(route('jasa.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('barang', ['id' => $jasa->id]);
        $this->assertSame(0, DaftarHarga::query()->withoutGlobalScopes()->where('barang_id', $jasa->id)->count());
    }

    public function test_data_produk_destroy_mengarsipkan_saat_produk_terpakai(): void
    {
        $this->login();
        $barang = $this->makeBarang(['stok' => 3]);

        BbPersediaan::create([
            'barang_id' => $barang->id,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Saldo Awal',
            'masuk_qty' => 3,
            'masuk_harga' => 8000,
            'keluar_qty' => 0,
            'keluar_harga' => 0,
            'saldo_qty' => 3,
            'saldo_harga' => 24000,
            'ratt' => 8000,
        ]);

        $this->delete(route('data-produk.destroy', $barang))
            ->assertRedirect(route('data-produk.index', ['tipe' => 'barang']))
            ->assertSessionHas('success');

        $this->assertNotNull(Barang::find($barang->id));
        $this->assertFalse(Barang::find($barang->id)->is_aktif);
    }

    public function test_tambah_satuan_melalui_endpoint(): void
    {
        $this->login();

        $this->postJson(route('barang.tambah-satuan'), ['nama' => 'liter'])
            ->assertOk()
            ->assertJson(['nama' => 'liter']);

        $this->assertDatabaseHas('satuan', ['nama' => 'liter']);
    }

    public function test_tambah_satuan_duplikat_tidak_ganda(): void
    {
        $this->login();

        $this->postJson(route('barang.tambah-satuan'), ['nama' => 'liter'])->assertOk();
        $this->postJson(route('barang.tambah-satuan'), ['nama' => 'liter'])->assertOk();

        $this->assertSame(1, Satuan::where('nama', 'liter')->count());
    }

    public function test_tambah_satuan_harus_valid(): void
    {
        $this->login();

        $this->postJson(route('barang.tambah-satuan'), ['nama' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors('nama');
    }

    public function test_tambah_kategori_melalui_endpoint(): void
    {
        $this->login();

        $this->postJson(route('barang.tambah-kategori'), ['nama' => 'Aksesoris'])
            ->assertOk()
            ->assertJson(['nama' => 'Aksesoris']);

        $this->assertDatabaseHas('kategori', ['nama' => 'Aksesoris']);
    }

    public function test_form_barang_memakai_url_relatif_untuk_modal_satuan_dan_kategori(): void
    {
        $this->login();

        $html = $this->get(route('barang.create'))->assertOk()->getContent();

        $this->assertStringContainsString("satuanModalForm('/barang/tambah-satuan'", $html);
        $this->assertStringContainsString("satuanModalForm('/barang/tambah-kategori'", $html);
    }

    public function test_form_jasa_memakai_url_relatif_untuk_modal_satuan_dan_kategori(): void
    {
        $this->login();

        $html = $this->get(route('jasa.create'))->assertOk()->getContent();

        $this->assertStringContainsString("satuanModalForm('/barang/tambah-satuan'", $html);
        $this->assertStringContainsString("satuanModalForm('/barang/tambah-kategori'", $html);
    }

    public function test_simpan_kolom_menyimpan_preferensi(): void
    {
        $this->login();

        $this->post(route('barang.simpan-kolom'), ['kolom' => ['kode', 'nama', 'harga_jual']])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $prefs = json_decode((string) Pengaturan::tampil('barang_kolom'), true);
        $this->assertContains('kode', $prefs);
        $this->assertContains('aksi', $prefs);
    }

    public function test_store_jasa_membuat_kode_jas(): void
    {
        $this->login();

        $this->post(route('jasa.store'), [
            'nama' => 'Jasa Konsultasi',
            'satuan' => 'paket',
            'kategori' => 'Jasa',
            'harga_jual' => 200000,
            'is_aktif' => '1',
        ])->assertSessionHasNoErrors()->assertRedirect(route('jasa.index'));

        $jasa = Barang::where('nama', 'Jasa Konsultasi')->first();
        $this->assertNotNull($jasa);
        $this->assertSame('jasa', $jasa->tipe);
        $this->assertStringStartsWith('JAS-', $jasa->kode);
        $this->assertEqualsWithDelta(0, (float) $jasa->stok, 0.01);
    }

    public function test_form_barang_menampilkan_satuan_dan_kategori_dari_master(): void
    {
        $this->login();
        Satuan::create(['nama' => 'liter']);
        Kategori::create(['nama' => 'Aksesoris']);

        $this->get(route('barang.create'))
            ->assertOk()
            ->assertSee('liter')
            ->assertSee('Aksesoris');
    }
}
