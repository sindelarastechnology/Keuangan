<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aset extends Model
{
    use BelongsToUser;
    use HasFactory;

    protected $table = 'asets';

    protected $fillable = [
        'kode', 'nama', 'kategori', 'deskripsi', 'lokasi',
        'tanggal_perolehan', 'harga_perolehan', 'nilai_residu',
        'masa_manfaat_bulan', 'akun_aset_id', 'akun_akumulasi_id',
        'akun_beban_id', 'sumber_dana_id', 'rekening_id', 'supplier_id',
        'catat_perolehan', 'status', 'keterangan', 'foto',
        'disposisi_alasan', 'disposisi_harga_jual', 'disposisi_laba',
        'disposisi_tanggal',
    ];

    protected $casts = [
        'tanggal_perolehan' => 'date',
        'harga_perolehan' => 'decimal:2',
        'nilai_residu' => 'decimal:2',
        'masa_manfaat_bulan' => 'integer',
        'catat_perolehan' => 'boolean',
        'disposisi_harga_jual' => 'decimal:2',
        'disposisi_laba' => 'decimal:2',
        'disposisi_tanggal' => 'date',
    ];

    public function penyusutan(): HasMany
    {
        return $this->hasMany(Penyusutan::class)->orderBy('periode');
    }

    public function akunAset(): BelongsTo
    {
        return $this->belongsTo(AkunPerkiraan::class, 'akun_aset_id');
    }

    public function akunAkumulasi(): BelongsTo
    {
        return $this->belongsTo(AkunPerkiraan::class, 'akun_akumulasi_id');
    }

    public function akunBeban(): BelongsTo
    {
        return $this->belongsTo(AkunPerkiraan::class, 'akun_beban_id');
    }

    public function sumberDana(): BelongsTo
    {
        return $this->belongsTo(AkunPerkiraan::class, 'sumber_dana_id');
    }

    public function rekening(): BelongsTo
    {
        return $this->belongsTo(Rekening::class, 'rekening_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    protected function bebanBulanan(): Attribute
    {
        return Attribute::make(
            get: fn () => round(((float) $this->harga_perolehan - (float) $this->nilai_residu) / max(1, (int) $this->masa_manfaat_bulan), 2)
        );
    }

    protected function akumulasi(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) ($this->penyusutan_sum_beban ?? $this->penyusutan->sum('beban'))
        );
    }

    protected function nilaiBuku(): Attribute
    {
        return Attribute::make(
            get: fn () => round((float) $this->harga_perolehan - $this->akumulasi, 2)
        );
    }

    protected function nilaiSisaDisusutkan(): Attribute
    {
        return Attribute::make(
            get: fn () => round((float) $this->harga_perolehan - (float) $this->nilai_residu - $this->akumulasi, 2)
        );
    }

    protected function sudahDisusutkanPenuh(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nilai_sisa_disusutkan <= 0.01
        );
    }

    protected function mulaiPeriode(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tanggal_perolehan ? Carbon::parse($this->tanggal_perolehan)->addMonth()->format('Y-m') : null
        );
    }

    public function bisaDisusutkan(string $periode): bool
    {
        if ($this->status !== 'aktif') {
            return false;
        }

        if ($this->sudah_disusutkan_penuh) {
            return false;
        }

        return $periode >= $this->mulai_periode;
    }

    public static function generateKode(string $prefix = 'AS'): string
    {
        $max = (int) static::max('id') + 1;

        return $prefix.'-'.str_pad((string) $max, 4, '0', STR_PAD_LEFT);
    }
}
