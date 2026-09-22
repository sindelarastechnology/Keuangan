<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BbHutang extends Model
{
    use BelongsToUser;

    protected $table = 'bb_hutang';

    protected $fillable = ['supplier_id', 'pembelian_id', 'jurnal_id', 'tanggal', 'keterangan', 'debit', 'kredit', 'saldo'];

    protected $casts = [
        'debit' => 'decimal:2',
        'kredit' => 'decimal:2',
        'saldo' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function pembelian(): BelongsTo
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }

    public function jurnal(): BelongsTo
    {
        return $this->belongsTo(JurnalUmum::class);
    }
}
