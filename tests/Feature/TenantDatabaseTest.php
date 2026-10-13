<?php

namespace Tests\Feature;

use App\Http\Middleware\ResolveTenantDatabase;
use App\Models\User;
use App\Models\UserDatabase;
use App\Services\TenantDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TenantDatabaseTest extends TestCase
{
    use RefreshDatabase;

    private string $basePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->basePath = storage_path('framework/testing/tenant-'.uniqid());

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

    public function test_provision_membuat_file_dan_menjalankan_skema_tenant(): void
    {
        $user = User::factory()->create();

        TenantDatabase::provision($user);

        $this->assertFileExists(TenantDatabase::pathFor($user));
        $this->assertTrue(Schema::connection('tenant')->hasTable('akun_perkiraan'));
        $this->assertTrue(Schema::connection('tenant')->hasTable('jurnal_umum'));
        $this->assertTrue(TenantDatabase::hasDatabase($user));

        $mapping = UserDatabase::query()->where('user_id', $user->id)->first();

        $this->assertNotNull($mapping);
        $this->assertSame(UserDatabase::STATUS_READY, $mapping->status);
    }

    public function test_middleware_mengarahkan_koneksi_default_ke_tenant_lalu_mengembalikan(): void
    {
        $user = User::factory()->create();
        TenantDatabase::provision($user);
        TenantDatabase::disconnect();

        $this->actingAs($user);

        $seen = null;

        app(ResolveTenantDatabase::class)->handle(request(), function () use (&$seen) {
            $seen = DB::getDefaultConnection();

            return response('ok');
        });

        $this->assertSame('tenant', $seen);
        $this->assertSame('mysql', DB::getDefaultConnection());
    }

    public function test_data_tenant_terpisah_antar_user(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        TenantDatabase::provision($userA);
        TenantDatabase::connect($userA);
        DB::table('pengaturan')->insert(['user_id' => $userA->id, 'key' => 'nama_usaha', 'value' => 'Toko A', 'created_at' => now(), 'updated_at' => now()]);

        TenantDatabase::provision($userB);
        TenantDatabase::connect($userB);
        DB::table('pengaturan')->insert(['user_id' => $userB->id, 'key' => 'nama_usaha', 'value' => 'Toko B', 'created_at' => now(), 'updated_at' => now()]);

        TenantDatabase::connect($userA);
        $this->assertSame('Toko A', DB::table('pengaturan')->where('key', 'nama_usaha')->value('value'));

        TenantDatabase::connect($userB);
        $this->assertSame('Toko B', DB::table('pengaturan')->where('key', 'nama_usaha')->value('value'));
    }

    public function test_drop_menghapus_file_dan_peta_user(): void
    {
        $user = User::factory()->create();
        TenantDatabase::provision($user);
        TenantDatabase::disconnect();

        $path = TenantDatabase::pathFor($user);

        $this->assertFileExists($path);
        $this->assertTrue(TenantDatabase::hasDatabase($user));

        TenantDatabase::drop($user);

        $this->assertFileDoesNotExist($path);
        $this->assertFalse(TenantDatabase::hasDatabase($user));
        $this->assertDatabaseMissing('user_databases', ['user_id' => $user->id]);
    }

    public function test_menghapus_user_ikut_menghapus_database_tenant(): void
    {
        $user = User::factory()->create();
        TenantDatabase::provision($user);
        TenantDatabase::disconnect();

        $path = TenantDatabase::pathFor($user);

        $user->delete();

        $this->assertFileDoesNotExist($path);
    }

    public function test_drop_tidak_menghapus_file_saat_fitur_nonaktif(): void
    {
        $user = User::factory()->create();
        TenantDatabase::provision($user);
        TenantDatabase::disconnect();

        // Simulasikan test suite yang tidak memakai tenant. Path diarahkan ke
        // folder khusus test — bukan storage/app/tenant asli — tapi key-nya sama:
        // tanpa guard enabled(), hook User::deleting tetap menghapus file.
        $fakePath = $this->basePath.'-nonaktif';
        $path = $fakePath.DIRECTORY_SEPARATOR.$user->id.DIRECTORY_SEPARATOR.'app.sqlite';

        File::ensureDirectoryExists(dirname($path));
        File::put($path, 'sqlite-tenant');

        try {
            config()->set('tenant.enabled', false);
            config()->set('tenant.path', $fakePath);

            $user->delete();

            $this->assertFileExists($path, 'File database tidak boleh terhapus saat fitur tenant nonaktif.');
            $this->assertSame('sqlite-tenant', File::get($path));

            // Drop sepenuhnya no-op: peta user_databases juga tidak disentuh,
            // sehingga tidak ada efek samping dari environment non-tenant.
            $this->assertDatabaseHas('user_databases', ['user_id' => $user->id]);
        } finally {
            File::deleteDirectory($fakePath);
            File::deleteDirectory($this->basePath.DIRECTORY_SEPARATOR.$user->id);
        }
    }

    public function test_menghapus_user_ikut_menghapus_notifikasinya(): void
    {
        $user = User::factory()->create();
        $user->notify(new class extends Notification
        {
            public function via(object $notifiable): array
            {
                return ['database'];
            }

            public function toArray(object $notifiable): array
            {
                return ['judul' => 'Tes'];
            }
        });

        $this->assertSame(1, $user->notifications()->count());

        $user->delete();

        $this->assertDatabaseCount('notifications', 0);
    }
}
