<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penyusutan extends Model
{
    use BelongsToUser;

    protected $table = 'penyusutan';

    protected $fillable = [
        'aset_id', 'periode', 'tanggal', 'beban',
        'akumulasi_setelah', 'nilai_buku_setelah', 'jurnal_id', 'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'beban' => 'decimal:2',
        'akumulasi_setelah' => 'decimal:2',
        'nilai_buku_setelah' => 'decimal:2',
    ];

    public function aset(): BelongsTo
    {
        return $this->belongsTo(Aset::class, 'aset_id');
    }

    public function jurnal(): BelongsTo
    {
        return $this->belongsTo(JurnalUmum::class, 'jurnal_id');
    }
}
