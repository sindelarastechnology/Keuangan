<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutupBuku extends Model
{
    use BelongsToUser;

    protected $table = 'tutup_buku';

    protected $fillable = [
        'periode_id', 'tanggal', 'laba_rugi',
        'total_pendapatan', 'total_beban', 'keterangan',
    ];

    protected $casts = [
        'laba_rugi' => 'decimal:2',
        'total_pendapatan' => 'decimal:2',
        'total_beban' => 'decimal:2',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PeriodeAkuntansi::class, 'periode_id');
    }
}
