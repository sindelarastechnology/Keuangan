<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokOpnameItem extends Model
{
    use BelongsToUser;

    protected $table = 'stok_opname_items';

    protected $fillable = [
        'stok_opname_id', 'barang_id', 'stok_sistem', 'stok_fisik', 'selisih', 'harga', 'subtotal', 'keterangan',
    ];

    protected $casts = [
        'stok_sistem' => 'decimal:2',
        'stok_fisik' => 'decimal:2',
        'selisih' => 'decimal:2',
        'harga' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function stokOpname(): BelongsTo
    {
        return $this->belongsTo(StokOpname::class, 'stok_opname_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
