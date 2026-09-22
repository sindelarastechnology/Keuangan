<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetTemplate extends Model
{
    use BelongsToUser;
    use HasFactory;

    protected $table = 'asset_templates';

    protected $fillable = [
        'nama_kategori', 'masa_manfaat_bulan', 'persen_residu',
        'akun_aset_id', 'akun_akumulasi_id', 'akun_beban_id',
    ];

    protected $casts = [
        'masa_manfaat_bulan' => 'integer',
        'persen_residu' => 'decimal:2',
    ];
}
