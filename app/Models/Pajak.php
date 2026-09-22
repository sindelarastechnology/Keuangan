<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pajak extends Model
{
    use BelongsToUser;
    use HasFactory;

    protected $table = 'pajak';

    protected $fillable = ['nama', 'rate', 'is_aktif'];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_aktif' => 'boolean',
    ];

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}
