<?php

namespace Tests\Feature;

use App\Http\Middleware\ResolveTenantDatabase;
use App\Models\AkunPerkiraan;
use App\Models\AuditTrail;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\JurnalUmum;
use App\Models\Pembelian;
use App\Models\Pengaturan;
use App\Models\Penjualan;
use App\Models\Supplier;
use App\Models\User;
use App\Services\ProvisionTenant;
use App\Services\ReportService;
use App\Services\TenantDatabase;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Menjalankan alur nyata aplikasi dengan database per-user aktif
 * (TENANT_DB_ENABLED=true), berbeda dari suite utama yang memakai satu database.
 *
 * Tujuannya membuktikan user baru bisa daftar, masuk, memakai sistem, dan tidak
 * pernah melihat data user lain — dalam mode yang benar-benar dipakai produksi.
 */
class TenantModeAlurPenggunaTest extends TestCase
{
    use RefreshDatabase;

    private string $basePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->basePath = storage_path('framework/testing/tenant-alur-'.uniqid());

        config()->set('tenant.enabled', true);
        config()->set('tenant.path', $this->basePath);
    }

    protected function tearDown(): void
    {
        DB::setDefaultConnection('mysql');
        DB::purge('tenant');

        if (File::isDirectory($this->basePath)) {
            File::deleteDirectory($this->basePath);
        }

        parent::tearDown();
    }

    public function test_pendaftaran_membuat_database_tenant_lengkap_dan_tanpa_data_yatim(): void
    {
        $response = $this->post('/register', [
            'name' => 'Pemilik Baru',
            'email' => 'baru@tenant.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'baru@tenant.test')->firstOrFail();

        $this->assertTrue(TenantDatabase::hasDatabase($user));
        $this->assertFileExists(TenantDatabase::pathFor($user));

        TenantDatabase::connect($user);

        try {
            $this->assertTrue(Schema::hasTable('akun_perkiraan'));
            $this->assertTrue(AkunPerkiraan::where('kode', '111')->exists(), 'chart of accounts harus ter-seed');

            // Kunci unik per user harus berlaku: tidak boleh ada duplikat kode.
            $kolomKunci = [
                'akun_perkiraan' => 'kode',
                'customers' => 'kode',
                'suppliers' => 'kode',
                'gudangs' => 'kode',
                'asset_templates' => 'nama_kategori',
            ];

            foreach ($kolomKunci as $table => $kolom) {
                $yatim = DB::table($table)->whereNull('user_id')->count();

                $this->assertSame(0, $yatim, "{$table} tidak boleh punya baris tanpa user_id");

                $dobel = DB::table($table)
                    ->whereNotNull('user_id')
                    ->select('user_id', $kolom)
                    ->groupBy('user_id', $kolom)
                    ->havingRaw('COUNT(*) > 1')
                    ->count();

                $this->assertSame(0, $dobel, "{$table} tidak boleh punya {$kolom} dobel untuk user yang sama");
            }
        } finally {
            TenantDatabase::disconnect();
        }
    }

    public function test_login_membuka_dashboard_dengan_data_tenant_masing_masing(): void
    {
        [$userA, $userB] = $this->buatDuaUserDenganUsahaBeda();

        $this->post('/login', ['email' => $userA->email, 'password' => 'password']);
        $this->assertAuthenticatedAs($userA);
        $this->get(route('dashboard'))->assertOk();

        $this->post('/logout');
        $this->post('/login', ['email' => $userB->email, 'password' => 'password']);
        $this->assertAuthenticatedAs($userB);
        $this->get(route('dashboard'))->assertOk();

        $this->post('/logout');
    }

    public function test_user_hanya_melihat_data_miliknya_sendiri(): void
    {
        [$userA, $userB] = $this->buatDuaUserDenganUsahaBeda();

        $this->actingAs($userA)->get(route('pengaturan.index'))->assertOk();

        // Middleway mengembalikan koneksi ke shared setelah request selesai.
        TenantDatabase::connect($userA);
        $this->assertSame('Usaha A', Pengaturan::tampil('nama_usaha'));
        TenantDatabase::disconnect();

        $this->post('/logout');

        $this->actingAs($userB)->get(route('pengaturan.index'))->assertOk();

        TenantDatabase::connect($userB);
        $this->assertSame('Usaha B', Pengaturan::tampil('nama_usaha'));
        TenantDatabase::disconnect();
    }

    public function test_transaksi_tertulis_di_database_user_yang_benar(): void
    {
        [$userA, $userB] = $this->buatDuaUserDenganUsahaBeda();

        TenantDatabase::connect($userA);
        $barangA = $this->buatBarang('Barang A', 25_000);

        TenantDatabase::connect($userB);
        $barangB = $this->buatBarang('Barang B', 90_000);
        $jumlahB = Barang::count();

        // User B tidak boleh melihat barang milik user A. Dicocokkan lewat kode,
        // bukan id: tiap file SQLite punya penomoran sendiri sehingga id sama
        // bisa terjadi pada dua user berbeda.
        $this->assertSame(1, $jumlahB);
        $this->assertNull(Barang::where('kode', $barangA->kode)->first());
        $this->assertNotNull(Barang::where('kode', $barangB->kode)->first());

        TenantDatabase::disconnect();

        // Setelah pindah user, data user A utuh di file-nya.
        TenantDatabase::connect($userA);
        $this->assertNotNull(Barang::where('kode', $barangA->kode)->first());
        $this->assertNull(Barang::where('kode', $barangB->kode)->first());
        TenantDatabase::disconnect();
    }

    public function test_penjualan_tercatat_dengan_jurnal_balance_di_database_user(): void
    {
        [$userA] = $this->buatDuaUserDenganUsahaBeda();

        $this->actingAs($userA);

        TenantDatabase::connect($userA);
        $barang = $this->buatBarang('Barang Jurnal', 40_000);
        $customer = Customer::create(['kode' => 'C-JURNAL', 'nama' => 'Pelanggan Jurnal', 'is_aktif' => true]);
        TenantDatabase::disconnect();

        $response = $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $customer->id,
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'items' => [
                ['barang_id' => $barang->id, 'jumlah' => 3, 'harga_satuan' => 40_000, 'diskon' => 0],
            ],
        ]);

        $this->assertNull(session('error'), 'Transaksi gagal: '.(string) session('error'));
        $this->assertSame([], session()->get('errors')?->toArray() ?? []);

        // Middleware sudah mengembalikan koneksi ke database shared setelah
        // request selesai, jadi sambungkan lagi untuk memeriksa hasil tenant.
        TenantDatabase::connect($userA);

        $this->assertSame(1, Penjualan::count());
        $this->assertGreaterThan(0, JurnalUmum::count());

        // Setiap jurnal yang tertulis harus balance.
        foreach (JurnalUmum::with('items')->get() as $jurnal) {
            $debit = (float) $jurnal->items->sum('debit');
            $kredit = (float) $jurnal->items->sum('kredit');

            $this->assertEqualsWithDelta($debit, $kredit, 0.01, "Jurnal #{$jurnal->id} tidak balance");
        }

        // Laporan laba rugi harus bisa membaca jurnal itu.
        $labaRugi = ReportService::labaRugi(now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString());

        $this->assertIsArray($labaRugi);
        $this->assertArrayHasKey('pendapatan', $labaRugi);
        $this->assertGreaterThan(0, (float) $labaRugi['total_pendapatan']);

        $this->post('/logout');
    }

    public function test_audit_trail_tertulis_tanpa_http_tidak_gagal(): void
    {
        [$userA] = $this->buatDuaUserDenganUsahaBeda();

        TenantDatabase::connect($userA);
        Auth::logout();

        $akun = AkunPerkiraan::withoutGlobalScope('belongsToUser')->firstOrFail();

        $trail = AuditTrail::log($akun, 'updated', ['a' => 1], ['a' => 2]);

        $this->assertSame($userA->id, $trail->user_id);

        AuditTrail::withoutGlobalScope('belongsToUser')
            ->where('id', $trail->id)
            ->delete();

        TenantDatabase::disconnect();
    }

    public function test_halaman_laporan_terbaca_dan_tidak_melihat_data_user_lain(): void
    {
        [$userA, $userB] = $this->buatDuaUserDenganUsahaBeda();

        TenantDatabase::connect($userA);
        $this->buatBarang('Barang Laporan', 15_000);
        TenantDatabase::disconnect();

        $this->actingAs($userB);

        foreach (['laporan.laba-rugi', 'laporan.neraca', 'laporan.arus-kas', 'laporan.neraca-saldo'] as $rute) {
            $this->get(route($rute))->assertOk();
        }

        $this->assertSame(0, Barang::withoutGlobalScope('belongsToUser')->count(), 'user B tidak boleh punya barang');

        $this->post('/logout');
    }

    public function test_menghapus_user_menghapus_file_databasenya(): void
    {
        [$userA] = $this->buatDuaUserDenganUsahaBeda();

        $path = TenantDatabase::pathFor($userA);
        $this->assertFileExists($path);

        $userA->delete();

        $this->assertFileDoesNotExist($path);
        $this->assertDatabaseMissing('user_databases', ['user_id' => $userA->id]);
    }

    public function test_file_tenant_setengah_jadi_ditolak_dan_diperbaiki_otomatis(): void
    {
        [$userA] = $this->buatDuaUserDenganUsahaBeda();

        $path = TenantDatabase::pathFor($userA);

        DB::purge('tenant');
        config()->set('database.connections.tenant.database', $path);
        DB::connection('tenant')->statement('DROP TABLE pengaturan');
        DB::purge('tenant');

        $this->assertFalse(TenantDatabase::hasDatabase($userA), 'file tanpa tabel wajib harus ditolak');

        // ensure() harus memperbaiki sendiri lewat provision().
        TenantDatabase::ensure($userA);

        $this->assertTrue(TenantDatabase::hasDatabase($userA));
        TenantDatabase::disconnect();
    }

    public function test_detail_dan_preview_transaksi_terbuka_di_mode_tenant(): void
    {
        [$userA] = $this->buatDuaUserDenganUsahaBeda();

        $trx = $this->catatTransaksi($userA);

        // Sebelum perbaikan urutan middleware, route ini 404 karena binding
        // di-resolve pada database shared, bukan database tenant.
        $this->actingAs($userA)->get(route('penjualan.show', $trx['penjualan']))->assertOk();
        $this->actingAs($userA)->get(route('penjualan.pdf', $trx['penjualan']))->assertOk();
        $this->actingAs($userA)->get(route('pembelian.show', $trx['pembelian']))->assertOk();
        $this->actingAs($userA)->get(route('pembelian.pdf', $trx['pembelian']))->assertOk();
        $this->actingAs($userA)->get(route('jurnal.show', $trx['jurnal']))->assertOk();

        $this->post('/logout');
    }

    public function test_detail_transaksi_tidak_dapat_dilihat_user_lain(): void
    {
        [$userA, $userB] = $this->buatDuaUserDenganUsahaBeda();

        $trx = $this->catatTransaksi($userA);

        $this->actingAs($userB);

        $this->get(route('penjualan.show', $trx['penjualan']))->assertNotFound();
        $this->get(route('pembelian.show', $trx['pembelian']))->assertNotFound();

        // Data user A tidak pernah bocor ke database user B.
        TenantDatabase::connect($userB);
        $this->assertSame(0, Penjualan::count());
        $this->assertSame(0, Pembelian::count());
        TenantDatabase::disconnect();

        $this->post('/logout');
    }

    public function test_tenant_diresolusi_sebelum_route_model_binding(): void
    {
        $priority = app(Kernel::class)->getMiddlewarePriority();

        $tenantIndex = array_search(ResolveTenantDatabase::class, $priority, true);
        $bindingIndex = array_search(SubstituteBindings::class, $priority, true);

        $this->assertNotFalse($tenantIndex, 'middleware tenant-db harus ada di daftar prioritas');
        $this->assertNotFalse($bindingIndex, 'SubstituteBindings harus ada di daftar prioritas');
        $this->assertLessThan($bindingIndex, $tenantIndex, 'Koneksi tenant harus diarahkan sebelum route model binding');
    }

    /**
     * Catat penjualan + pembelian milik user melalui alur aplikasi.
     *
     * @return array{penjualan: Penjualan, pembelian: Pembelian, jurnal: JurnalUmum}
     */
    private function catatTransaksi(User $user): array
    {
        $kode = strtoupper(substr(md5((string) $user->id), 0, 6));

        TenantDatabase::connect($user);
        $barang = $this->buatBarang('Barang Transaksi', 30_000);
        $customer = Customer::create(['kode' => 'C-'.$kode, 'nama' => 'Pelanggan Transaksi', 'is_aktif' => true]);
        $supplier = Supplier::create(['kode' => 'S-'.$kode, 'nama' => 'Supplier Transaksi', 'is_aktif' => true]);
        TenantDatabase::disconnect();

        $this->actingAs($user);

        $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $customer->id,
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'items' => [
                ['barang_id' => $barang->id, 'jumlah' => 2, 'harga_satuan' => 30_000, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();

        $this->post(route('pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $supplier->id,
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'items' => [
                ['barang_id' => $barang->id, 'jumlah' => 5, 'harga_satuan' => 20_000, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();

        TenantDatabase::connect($user);

        $hasil = [
            'penjualan' => Penjualan::latest('id')->firstOrFail(),
            'pembelian' => Pembelian::latest('id')->firstOrFail(),
            'jurnal' => JurnalUmum::latest('id')->firstOrFail(),
        ];

        TenantDatabase::disconnect();

        return $hasil;
    }

    /**
     * Dua user nyata dengan file tenant masing-masing dan usaha berbeda.
     *
     * @return array{0: User, 1: User}
     */
    private function buatDuaUserDenganUsahaBeda(): array
    {
        $userA = User::factory()->create(['email' => 'a@tenant.test']);
        $userB = User::factory()->create(['email' => 'b@tenant.test']);

        ProvisionTenant::provision($userA);
        ProvisionTenant::provision($userB);

        foreach ([[$userA, 'Usaha A'], [$userB, 'Usaha B']] as [$user, $nama]) {
            TenantDatabase::connect($user);
            DB::table('pengaturan')->updateOrInsert(
                ['user_id' => $user->id, 'key' => 'nama_usaha'],
                ['value' => $nama, 'created_at' => now(), 'updated_at' => now()]
            );
            TenantDatabase::disconnect();
        }

        return [$userA, $userB];
    }

    private function buatBarang(string $nama, int $harga): Barang
    {
        return Barang::create([
            'kode' => 'B-'.strtoupper(substr(md5($nama), 0, 6)),
            'nama' => $nama,
            'satuan' => 'pcs',
            'harga_beli' => $harga,
            'harga_jual' => $harga,
            'stok' => 10,
            'is_aktif' => true,
        ]);
    }
}
