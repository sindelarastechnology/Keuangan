<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasKeluarItem extends Model
{
    use BelongsToUser;

    protected $table = 'kas_keluar_items';

    protected $fillable = ['kas_keluar_id', 'akun_id', 'keterangan', 'nominal'];

    protected $casts = ['nominal' => 'decimal:2'];

    public function kasKeluar(): BelongsTo
    {
        return $this->belongsTo(KasKeluar::class);
    }

    public function akun(): BelongsTo
    {
        return $this->belongsTo(AkunPerkiraan::class, 'akun_id');
    }
}
