<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class TutupBukuTahunan extends Model
{
    use BelongsToUser;

    protected $table = 'tutup_buku_tahunan';

    protected $fillable = ['tahun', 'tanggal', 'laba_rugi', 'total_pendapatan', 'total_beban', 'keterangan'];

    protected $casts = [
        'tanggal' => 'date',
        'laba_rugi' => 'decimal:2',
        'total_pendapatan' => 'decimal:2',
        'total_beban' => 'decimal:2',
    ];
}
