<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rekening extends Model
{
    use BelongsToUser;
    use HasFactory;

    protected $table = 'rekenings';

    protected $fillable = [
        'jenis', 'nama', 'nomor_rekening', 'nama_pemilik', 'akun_id', 'saldo_awal', 'is_aktif',
    ];

    protected $casts = [
        'saldo_awal' => 'decimal:2',
        'is_aktif' => 'boolean',
    ];

    public function akun(): BelongsTo
    {
        return $this->belongsTo(AkunPerkiraan::class, 'akun_id');
    }

    public function isKas(): bool
    {
        return $this->jenis === 'kas';
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    public function getSaldoAttribute(): float
    {
        $akunId = (int) $this->akun_id;

        if ($akunId <= 0) {
            return (float) $this->saldo_awal;
        }

        $total = (float) JurnalItem::where('jurnal_items.akun_id', $akunId)
            ->whereHas('jurnal', fn ($q) => $q->tanpaVoid()->where('is_posted', true))
            ->selectRaw('COALESCE(SUM(jurnal_items.debit),0) - COALESCE(SUM(jurnal_items.kredit),0) as selisih')
            ->value('selisih');

        // Efek jurnal "Saldo Awal ..." pada akun ini dikeluarkan dari total GL,
        // lalu saldo_awal rekening ditambahkan kembali. Hasil tetap konsisten
        // baik jurnal pembuka tercatat maupun tidak, dan tidak pernah dobel-hitung.
        $totalSaldoAwal = (float) JurnalItem::where('jurnal_items.akun_id', $akunId)
            ->whereHas('jurnal', fn ($q) => $q->tanpaVoid()->where('is_posted', true)->where('keterangan', 'like', 'Saldo Awal%'))
            ->selectRaw('COALESCE(SUM(jurnal_items.debit),0) - COALESCE(SUM(jurnal_items.kredit),0) as selisih')
            ->value('selisih');

        return round((float) $this->saldo_awal + $total - $totalSaldoAwal, 2);
    }
}
