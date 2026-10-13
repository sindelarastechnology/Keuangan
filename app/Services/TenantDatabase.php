<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDatabase;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Throwable;

/**
 * Mengelola database SQLite per user (tenant).
 *
 * MySQL hanya menyimpan data shared + peta user→database. Saat request
 * terautentikasi, koneksi default diarahkan ke file tenant milik user; seluruh
 * model tanpa koneksi eksplisit (seluruh model akuntansi) otomatis ikut.
 */
class TenantDatabase
{
    /**
     * Nama koneksi shared. Model shared (User, Chat, Donasi, dll) di-pin ke sini.
     */
    public const SHARED_CONNECTION = 'mysql';

    /**
     * Tabel yang wajib ada supaya file tenant dianggap utuh.
     *
     * Dipakai hasDatabase() untuk menolak file setengah jadi: mapping berstatus
     * ready saja tidak cukup bila migrasi tidak pernah selesai.
     *
     * @var array<int, string>
     */
    private const REQUIRED_TABLES = ['akun_perkiraan', 'jurnal_umum', 'pengaturan', 'barang'];

    /**
     * Key pada tabel `pengaturan` yang menyimpan user_id pemilik file tenant.
     *
     * Satu-satunya bukti kepemilikan yang tersimpan di dalam file itu sendiri.
     * Tanpa marker ini, file SQLite yang tertinggal dari user yang sudah dihapus
     * (mis. drop() dilewati karena fitur nonaktif, atau penghapusan lewat jalur
     * non-Eloquent) bisa dipakai ulang oleh user lain yang kebetulan mendapat
     * user_id sama — dan seluruh data akun sebelumnya ikut terbaca.
     */
    public const OWNER_MARKER_KEY = 'tenant_user_id';

    /**
     * Cache status "file tenant sudah utuh" per user dalam satu proses.
     *
     * hasDatabase() membuka koneksi terpisah untuk memeriksa skema; tanpa cache
     * pemeriksaan itu diulang pada setiap request terautentikasi. Diisi ulang
     * setiap kali connect()/provision()/drop() mengubah file.
     *
     * @var array<int, bool>
     */
    private static array $skemaCache = [];

    /**
     * User pemilik database tenant yang sedang aktif.
     *
     * Diisi oleh connect() dan dibersihkan disconnect(). Dipakai oleh kode di
     * luar HTTP (command/queue) yang tidak punya Auth::id() tetapi tetap harus
     * tahu pemilik data yang sedang dikerjakan.
     */
    private static ?int $activeUserId = null;

    /**
     * Kedalaman counter penonaktifan global scope tenant.
     *
     * Migrasi dan operasi raw database tidak boleh difilter user_id: kolomnya
     * belum tentu ada dan konsol tidak punya konteks pemilik.
     */
    private static int $scopeDepth = 0;

    public static function enabled(): bool
    {
        return (bool) config('tenant.enabled');
    }

    /**
     * ID user pemilik tenant aktif, atau null bila tidak ada.
     */
    public static function activeUserId(): ?int
    {
        return self::$activeUserId;
    }

    public static function connectionName(): string
    {
        return (string) config('tenant.connection', 'tenant');
    }

    /**
     * Apakah global scope tenant sedang dinonaktifkan sementara.
     */
    public static function scopeDisabled(): bool
    {
        return self::$scopeDepth > 0;
    }

    /**
     * Jalankan callback tanpa global scope tenant (nested-safe).
     *
     * @template T
     *
     * @param  callable():T  $callback
     * @return T
     */
    public static function tanpaScope(callable $callback): mixed
    {
        self::$scopeDepth++;

        try {
            return $callback();
        } finally {
            self::$scopeDepth = max(0, self::$scopeDepth - 1);
        }
    }

    /**
     * Lokasi file SQLite milik user.
     */
    public static function pathFor(User|int $user): string
    {
        $userId = $user instanceof User ? $user->getKey() : $user;

        return rtrim((string) config('tenant.path'), '/\\').DIRECTORY_SEPARATOR.$userId.DIRECTORY_SEPARATOR.'app.sqlite';
    }

