<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JurnalItem extends Model
{
    use BelongsToUser;

    protected $table = 'jurnal_items';

    protected $fillable = ['jurnal_id', 'akun_id', 'debit', 'kredit', 'keterangan'];

    protected $casts = [
        'debit' => 'decimal:2',
        'kredit' => 'decimal:2',
    ];

    public function jurnal(): BelongsTo
    {
        return $this->belongsTo(JurnalUmum::class, 'jurnal_id');
    }

    public function akun(): BelongsTo
    {
        return $this->belongsTo(AkunPerkiraan::class, 'akun_id');
    }
}
