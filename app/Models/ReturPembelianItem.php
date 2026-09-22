<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturPembelianItem extends Model
{
    use BelongsToUser;

    protected $table = 'retur_pembelian_items';

    protected $fillable = [
        'retur_pembelian_id', 'pembelian_item_id', 'barang_id',
        'jumlah', 'harga_satuan', 'subtotal',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function returPembelian(): BelongsTo
    {
        return $this->belongsTo(ReturPembelian::class, 'retur_pembelian_id');
    }

    public function pembelianItem(): BelongsTo
    {
        return $this->belongsTo(PembelianItem::class, 'pembelian_item_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
