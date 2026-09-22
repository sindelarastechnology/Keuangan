<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\DaftarHarga;
use App\Models\DaftarHargaRiwayat;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DaftarHargaAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $sup = Supplier::first();
        if (! $sup) {
            $sup = Supplier::create(['kode' => 'SUP-1', 'nama' => 'Supplier Uji']);
        }
        $brg = Barang::where('tipe', 'barang')->first();
        if (! $brg) {
            $brg = Barang::create(['kode' => 'BRG-1', 'nama' => 'Barang Uji', 'tipe' => 'barang']);
        }
        if (DaftarHarga::count() === 0) {
            DaftarHarga::create([
                'supplier_id' => $sup->id,
                'barang_id' => $brg->id,
                'harga' => 5000,
                'is_aktif' => true,
                'min_qty' => 1,
                'max_qty' => null,
            ]);
        }
    }

    private function login(): User
    {
        $user = User::where('email', 'admin@keuangan.test')->first();
        $this->actingAs($user);

        return $user;
    }

    public function test_index_page()
    {
        $this->login();
        $r = $this->get(route('daftar-harga.index'));
        $r->assertStatus(200);
    }

    public function test_index_with_supplier_filter()
    {
        $this->login();
        $sup = Supplier::first();
        $r = $this->get(route('daftar-harga.index', ['supplier_id' => $sup->id]));
        $r->assertStatus(200);
    }

    public function test_index_with_history()
    {
        $this->login();
        $r = $this->get(route('daftar-harga.index', ['show_history' => 1]));
        $r->assertStatus(200);
    }

    public function test_create_page()
    {
        $this->login();
        $r = $this->get(route('daftar-harga.create'));
        $r->assertStatus(200);
    }

    public function test_store_tiered()
    {
        $this->login();
        $sup = Supplier::first();
        $brg = Barang::where('tipe', 'barang')->first();

        DaftarHarga::where('supplier_id', $sup->id)->where('barang_id', $brg->id)->delete();

        $r = $this->post(route('daftar-harga.store'), [
            'entitas' => 'supplier',
            'supplier_id' => $sup->id,
            'barang_id' => $brg->id,
            'tiers' => [
                ['min_qty' => 1, 'max_qty' => 10, 'harga' => 5000, 'keterangan' => 'Eceran'],
                ['min_qty' => 11, 'max_qty' => 100, 'harga' => 4500, 'keterangan' => 'Partai'],
            ],
        ]);
        $r->assertSessionHasNoErrors();
        $r->assertRedirect(route('daftar-harga.index', ['entitas' => 'supplier']));

        $count = DaftarHarga::where('supplier_id', $sup->id)
            ->where('barang_id', $brg->id)
            ->where('min_qty', 1)->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_store_overlap_rejected()
    {
        $this->login();
        $sup = Supplier::first();
        $brg = Barang::where('tipe', 'barang')->first();

        // First insert
        $this->post(route('daftar-harga.store'), [
            'entitas' => 'supplier',
            'supplier_id' => $sup->id,
            'barang_id' => $brg->id,
            'tiers' => [
                ['min_qty' => 1, 'max_qty' => 20, 'harga' => 5000],
            ],
        ]);

        // Overlapping insert should fail validation
        $r = $this->post(route('daftar-harga.store'), [
            'entitas' => 'supplier',
            'supplier_id' => $sup->id,
            'barang_id' => $brg->id,
            'tiers' => [
                ['min_qty' => 10, 'max_qty' => 30, 'harga' => 4000],
            ],
        ]);
        $r->assertSessionHasErrors('tiers');
    }

    public function test_edit_page()
    {
        $this->login();
        $dh = DaftarHarga::first();
        $r = $this->get(route('daftar-harga.edit', $dh));
        $r->assertStatus(200);
    }

    public function test_update_tiered()
    {
        $this->login();
        $dh = DaftarHarga::first();

        $r = $this->put(route('daftar-harga.update', $dh), [
            'entitas' => 'supplier',
            'supplier_id' => $dh->supplier_id,
            'barang_id' => $dh->barang_id,
            'tiers' => [
                ['id' => $dh->id, 'min_qty' => 1, 'max_qty' => 50, 'harga' => 3800, 'keterangan' => 'Tier 1'],
                ['min_qty' => 51, 'max_qty' => 200, 'harga' => 3000, 'keterangan' => 'Tier 2'],
            ],
        ]);
        $r->assertSessionHasNoErrors();
        $r->assertRedirect(route('daftar-harga.index', ['entitas' => 'supplier']));

        $this->assertDatabaseHas('daftar_harga', ['id' => $dh->id, 'harga' => 3800]);
    }

    public function test_riwayat_page_initial()
    {
        $this->login();
        $r = $this->get(route('daftar-harga.riwayat'));
        $r->assertStatus(200);
    }

    public function test_riwayat_page_filtered()
    {
        $this->login();
        $dh = DaftarHarga::first();
        $r = $this->get(route('daftar-harga.riwayat', ['supplier_id' => $dh->supplier_id, 'barang_id' => $dh->barang_id]));
        $r->assertStatus(200);
    }

    public function test_destroy_soft_archives()
    {
        $this->login();
        $dh = DaftarHarga::first();
        $r = $this->delete(route('daftar-harga.destroy', $dh));
        $r->assertRedirect(route('daftar-harga.index', ['entitas' => 'supplier']));

        $this->assertDatabaseHas('daftar_harga', [
            'id' => $dh->id,
            'is_aktif' => 0,
        ]);
    }

    public function test_cari_harga()
    {
        $sup = Supplier::first();
        $brg = Barang::where('tipe', 'barang')->first();

        // Ensure clean tiers
        DaftarHarga::where('supplier_id', $sup->id)->where('barang_id', $brg->id)->delete();
        DaftarHarga::create(['entitas' => 'supplier', 'supplier_id' => $sup->id, 'barang_id' => $brg->id, 'harga' => 5000, 'min_qty' => 1, 'max_qty' => 10]);
        DaftarHarga::create(['entitas' => 'supplier', 'supplier_id' => $sup->id, 'barang_id' => $brg->id, 'harga' => 4000, 'min_qty' => 11, 'max_qty' => 100]);
        DaftarHarga::create(['entitas' => 'supplier', 'supplier_id' => $sup->id, 'barang_id' => $brg->id, 'harga' => 3000, 'min_qty' => 101, 'max_qty' => null]);

        $this->assertEquals(5000, (float) DaftarHarga::cariHarga($sup->id, $brg->id, 5)->harga);
        $this->assertEquals(4000, (float) DaftarHarga::cariHarga($sup->id, $brg->id, 50)->harga);
        $this->assertEquals(3000, (float) DaftarHarga::cariHarga($sup->id, $brg->id, 500)->harga);
    }

    public function test_store_customer_tiered()
    {
        $this->login();
        $cust = Customer::create(['kode' => 'CUST-1', 'nama' => 'Customer Uji', 'is_aktif' => true]);
        $brg = Barang::where('tipe', 'barang')->first();

        $r = $this->post(route('daftar-harga.store'), [
            'entitas' => 'customer',
            'customer_id' => $cust->id,
            'barang_id' => $brg->id,
            'tiers' => [
                ['min_qty' => 1, 'max_qty' => 10, 'harga' => 8000, 'keterangan' => 'Eceran'],
                ['min_qty' => 11, 'max_qty' => null, 'harga' => 7500, 'keterangan' => 'Partai'],
            ],
        ]);
        $r->assertSessionHasNoErrors();
        $r->assertRedirect(route('daftar-harga.index', ['entitas' => 'customer']));

        $row = DaftarHarga::where('entitas', 'customer')->where('customer_id', $cust->id)->where('min_qty', 1)->first();
        $this->assertNotNull($row);
        $this->assertEquals(8000, (float) $row->harga);
        $this->assertNull($row->supplier_id);
    }

    public function test_cari_jual()
    {
        $cust = Customer::create(['kode' => 'CUST-2', 'nama' => 'Customer Jual', 'is_aktif' => true]);
        $brg = Barang::where('tipe', 'barang')->first();

        DaftarHarga::where('entitas', 'customer')->where('customer_id', $cust->id)->delete();
        DaftarHarga::create(['entitas' => 'customer', 'customer_id' => $cust->id, 'barang_id' => $brg->id, 'harga' => 9000, 'min_qty' => 1, 'max_qty' => 20]);
        DaftarHarga::create(['entitas' => 'customer', 'customer_id' => $cust->id, 'barang_id' => $brg->id, 'harga' => 8000, 'min_qty' => 21, 'max_qty' => null]);

        $this->assertEquals(9000, (float) DaftarHarga::cariJual($cust->id, $brg->id, 5)->harga);
        $this->assertEquals(8000, (float) DaftarHarga::cariJual($cust->id, $brg->id, 50)->harga);
        $this->assertNull(DaftarHarga::cariJual(999999, $brg->id, 5));
    }

    public function test_customer_overlap_scoped_per_rekanan()
    {
        $this->login();
        $cust1 = Customer::create(['kode' => 'CUST-3', 'nama' => 'Customer 1', 'is_aktif' => true]);
        $cust2 = Customer::create(['kode' => 'CUST-4', 'nama' => 'Customer 2', 'is_aktif' => true]);
        $brg = Barang::where('tipe', 'barang')->first();

        // Insert for customer 1
        $this->post(route('daftar-harga.store'), [
            'entitas' => 'customer',
            'customer_id' => $cust1->id,
            'barang_id' => $brg->id,
            'tiers' => [['min_qty' => 1, 'max_qty' => 20, 'harga' => 8000]],
        ]);

        // Same range for customer 2 should NOT overlap (scoped per customer)
        $r = $this->post(route('daftar-harga.store'), [
            'entitas' => 'customer',
            'customer_id' => $cust2->id,
            'barang_id' => $brg->id,
            'tiers' => [['min_qty' => 1, 'max_qty' => 20, 'harga' => 7000]],
        ]);
        $r->assertSessionHasNoErrors();

        // Same range AGAIN for customer 1 SHOULD overlap
        $r2 = $this->post(route('daftar-harga.store'), [
            'entitas' => 'customer',
            'customer_id' => $cust1->id,
            'barang_id' => $brg->id,
            'tiers' => [['min_qty' => 10, 'max_qty' => 30, 'harga' => 6000]],
        ]);
        $r2->assertSessionHasErrors('tiers');
    }

    public function test_index_with_customer_filter()
    {
        $this->login();
        $cust = Customer::create(['kode' => 'CUST-5', 'nama' => 'Customer Filter', 'is_aktif' => true]);
        $brg = Barang::where('tipe', 'barang')->first();
        $this->post(route('daftar-harga.store'), [
            'entitas' => 'customer',
            'customer_id' => $cust->id,
            'barang_id' => $brg->id,
            'tiers' => [['min_qty' => 1, 'max_qty' => null, 'harga' => 9500]],
        ]);

        $r = $this->get(route('daftar-harga.index', ['entitas' => 'customer', 'customer_id' => $cust->id]));
        $r->assertStatus(200);
    }

    public function test_riwayat_customer_filtered()
    {
        $this->login();
        $cust = Customer::create(['kode' => 'CUST-6', 'nama' => 'Customer Riwayat', 'is_aktif' => true]);
        $brg = Barang::where('tipe', 'barang')->first();
        $this->post(route('daftar-harga.store'), [
            'entitas' => 'customer',
            'customer_id' => $cust->id,
            'barang_id' => $brg->id,
            'tiers' => [['min_qty' => 1, 'max_qty' => null, 'harga' => 7000]],
        ]);

        $r = $this->get(route('daftar-harga.riwayat', ['entitas' => 'customer', 'customer_id' => $cust->id, 'barang_id' => $brg->id]));
        $r->assertStatus(200);
    }

    public function test_store_records_riwayat_buat()
    {
        $this->login();
        $sup = Supplier::first();
        $brg = Barang::where('tipe', 'barang')->first();
        DaftarHarga::where('supplier_id', $sup->id)->where('barang_id', $brg->id)->delete();

        $this->post(route('daftar-harga.store'), [
            'entitas' => 'supplier',
            'supplier_id' => $sup->id,
            'barang_id' => $brg->id,
            'tiers' => [
                ['min_qty' => 1, 'max_qty' => null, 'harga' => 5000],
            ],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('daftar_harga_riwayat', [
            'entitas' => 'supplier',
            'supplier_id' => $sup->id,
            'barang_id' => $brg->id,
            'tipe' => DaftarHargaRiwayat::TIPE_BUAT,
            'harga_lama' => null,
            'harga_baru' => 5000,
            'min_qty_baru' => 1,
            'max_qty_baru' => null,
        ]);
    }

    public function test_update_records_riwayat_ubah()
    {
        $this->login();
        $dh = DaftarHarga::first();
        $this->assertEquals(5000, (float) $dh->harga);

        $this->put(route('daftar-harga.update', $dh), [
            'entitas' => 'supplier',
            'supplier_id' => $dh->supplier_id,
            'barang_id' => $dh->barang_id,
            'tiers' => [
                ['id' => $dh->id, 'min_qty' => 1, 'max_qty' => null, 'harga' => 3800],
            ],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('daftar_harga_riwayat', [
            'entitas' => 'supplier',
            'supplier_id' => $dh->supplier_id,
            'barang_id' => $dh->barang_id,
            'tipe' => DaftarHargaRiwayat::TIPE_UBAH,
            'harga_lama' => 5000,
            'harga_baru' => 3800,
        ]);
    }

    public function test_update_records_riwayat_nonaktif_for_removed_tier()
    {
        $this->login();
        $sup = Supplier::first();
        $brg = Barang::where('tipe', 'barang')->first();
        DaftarHarga::where('supplier_id', $sup->id)->where('barang_id', $brg->id)->delete();

        $this->post(route('daftar-harga.store'), [
            'entitas' => 'supplier',
            'supplier_id' => $sup->id,
            'barang_id' => $brg->id,
            'tiers' => [
                ['min_qty' => 1, 'max_qty' => 50, 'harga' => 5000],
                ['min_qty' => 51, 'max_qty' => null, 'harga' => 4000],
            ],
        ])->assertSessionHasNoErrors();

        $tier1 = DaftarHarga::where('supplier_id', $sup->id)->where('min_qty', 1)->first();
        $tier2 = DaftarHarga::where('supplier_id', $sup->id)->where('min_qty', 51)->first();

        // Update hanya menyertakan tier1 -> tier2 diarsipkan + dicatat
        $this->put(route('daftar-harga.update', $tier1), [
            'entitas' => 'supplier',
            'supplier_id' => $sup->id,
            'barang_id' => $brg->id,
            'tiers' => [
                ['id' => $tier1->id, 'min_qty' => 1, 'max_qty' => 50, 'harga' => 5000],
            ],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('daftar_harga_riwayat', [
            'entitas' => 'supplier',
            'supplier_id' => $sup->id,
            'barang_id' => $brg->id,
            'tipe' => DaftarHargaRiwayat::TIPE_NONAKTIF,
            'harga_lama' => 4000,
            'min_qty_lama' => 51,
        ]);
        $this->assertDatabaseHas('daftar_harga', ['id' => $tier2->id, 'is_aktif' => 0]);
    }

    public function test_destroy_records_riwayat_nonaktif()
    {
        $this->login();
        $dh = DaftarHarga::first();
        $this->assertEquals(5000, (float) $dh->harga);

        $this->delete(route('daftar-harga.destroy', $dh))
            ->assertRedirect(route('daftar-harga.index', ['entitas' => 'supplier']));

        $this->assertDatabaseHas('daftar_harga_riwayat', [
            'entitas' => 'supplier',
            'supplier_id' => $dh->supplier_id,
            'barang_id' => $dh->barang_id,
            'tipe' => DaftarHargaRiwayat::TIPE_NONAKTIF,
            'harga_lama' => 5000,
        ]);
    }

    public function test_update_allows_editing_full_tier_set_without_false_overlap()
    {
        $this->login();
        $sup = Supplier::first();
        $brg = Barang::where('tipe', 'barang')->first();
        DaftarHarga::where('supplier_id', $sup->id)->where('barang_id', $brg->id)->delete();

        $this->post(route('daftar-harga.store'), [
            'entitas' => 'supplier',
            'supplier_id' => $sup->id,
            'barang_id' => $brg->id,
            'tiers' => [
                ['min_qty' => 1, 'max_qty' => 50, 'harga' => 5000],
                ['min_qty' => 51, 'max_qty' => null, 'harga' => 4000],
            ],
        ])->assertSessionHasNoErrors();

        $t1 = DaftarHarga::where('supplier_id', $sup->id)->where('min_qty', 1)->first();
        $t2 = DaftarHarga::where('supplier_id', $sup->id)->where('min_qty', 51)->first();

        // Edit keduanya bersama-sama: overlap antar baris yang sama-sama diedit TIDAK ditolak
        $r = $this->put(route('daftar-harga.update', $t1), [
            'entitas' => 'supplier',
            'supplier_id' => $sup->id,
            'barang_id' => $brg->id,
            'tiers' => [
                ['id' => $t1->id, 'min_qty' => 1, 'max_qty' => 50, 'harga' => 5500],
                ['id' => $t2->id, 'min_qty' => 51, 'max_qty' => null, 'harga' => 4500],
            ],
        ]);
        $r->assertSessionHasNoErrors();
        $this->assertDatabaseHas('daftar_harga', ['id' => $t1->id, 'harga' => 5500]);
        $this->assertDatabaseHas('daftar_harga', ['id' => $t2->id, 'harga' => 4500]);
    }
}
