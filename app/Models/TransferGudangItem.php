<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferGudangItem extends Model
{
    use BelongsToUser;

    protected $table = 'transfer_gudang_items';

    protected $fillable = ['transfer_gudang_id', 'barang_id', 'jumlah', 'keterangan'];

    protected $casts = ['jumlah' => 'decimal:2'];

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(TransferGudang::class, 'transfer_gudang_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
