<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use BelongsToUser;
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = ['kode', 'nama', 'alamat', 'telepon', 'email', 'npwp', 'keterangan', 'is_aktif'];

    protected $casts = ['is_aktif' => 'boolean'];

    public function daftarHarga(): HasMany
    {
        return $this->hasMany(DaftarHarga::class);
    }

    public function pembelians(): HasMany
    {
        return $this->hasMany(Pembelian::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    public function getSaldoHutangAttribute(): float
    {
        return BbHutang::where('supplier_id', $this->id)
            ->orderByDesc('id')
            ->value('saldo') ?? 0;
    }

    /**
     * Supplier default "UMUM" tempat harga beli standar tiap barang dicatat.
     */
    public static function umum(): ?self
    {
        return static::query()->where('kode', 'UMUM')->first();
    }
}
