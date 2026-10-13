<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\TenantDatabase;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'google_id', 'status', 'plan', 'plan_expires_at', 'trial_ends_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * User disimpan pada database shared; tidak ikut default connection tenant.
     */
    protected $connection = 'mysql';

    /**
     * Notifikasi database milik user (tabel shared `notifications`).
     *
     * @return MorphMany<SharedNotification, $this>
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(SharedNotification::class, 'notifiable')->latest();
    }

    /**
     * Hapus database tenant milik user saat user dihapus.
     */
    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            TenantDatabase::drop($user);

            $user->notifications()->delete();
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'plan_expires_at' => 'datetime',
            'trial_ends_at' => 'datetime',
        ];
    }

    /**
     * Cek apakah akun user masih aktif.
     */
    public function isAktif(): bool
    {
        return $this->status !== 'suspended';
    }
}
