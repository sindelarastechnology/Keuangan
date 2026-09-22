<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use BelongsToUser;
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = ['kode', 'nama', 'alamat', 'telepon', 'email', 'npwp', 'keterangan', 'is_aktif'];

    protected $casts = ['is_aktif' => 'boolean'];

    public function penjualans(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    public function getSaldoPiutangAttribute(): float
    {
        return BbPiutang::where('customer_id', $this->id)
            ->orderByDesc('id')
            ->value('saldo') ?? 0;
    }

    /**
     * Customer default "UMUM" tempat harga jual standar tiap barang dicatat.
     */
    public static function umum(): ?self
    {
        return static::query()->where('kode', 'UMUM')->first();
    }
}
