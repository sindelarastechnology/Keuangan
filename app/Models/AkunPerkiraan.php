<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AkunPerkiraan extends Model
{
    use BelongsToUser;
    use HasFactory;

    protected $table = 'akun_perkiraan';

    protected $fillable = [
        'kode', 'nama', 'jenis', 'saldo_normal',
        'is_header', 'parent_id', 'keterangan', 'is_aktif',
    ];

    protected $casts = [
        'is_header' => 'boolean',
        'is_aktif' => 'boolean',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(AkunPerkiraan::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AkunPerkiraan::class, 'parent_id');
    }

    public function jurnalItems(): HasMany
    {
        return $this->hasMany(JurnalItem::class, 'akun_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    public function scopeLeaf($query)
    {
        return $query->where('is_header', false);
    }

    public static function listJenis(): array
    {
        return [
            'aset' => 'Aset',
            'kewajiban' => 'Kewajiban',
            'modal' => 'Modal',
            'pendapatan' => 'Pendapatan',
            'beban' => 'Beban',
        ];
    }

    public function getJenisLabelAttribute(): string
    {
        return self::listJenis()[$this->jenis] ?? $this->jenis;
    }
}
