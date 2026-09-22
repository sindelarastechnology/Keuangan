<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerubahanStokItem extends Model
{
    use BelongsToUser;

    protected $table = 'perubahan_stok_items';

    protected $fillable = [
        'perubahan_stok_id', 'barang_id', 'arah', 'jumlah', 'harga', 'subtotal', 'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function perubahanStok(): BelongsTo
    {
        return $this->belongsTo(PerubahanStok::class, 'perubahan_stok_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
