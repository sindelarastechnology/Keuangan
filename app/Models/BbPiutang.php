<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BbPiutang extends Model
{
    use BelongsToUser;

    protected $table = 'bb_piutang';

    protected $fillable = ['customer_id', 'penjualan_id', 'jurnal_id', 'tanggal', 'keterangan', 'debit', 'kredit', 'saldo'];

    protected $casts = [
        'debit' => 'decimal:2',
        'kredit' => 'decimal:2',
        'saldo' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function jurnal(): BelongsTo
    {
        return $this->belongsTo(JurnalUmum::class);
    }
}
