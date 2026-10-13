<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TenantDatabase;
use App\Services\TenantTables;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BackfillTenantDatabaseTest extends TestCase
{
    use RefreshDatabase;

    private string $basePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->basePath = storage_path('framework/testing/backfill-'.uniqid());

        config()->set('tenant.enabled', true);
        config()->set('tenant.path', $this->basePath);
    }

    protected function tearDown(): void
    {
        // connect()/provision() menandai pemilik aktif secara statis; tanpa reset
        // state itu bocor ke test lain (mis. NomorGenerator memfilter user_id).
        TenantDatabase::disconnect();
        DB::setDefaultConnection('mysql');
        DB::purge('tenant');

        if (File::isDirectory($this->basePath)) {
            File::deleteDirectory($this->basePath);
        }

        parent::tearDown();
    }

    public function test_daftar_tabel_tenant_mencakup_model_berisik_user(): void
    {
        $tables = TenantTables::all();

        $this->assertContains('akun_perkiraan', $tables);
        $this->assertContains('pengaturan', $tables);
    }

    public function test_daftar_tabel_tenant_tidak_mencakup_model_yang_dikunci_ke_mysql(): void
    {
        $tables = TenantTables::all();

        $this->assertNotContains('donasis', $tables);
        $this->assertNotContains('users', $tables);
        $this->assertNotContains('user_databases', $tables);
        $this->assertNotContains('chat_rooms', $tables);
    }

    public function test_backfill_memindahkan_data_lama_ke_file_masing_masing_user(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $this->insertPengaturanLama($userA->id, 'nama_usaha', 'Toko A');
        $this->insertPengaturanLama($userA->id, 'npwp', '11.111.111.1-111.111');
        $this->insertPengaturanLama($userB->id, 'nama_usaha', 'Toko B');

        $this->artisan('akun:backfill-tenant-db')->assertSuccessful();

        TenantDatabase::connect($userA);
        $this->assertSame('Toko A', DB::table('pengaturan')->where('key', 'nama_usaha')->value('value'));
        $this->assertSame(2, $this->jumlahPengaturanTanpaMarker());

        TenantDatabase::connect($userB);
        $this->assertSame('Toko B', DB::table('pengaturan')->where('key', 'nama_usaha')->value('value'));
        $this->assertSame(1, $this->jumlahPengaturanTanpaMarker());
    }

    public function test_backfill_menyimpan_marker_kepemilikan_dan_dianggap_utuh(): void
    {
        $user = User::factory()->create();
        $this->insertPengaturanLama($user->id, 'nama_usaha', 'Toko A');

        $this->artisan('akun:backfill-tenant-db')->assertSuccessful();

        TenantDatabase::connect($user);

        $this->assertSame(
            (string) $user->id,
            DB::table('pengaturan')->where('key', TenantDatabase::OWNER_MARKER_KEY)->value('value')
        );

        $this->assertTrue(TenantDatabase::hasDatabase($user));
    }

    public function test_backfill_mempertahankan_id_baris_lama(): void
    {
        $user = User::factory()->create();
        $userId = $user->id;

        DB::connection('mysql')->table('pengaturan')->insert([
            'id' => 42,
            'user_id' => $userId,
            'key' => 'nama_usaha',
            'value' => 'Toko A',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('akun:backfill-tenant-db')->assertSuccessful();

        TenantDatabase::connect($user);

        $this->assertSame('Toko A', DB::table('pengaturan')->where('id', 42)->value('value'));
    }

    public function test_backfill_melewati_user_yang_sudah_terisi_tanpa_force(): void
    {
        $user = User::factory()->create();
        $this->insertPengaturanLama($user->id, 'nama_usaha', 'Toko A');

        $this->artisan('akun:backfill-tenant-db')->assertSuccessful();

        $this->artisan('akun:backfill-tenant-db')
            ->expectsOutputToContain('[dilewati]')
            ->assertSuccessful();

        TenantDatabase::connect($user);
        $this->assertSame(1, $this->jumlahPengaturanTanpaMarker());
        $this->assertSame('Toko A', DB::table('pengaturan')->where('key', 'nama_usaha')->value('value'));
    }

    public function test_backfill_force_menimpa_dan_marker_tetap_ada(): void
    {
        $user = User::factory()->create();
        $userId = $user->id;
        $this->insertPengaturanLama($userId, 'nama_usaha', 'Toko A');

        $this->artisan('akun:backfill-tenant-db')->assertSuccessful();

        DB::connection('mysql')->table('pengaturan')
            ->where('user_id', $userId)
            ->where('key', 'nama_usaha')
            ->update(['value' => 'Toko A v2']);

        $this->artisan('akun:backfill-tenant-db --force')->assertSuccessful();

        TenantDatabase::connect($user);

        $this->assertSame('Toko A v2', DB::table('pengaturan')->where('key', 'nama_usaha')->value('value'));
        $this->assertSame(1, $this->jumlahPengaturanTanpaMarker());
        $this->assertSame(
            (string) $userId,
            DB::table('pengaturan')->where('key', TenantDatabase::OWNER_MARKER_KEY)->value('value')
        );
        $this->assertTrue(TenantDatabase::hasDatabase($user));
    }

    public function test_backfill_tidak_memindahkan_baris_yatim(): void
    {
        $user = User::factory()->create();
        $this->insertPengaturanLama($user->id, 'nama_usaha', 'Toko A');
        $this->insertPengaturanLama(null, 'nama_usaha', 'Tanpa User');
        $this->insertPengaturanLama(999999, 'nama_usaha', 'User Hilang');

        $this->artisan('akun:backfill-tenant-db')->assertSuccessful();

        TenantDatabase::connect($user);

        $this->assertSame(['Toko A'], $this->nilaiPengaturanTanpaMarker());
    }

    public function test_dry_run_tidak_menulis_database_tenant(): void
    {
        $user = User::factory()->create();
        $this->insertPengaturanLama($user->id, 'nama_usaha', 'Toko A');

        $this->artisan('akun:backfill-tenant-db --dry-run')->assertSuccessful();

        $this->assertFileDoesNotExist(TenantDatabase::pathFor($user));
    }

    public function test_perintah_purge_menolak_tanpa_force(): void
    {
        $this->artisan('akun:bersihkan-tabel-tenant')->assertFailed();

        $this->assertTrue(Schema::connection('mysql')->hasTable('akun_perkiraan'));
    }

    public function test_perintah_purge_menolak_saat_user_yang_punya_data_belum_punya_database_tenant(): void
    {
        $user = User::factory()->create();
        $this->insertPengaturanLama($user->id, 'nama_usaha', 'Toko A');

        $this->artisan('akun:bersihkan-tabel-tenant --force')
            ->expectsOutputToContain('tidak punya database tenant')
            ->assertFailed();

        $this->assertTrue(Schema::connection('mysql')->hasTable('pengaturan'));
    }

    public function test_perintah_purge_menghapus_tabel_tenant_setelah_data_terverifikasi_pindah(): void
    {
        $user = User::factory()->create();
        $this->insertPengaturanLama($user->id, 'nama_usaha', 'Toko A');
        $this->artisan('akun:backfill-tenant-db')->assertSuccessful();

        $this->artisan('akun:bersihkan-tabel-tenant --force')->assertSuccessful();

        $this->assertFalse(Schema::connection('mysql')->hasTable('akun_perkiraan'));
        $this->assertFalse(Schema::connection('mysql')->hasTable('pengaturan'));
        $this->assertTrue(Schema::connection('mysql')->hasTable('users'));

        TenantDatabase::connect($user);
        $this->assertSame('Toko A', DB::table('pengaturan')->where('key', 'nama_usaha')->value('value'));
    }

    private function insertPengaturanLama(?int $userId, string $key, string $value): void
    {
        DB::connection('mysql')->table('pengaturan')->insert([
            'user_id' => $userId,
            'key' => $key,
            'value' => $value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Jumlah baris pengaturan tenant tanpa baris marker kepemilikan.
     */
    private function jumlahPengaturanTanpaMarker(): int
    {
        return DB::table('pengaturan')
            ->where('key', '!=', TenantDatabase::OWNER_MARKER_KEY)
            ->count();
    }

    /**
     * @return array<int, string|null>
     */
    private function nilaiPengaturanTanpaMarker(): array
    {
        return DB::table('pengaturan')
            ->where('key', '!=', TenantDatabase::OWNER_MARKER_KEY)
            ->pluck('value')
            ->all();
    }
}