    /**
     * Apakah user sudah punya file database siap pakai.
     */
    public static function hasDatabase(User|int $user): bool
    {
        if (! self::enabled()) {
            return false;
        }

        $userId = $user instanceof User ? $user->getKey() : $user;

        if (! UserDatabase::query()
            ->where('user_id', $userId)
            ->where('status', UserDatabase::STATUS_READY)
            ->exists()) {
            return false;
        }

        if (isset(self::$skemaCache[$userId])) {
            return self::$skemaCache[$userId];
        }

        if (! File::exists(self::pathFor($userId))) {
            return false;
        }

        return self::$skemaCache[$userId] = self::skemaLengkap($userId);
    }

    /**
     * Apakah file tenant sudah memuat seluruh tabel wajib DAN marker kepemilikan
     * yang cocok dengan user yang diminta.
     *
     * Pemeriksaan marker mencegah file nyasar (sisa user yang sudah dihapus)
     * dipakai ulang oleh user lain dengan user_id yang sama.
     *
     * Memakai koneksi terpisah supaya pemeriksaan tidak mengganggu default
     * connection yang sedang aktif pada request lain.
     */
    private static function skemaLengkap(int $userId): bool
    {
        $connection = self::connectionName();
        $sebelum = DB::getDefaultConnection();

        try {
            Config::set('database.connections.'.$connection.'.database', self::pathFor($userId));
            DB::purge($connection);

            $schema = DB::connection($connection)->getSchemaBuilder();

            foreach (self::REQUIRED_TABLES as $table) {
                if (! $schema->hasTable($table)) {
                    return false;
                }
            }

            $tercatat = DB::connection($connection)
                ->table('pengaturan')
                ->where('key', self::OWNER_MARKER_KEY)
                ->value('value');

            return (int) $tercatat === $userId;
        } catch (Throwable) {
            return false;
        } finally {
            DB::setDefaultConnection($sebelum);
            DB::purge($connection);
        }
    }

    /**
     * Siapkan file database + jalankan seluruh migrasi di dalamnya.
     *
     * Idempoten: migrasi hanya menjalankan batch yang belum tercatat pada file
     * tenant tersebut. Seluruh proses dikunci per user supaya dua permintaan
     * bersamaan (mis. daftar lalu langsung klik dashboard) tidak menjalankan
     * migrasi pada file SQLite yang sama.
     */
    public static function provision(User|int $user): void
    {
        if (! self::enabled()) {
            return;
        }

        $userId = $user instanceof User ? (int) $user->getKey() : (int) $user;

        unset(self::$skemaCache[$userId]);

        try {
            Cache::lock(self::lockName($userId), 180)->block(
                180,
                fn () => self::provisionLocked($userId)
            );
        } catch (LockTimeoutException $exception) {
            throw new RuntimeException(
                'Database akun sedang disiapkan oleh proses lain. Silakan coba beberapa saat lagi.',
                0,
                $exception
            );
        }
    }

    /**
     * Nama lock provisioning untuk satu user.
     */
    private static function lockName(int $userId): string
    {
        return 'tenant:provision:'.$userId;
    }

