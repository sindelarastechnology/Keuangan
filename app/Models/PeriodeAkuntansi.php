<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class PeriodeAkuntansi extends Model
{
    use BelongsToUser;

    protected $table = 'periode_akuntansi';

    protected $fillable = ['kode', 'bulan', 'tahun', 'is_open', 'is_closed', 'tanggal_buka', 'tanggal_tutup', 'is_locked', 'locked_at', 'locked_by', 'lock_reason'];

    protected $casts = [
        'is_open' => 'boolean',
        'is_closed' => 'boolean',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
    ];

    public function scopeAktif($query)
    {
        return $query->where('is_open', true);
    }

    public function getLabelAttribute(): string
    {
        return namaBulan($this->bulan).' '.$this->tahun;
    }
}
