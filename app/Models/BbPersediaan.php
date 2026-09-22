<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class BbPersediaan extends Model
{
    use BelongsToUser;

    protected $table = 'bb_persediaan';

    protected $fillable = [
        'barang_id', 'ref_type', 'ref_id', 'tanggal', 'keterangan',
        'masuk_qty', 'masuk_harga', 'keluar_qty', 'keluar_harga',
        'saldo_qty', 'saldo_harga', 'ratt',
    ];

    protected $casts = [
        'masuk_qty' => 'decimal:2',
        'masuk_harga' => 'decimal:2',
        'keluar_qty' => 'decimal:2',
        'keluar_harga' => 'decimal:2',
        'saldo_qty' => 'decimal:2',
        'saldo_harga' => 'decimal:2',
        'ratt' => 'decimal:2',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function ref()
    {
        return $this->morphTo();
    }
}
