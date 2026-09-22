<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToUser
{
    /**
     * Boot isolasi per-tenant.
     *
     * Global scope memfilter semua query dengan user_id = pemilik saat user
     * terautentikasi. Ketika tidak ada user (console, seeder, tinker), filter
     * dinonaktifkan agar migrasi/seed tetap berjalan. Kolom user_id diisi
     * otomatis saat creating berlangsung di request terautentikasi.
     */
    protected static function bootBelongsToUser(): void
    {
        static::addGlobalScope('belongsToUser', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where($builder->getModel()->qualifyColumn('user_id'), Auth::id());
            }
        });

        static::creating(function (Model $model) {
            if (Auth::check() && empty($model->user_id)) {
                $model->user_id = Auth::id();
            }
        });

        static::updating(function (Model $model) {
            if (Auth::check() && $model->isDirty('user_id') && $model->user_id !== Auth::id()) {
                $model->user_id = Auth::id();
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
