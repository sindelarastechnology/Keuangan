<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\TenantDatabase;
use App\Services\TenantTables;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

#[Signature('akun:reset-total {--force : Jalankan penghapusan tanpa konfirmasi}')]
#[Description('Hapus seluruh data user, file SQLite tenant, dan tabel tenant di shared (bersih total).')]
class ResetTenantTotal extends Command
{
    public function handle(): int
    {
        if (! $this->option('force')) {
            $this->warn('Perintah ini DESTRUKTIF. Tambahkan --force untuk menjalankannya.');

            return self::FAILURE;
        }

        if (TenantDatabase::enabled()) {
            $this->info('Tenant DB enabled. Membersihkan file SQLite dan user_databases.');
        }

        $folder = rtrim((string) config('tenant.path'), '/\\');
        $fileCount = 0;

        if (File::isDirectory($folder)) {
            foreach (File::directories($folder) as $dir) {
                $files = File::allFiles($dir);
                $fileCount += count($files);
            }
        }

        $users = User::query()->count();
        $shared = TenantDatabase::SHARED_CONNECTION;
        $tablesTenant = TenantTables::existingOnShared();

        $this->info('Laporan sebelum reset:');
        $this->line('  Users: '.$users);
        $this->line('  File SQLite tenant: '.$fileCount.' ('.($fileCount ? 'akan dihapus' : 'kosong').')');
        $this->line('  Tabel tenant di shared: '.count($tablesTenant).' (akan diproses)');

        $this->newLine();
        $this->line('Hapus user satu per satu lewat Eloquent (hook deleting menghapus file SQLite + user_databases)...');

        foreach (User::orderBy('id')->get() as $user) {
            $this->line('  Menghapus user #'.$user->id.' ('.$user->email.')');
            $user->delete();
        }

        Schema::connection($shared)->disableForeignKeyConstraints();

        $berurutan = [
            'chat_reports',
            'chat_room_reads',
            'chat_messages',
            'chat_rooms',
            'donasis',
            'notifications',
            'sessions',
            'cache',
            'cache_locks',
            'jobs',
            'failed_jobs',
            'job_batches',
            'password_reset_tokens',
            'banks',
            'kas',
        ];

        foreach ($berurutan as $t) {
            if (Schema::connection($shared)->hasTable($t)) {
                DB::connection($shared)->table($t)->truncate();
            }
        }

        foreach ($tablesTenant as $t) {
            if (Schema::connection($shared)->hasTable($t)) {
                $this->line('  Truncate '.$t);
                DB::connection($shared)->table($t)->truncate();
            }
        }

        Schema::connection($shared)->enableForeignKeyConstraints();

        $this->newLine();
        $this->info('Selesai. Selanjutnya jalankan:');
        $this->line('  php artisan optimize:clear');
        $this->line('  php artisan migrate --force');
        $this->line('  php artisan db:seed --force');

        return self::SUCCESS;
    }
}
