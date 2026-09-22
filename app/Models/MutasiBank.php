<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class MutasiBank extends Model
{
    use BelongsToUser;
    use HasFactory;

    protected $table = 'mutasi_bank';

    protected $fillable = ['nomor', 'tanggal', 'rekening_asal_id', 'rekening_tujuan_id', 'nominal', 'keterangan'];

    protected $casts = ['nominal' => 'decimal:2'];

    public function rekeningAsal(): BelongsTo
    {
        return $this->belongsTo(Rekening::class, 'rekening_asal_id');
    }

    public function rekeningTujuan(): BelongsTo
    {
        return $this->belongsTo(Rekening::class, 'rekening_tujuan_id');
    }

    public function jurnal(): MorphOne
    {
        return $this->morphOne(JurnalUmum::class, 'ref');
    }
}
