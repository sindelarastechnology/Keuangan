<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransferGudang extends Model
{
    use BelongsToUser;

    protected $table = 'transfer_gudang';

    protected $fillable = ['nomor', 'tanggal', 'gudang_asal', 'gudang_tujuan', 'keterangan', 'status', 'created_by'];

    protected $casts = [
        'tanggal' => 'date',
        'status' => 'string',
    ];

    public function asal(): BelongsTo
    {
        return $this->belongsTo(Gudang::class, 'gudang_asal');
    }

    public function tujuan(): BelongsTo
    {
        return $this->belongsTo(Gudang::class, 'gudang_tujuan');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransferGudangItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
