<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturPenjualanItem extends Model
{
    use BelongsToUser;

    protected $table = 'retur_penjualan_items';

    protected $fillable = [
        'retur_penjualan_id', 'penjualan_item_id', 'barang_id',
        'jumlah', 'harga_satuan', 'hpp', 'subtotal', 'hpp_total',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'hpp' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'hpp_total' => 'decimal:2',
    ];

    public function returPenjualan(): BelongsTo
    {
        return $this->belongsTo(ReturPenjualan::class, 'retur_penjualan_id');
    }

    public function penjualanItem(): BelongsTo
    {
        return $this->belongsTo(PenjualanItem::class, 'penjualan_item_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
