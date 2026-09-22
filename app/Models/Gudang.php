<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gudang extends Model
{
    use BelongsToUser;

    protected $table = 'gudangs';

    protected $fillable = ['kode', 'nama', 'alamat', 'keterangan', 'is_aktif'];

    protected $casts = ['is_aktif' => 'boolean'];

    public function stok(): HasMany
    {
        return $this->hasMany(StokGudang::class, 'gudang_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    public static function generateKode(): string
    {
        $max = (int) static::max('id') + 1;

        return 'GDG-'.str_pad((string) $max, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Gudang utama/default (yang pertama, biasanya "Gudang Umum").
     */
    public static function utama(): ?self
    {
        return static::query()->orderBy('id')->first();
    }
}
