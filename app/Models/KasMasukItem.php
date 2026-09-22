<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasMasukItem extends Model
{
    use BelongsToUser;

    protected $table = 'kas_masuk_items';

    protected $fillable = ['kas_masuk_id', 'akun_id', 'keterangan', 'nominal'];

    protected $casts = ['nominal' => 'decimal:2'];

    public function kasMasuk(): BelongsTo
    {
        return $this->belongsTo(KasMasuk::class);
    }

    public function akun(): BelongsTo
    {
        return $this->belongsTo(AkunPerkiraan::class, 'akun_id');
    }
}
