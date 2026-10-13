<?php

namespace App\Traits;

use App\Services\TenantDatabase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToUser
{
    /**
     * User pemilik data yang sedang diproses.
     *
     * Sumbernya adalah user yang login (request web) atau tenant yang sedang
     * aktif diproses command/worker. Keduanya harus dianggap sebagai pemilik
     * yang sama: tanpa fallback ke `TenantDatabase::activeUserId()`, setiap
     * perintah konsol yang memproses satu tenant tanpa `Auth::check()` akan
     * membaca dan menulis seluruh baris tanpa filter.
     */
    protected static function pemilikAktif(): ?int
    {
        $id = Auth::id() ?? TenantDatabase::activeUserId();

        return $id === null ? null : (int) $id;
    }

    /**
     * Boot isolasi per-tenant.
     *
     * Global scope memfilter semua query dengan user_id = pemilik saat pemilik
     * diketahui. Ketika tidak ada pemilik sama sekali (console tanpa konteks,
     * seeder, tinker), filter dinonaktifkan agar migrasi/seed tetap berjalan.
     * Kolom user_id diisi otomatis saat creating berlangsung di bawah konteks
     * pemilik yang sah.
     */
    protected static function bootBelongsToUser(): void
    {
        static::addGlobalScope('belongsToUser', function (Builder $builder) {
            if (TenantDatabase::scopeDisabled()) {
                return;
            }

            $pemilik = static::pemilikAktif();

            if ($pemilik !== null) {
                $builder->where($builder->getModel()->qualifyColumn('user_id'), $pemilik);
            }
        });

        static::creating(function (Model $model) {
            if (TenantDatabase::scopeDisabled()) {
                return;
            }

            $pemilik = static::pemilikAktif();

            if ($pemilik !== null && empty($model->user_id)) {
                $model->user_id = $pemilik;
            }
        });

        static::updating(function (Model $model) {
            if (TenantDatabase::scopeDisabled()) {
                return;
            }

            $pemilik = static::pemilikAktif();

            if ($pemilik !== null && $model->isDirty('user_id') && $model->user_id !== $pemilik) {
                $model->user_id = $pemilik;
            }
        });
    }

    /**
     * Scope eksplisit per pemilik (dipakai saat provisioning/migrasi).
     */
    public function scopeUntukUser(Builder $query, int $userId): Builder
    {
        return $query->withoutGlobalScope('belongsToUser')->where($this->qualifyColumn('user_id'), $userId);
    }
}
