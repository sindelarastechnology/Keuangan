<?php

namespace App\Services;

use App\Models\AkunPerkiraan;
use App\Models\Customer;
use App\Models\Gudang;
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
 * Idempoten: seed dijalankan hanya bila tenant belum memiliki chart of
 * accounts. Dipanggil saat DatabaseSeeder (admin pertama) dan saat user baru
 * pertama kali login/terverifikasi email.
 */
class ProvisionTenant
{
    public static function provision(User $user): void
    {
        if (AkunPerkiraan::withoutGlobalScope('belongsToUser')->where('user_id', $user->id)->exists()) {
            return;
        }

        // Pastikan konteks auth = pemilik agar global scope menempel user_id.
        $guard = Auth::guard('web');
        $guard->setUser($user);

        (new AkunPerkiraanSeeder)->run();
        (new AssetTemplateSeeder)->run();
        (new PajakSeeder)->run();
        (new PengaturanSeeder)->run();
        (new PeriodeSeeder)->run();

        self::pastikanGudangDefault($user);
        self::pastikanRekananUmum($user);
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
