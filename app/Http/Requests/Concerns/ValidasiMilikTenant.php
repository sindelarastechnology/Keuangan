<?php

namespace App\Http\Requests\Concerns;

use App\Services\TenantDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

/**
 * Aturan validasi yang membatasi referensi ke baris milik user sendiri.
 *
 * Aturan `exists:tabel,id` biasa memeriksa seluruh tabel. Pada mode database
 * per-user hal itu tidak bisa menjebol karena setiap user memakai file SQLite
 * sendiri, tetapi pada mode shared (test suite, integrasi, atau saat
 * TENANT_DB_ENABLED tidak aktif) id milik user lain tetap lolos validasi.
 * Controllers sudah terlindungi global scope `BelongsToUser`, jadi yang suffers
 * adalah pesan errornya — validasi gagal Total dengan alasan yang menyesatkan.
 *
 * Memakai aturan ini membuat penolakan terjadi lebih awal dengan alasan yang
 * benar: "rekening tidak ada milik Anda", bukan "akun kas tidak valid".
 */
trait ValidasiMilikTenant
{
    /**
     * Aturan exists yang hanya menerima baris milik user aktif.
     *
     * @param  string  $table  nama tabel tenant
     * @param  string  $column  kolom yang dibandingkan
     */
    protected function milikTenant(string $table, string $column = 'id'): Exists
    {
        $rule = Rule::exists($table, $column);

        $userId = Auth::id() ?? TenantDatabase::activeUserId();

        if ($userId === null) {
            // Tanpa konteks pemilik (console/worker tanpa Auth) tidak ada yang
            // bisa dipastikan, jadi jangan tambah kriteria apa pun agar perintah
            // internal tidak ikut gagal diam-diam.
            return $rule;
        }

        return $rule->where(fn ($query) => $query->where($table.'.user_id', $userId));
    }
}