    /**
     * Isi provisioning yang dijalankan di dalam lock per user.
     */
    private static function provisionLocked(int $userId): void
    {
        $path = self::pathFor($userId);

        UserDatabase::query()->updateOrCreate(
            ['user_id' => $userId],
            ['database_path' => $path, 'status' => UserDatabase::STATUS_PROVISIONING, 'last_error' => null],
        );

        try {
            File::ensureDirectoryExists(dirname($path));

            // File yang sudah ada tapi tidak sah (skema tidak lengkap, atau marker
            // kepemilikannya milik user lain) tidak bisa diperbaiki dengan migrate:
            // Laravel menganggap semua migrasi sudah dijalankan. Bangun ulang dari
            // nol supaya tenant tidak pernah tersangkut selamanya dan file nyasar
            // tidak pernah dipakai user yang salah.
            if (File::exists($path) && ! self::skemaLengkap($userId)) {
                self::hapusFile($userId);
                File::ensureDirectoryExists(dirname($path));
            }

            if (! File::exists($path)) {
                File::put($path, '');
            }

            self::connect($userId);

            // Migrasi perlu berjalan tanpa filter per-user: sebagian migrasi lama
            // menyentuh model tenant (mis. AkunPerkiraan) sebelum kolom user_id
            // ada, dan saat request terautentikasi global scope akan menambah
            // klausa user_id yang belum tersedia.
            self::tanpaScope(function () {
                Artisan::call('migrate', [
                    '--database' => self::connectionName(),
                    '--force' => true,
                ]);
            });

            self::purgasiBarisTanpaPemilik();

            // Tandai kepemilikan file ini di dalam file itu sendiri. Ditulis
            // setelah migrasi selesai supaya tidak ikut terhapus oleh purge dan
            // selalu mencerminkan user yang benar-benar memakai file tersebut.
            DB::connection(self::connectionName())->table('pengaturan')->updateOrInsert(
                ['user_id' => $userId, 'key' => self::OWNER_MARKER_KEY],
                ['value' => (string) $userId, 'updated_at' => now(), 'created_at' => now()],
            );

            unset(self::$skemaCache[$userId]);

            UserDatabase::query()->where('user_id', $userId)->update([
                'status' => UserDatabase::STATUS_READY,
                'provisioned_at' => now(),
                'last_error' => null,
            ]);
        } catch (Throwable $exception) {
            UserDatabase::query()->where('user_id', $userId)->update([
                'status' => UserDatabase::STATUS_FAILED,
                'last_error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Arahkan koneksi default ke database tenant user (tanpa menyiapkan file).
     */
    public static function connect(User|int $user): void
    {
        $connection = self::connectionName();
        $path = self::pathFor($user);

        Config::set('database.connections.'.$connection.'.database', $path);

        DB::purge($connection);
        DB::setDefaultConnection($connection);

        self::$activeUserId = $user instanceof User ? (int) $user->getKey() : (int) $user;
    }

    /**
     * Pastikan file ada lalu arahkan default connection ke tenant user.
     */
    public static function ensure(User $user): void
    {
        if (! self::hasDatabase($user)) {
            self::provision($user);
        }

        self::connect($user);
    }

    /**
     * Kembalikan default connection ke database shared.
     */
    public static function disconnect(): void
    {
        self::$activeUserId = null;

        DB::setDefaultConnection(self::SHARED_CONNECTION);
        DB::purge(self::connectionName());
    }

    /**
     * Buang baris tenant yang `user_id`-nya NULL.
     *
     * Sebagian migrasi lama menyisipkan data default (gudang GDG-0001,
     * rekanan UMUM, akun 526) tanpa mengisi user_id. Baris seperti itu tidak
     * terlihat di UI — global scope menyaringnya — tapi tetap menggandakan
     * data milik user, dan melanggar UNIQUE(user_id, kode) begitu user
     * seawajarnya memakai kode yang sama.
     *
     * Dipanggil segera setelah migrasi tenant selesai, sebelum seeder berjalan,
     * sehingga user baru tidak pernah menerima duplikat.
     */
    public static function purgasiBarisTanpaPemilik(): int
    {
        $connection = self::connectionName();
        $terhapus = 0;

        foreach (TenantTables::all() as $table) {
            if (DB::connection($connection)->getSchemaBuilder()->hasTable($table)) {
                $terhapus += DB::connection($connection)->table($table)->whereNull('user_id')->delete();
            }
        }

        return $terhapus;
    }

    /**
     * Hapus file database tenant beserta peta user→database.
     *
     * Tidak melakukan apa pun bila fitur nonaktif. Path tenant berasal dari
     * config dan tetap menunjuk ke storage/app/tenant pada environment mana pun;
     * tanpa guard ini, test yang menghapus user (hook User::deleting) ikut
     * menghapus file database produksi milik user dengan id yang sama.
     */
    public static function drop(User|int $user): void
    {
        if (! self::enabled()) {
            return;
        }

        $userId = $user instanceof User ? (int) $user->getKey() : (int) $user;
        $connection = self::connectionName();

        unset(self::$skemaCache[$userId]);

        if (DB::getDefaultConnection() === $connection) {
            DB::setDefaultConnection(self::SHARED_CONNECTION);
        }

        DB::purge($connection);

        self::hapusFile($userId);

        UserDatabase::query()->where('user_id', $userId)->delete();
    }

    /**
     * Hapus file SQLite milik user beserta file pendampingnya.
     */
    private static function hapusFile(int $userId): void
    {
        $path = self::pathFor($userId);

        foreach ([$path, $path.'-wal', $path.'-shm'] as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        $directory = dirname($path);

        if (File::isDirectory($directory) && count(File::files($directory)) === 0) {
            File::deleteDirectory($directory);
        }
    }
}
