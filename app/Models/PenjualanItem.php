<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenjualanItem extends Model
{
    use BelongsToUser;

    protected $table = 'penjualan_items';

    protected $fillable = [
        'penjualan_id', 'barang_id', 'jumlah', 'harga_satuan',
        'diskon', 'subtotal', 'hpp', 'hpp_total',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'diskon' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'hpp' => 'decimal:2',
        'hpp_total' => 'decimal:2',
    ];

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
