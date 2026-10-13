<?php

namespace App\Services;

use App\Models\AkunPerkiraan;
use App\Models\Customer;
use App\Models\Gudang;
use App\Models\Rekening;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\AkunPerkiraanSeeder;
use Database\Seeders\AssetTemplateSeeder;
use Database\Seeders\PajakSeeder;
use Database\Seeders\PengaturanSeeder;
use Database\Seeders\PeriodeSeeder;
use Illuminate\Support\Facades\Auth;

/**
 * Menyiapkan data awal milik satu tenant (satu user).
 *
 * Idempoten: seed.chart of accounts hanya berjalan bila tenant belum
 * memilikinya, sedangkan data default yang wajib ada agar aplikasi bisa dipakai
 * (gudang, rekanan UMUM, rekening kas) selalu dipastikan ulang. Dipanggil saat
 * DatabaseSeeder (admin pertama) dan setiap kali user login/verifikasi email.
 */
class ProvisionTenant
{
    public static function provision(User $user): void
    {
        // Siapkan database tenant milik user lalu arahkan default connection ke
        // sana. Saat fitur nonaktif (test suite) ini tidak melakukan apa pun dan
        // seed berjalan pada database tunggal seperti sebelumnya.
        if (TenantDatabase::enabled()) {
            TenantDatabase::ensure($user);
        }

        $perluSeedChart = ! AkunPerkiraan::withoutGlobalScope('belongsToUser')
            ->where('user_id', $user->id)
            ->exists();

        // Pastikan konteks auth = pemilik agar global scope menempel user_id.
        $guard = Auth::guard('web');
        $sebelumnya = $guard->user();
        $guard->setUser($user);

        try {
            if ($perluSeedChart) {
                (new AkunPerkiraanSeeder)->run();
                (new AssetTemplateSeeder)->run();
                (new PajakSeeder)->run();
                (new PengaturanSeeder)->run();
                (new PeriodeSeeder)->run();

                self::pastikanGudangDefault($user);
                self::pastikanRekananUmum($user);
            }

            // Berjalan di luar blok seed supaya tenant lama yang belum punya
            // rekening pun ikut dilengkapi pada login berikutnya.
            self::pastikanRekeningKasDefault($user);
        } finally {
            // Kembalikan konteks auth seperti semula. Tanpa ini user yang baru
            // saja diprovisioning ikut dianggap login pada proses panjang
            // (console, test, worker), sehingga request berikutnya bisa memakai
            // akun yang salah.
            if ($sebelumnya instanceof User) {
                $guard->setUser($sebelumnya);
            } else {
                $guard->forgetUser();
            }
        }
    }

    /**
     * Pastikan tenant punya minimal satu rekening kas.
     *
     * Tanpa ini dropdown Rekening Tujuan pada form Kas Masuk/Kas Keluar kosong
     * dan validasi `rekening_id` wajib menggagalkan pencatatan kas sama sekali.
     * Dipakai akun kas default '111' dari chart of accounts.
     */
    private static function pastikanRekeningKasDefault(User $user): void
    {
        $akunKas = AkunPerkiraan::withoutGlobalScope('belongsToUser')
            ->where('user_id', $user->id)
            ->where('kode', '111')
            ->value('id');

        if (! $akunKas) {
            return;
        }

        $sudahAda = Rekening::withoutGlobalScope('belongsToUser')
            ->where('user_id', $user->id)
            ->where(function ($q) use ($akunKas) {
                $q->where('akun_id', $akunKas)
                    ->orWhere('nama', 'Kas');
            })
            ->exists();

        if ($sudahAda) {
            return;
        }

        Rekening::withoutGlobalScope('belongsToUser')->create([
            'user_id' => $user->id,
            'jenis' => 'kas',
            'nama' => 'Kas',
            'akun_id' => (int) $akunKas,
            'saldo_awal' => 0,
            'is_aktif' => true,
        ]);
    }

    private static function pastikanGudangDefault(User $user): void
    {
        Gudang::withoutGlobalScope('belongsToUser')
            ->where('user_id', $user->id)
            ->where('kode', 'GDG-0001')
            ->firstOrCreate(
                ['user_id' => $user->id, 'kode' => 'GDG-0001'],
                ['nama' => 'Gudang Umum', 'keterangan' => 'Gudang default untuk semua barang.', 'is_aktif' => true]
            );
    }

    private static function pastikanRekananUmum(User $user): void
    {
        Supplier::withoutGlobalScope('belongsToUser')
            ->where('user_id', $user->id)
            ->where('kode', 'UMUM')
            ->firstOrCreate(
                ['user_id' => $user->id, 'kode' => 'UMUM'],
                ['nama' => 'UMUM', 'is_aktif' => true]
            );

        Customer::withoutGlobalScope('belongsToUser')
            ->where('user_id', $user->id)
            ->where('kode', 'UMUM')
            ->firstOrCreate(
                ['user_id' => $user->id, 'kode' => 'UMUM'],
                ['nama' => 'UMUM', 'is_aktif' => true]
            );
    }
}
