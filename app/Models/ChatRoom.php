<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatRoom extends Model
{
    public const TIPE_GLOBAL = 'global';

    public const TIPE_DM = 'dm';

    protected $fillable = [
        'tipe',
        'user_a_id',
        'user_b_id',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'room_id');
    }

    public function userA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_a_id');
    }

    public function userB(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_b_id');
    }

    public function isGlobal(): bool
    {
        return $this->tipe === self::TIPE_GLOBAL;
    }

    public function isDm(): bool
    {
        return $this->tipe === self::TIPE_DM;
    }

    /**
     * Lawan bicara untuk ruang DM. Mengembalikan null untuk ruang global.
     */
    public function mitra(int $userId): ?User
    {
        if ($this->isGlobal()) {
            return null;
        }

        if ((int) $this->user_a_id === $userId) {
            return $this->userB;
        }

        if ((int) $this->user_b_id === $userId) {
            return $this->userA;
        }

        return null;
    }

    /**
     * Ruang komunitas global, dibuat otomatis bila belum ada.
     */
    public static function ruangKomunitas(): self
    {
        return self::firstOrCreate(['tipe' => self::TIPE_GLOBAL]);
    }

    /**
     * Cari ruang DM untuk pasangan user secara kanonis (id kecil = user_a).
     */
    public static function cariDm(int $a, int $b): ?self
    {
        [$kecil, $besar] = self::pasanganKanonis($a, $b);

        return self::where('tipe', self::TIPE_DM)
            ->where('user_a_id', $kecil)
            ->where('user_b_id', $besar)
            ->first();
    }

    /**
     * Ambil atau buat ruang DM antara dua user.
     */
    public static function buatDm(int $a, int $b): self
    {
        if ($a === $b) {
            throw new \InvalidArgumentException('Tidak dapat membuat ruang DM dengan diri sendiri.');
        }

        [$kecil, $besar] = self::pasanganKanonis($a, $b);

        return self::firstOrCreate([
            'tipe' => self::TIPE_DM,
            'user_a_id' => $kecil,
            'user_b_id' => $besar,
        ]);
    }

    /**
     * @return array{0: int, 1: int}
     */
    private static function pasanganKanonis(int $a, int $b): array
    {
        return $a < $b ? [$a, $b] : [$b, $a];
    }
}
