<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokGudang extends Model
{
    use BelongsToUser;

    protected $table = 'stok_gudang';

    protected $fillable = ['barang_id', 'gudang_id', 'qty'];

    protected $casts = ['qty' => 'decimal:2'];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class);
    }
}
