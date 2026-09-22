<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use App\Traits\HasApprovalWorkflow;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JurnalUmum extends Model
{
    use BelongsToUser;
    use HasApprovalWorkflow;

    protected $table = 'jurnal_umum';

    protected $fillable = [
        'nomor', 'tanggal', 'keterangan', 'tipe',
        'ref_type', 'ref_id', 'is_posted', 'voided_at',
        'created_by', 'updated_by', 'change_reason',
    ];

    protected $casts = [
        'is_posted' => 'boolean',
        'approved_at' => 'datetime',
        'voided_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(JurnalItem::class, 'jurnal_id')->orderBy('id');
    }

    public function ref()
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function auditTrails(): HasMany
    {
        return $this->hasMany(AuditTrail::class, 'auditable_id')->where('auditable_type', self::class)->orderByDesc('created_at');
    }

    public function getTotalDebitAttribute(): float
    {
        return (float) $this->items()->sum('debit');
    }

    public function getTotalKreditAttribute(): float
    {
        return (float) $this->items()->sum('kredit');
    }

    public function getTipeLabelAttribute(): string
    {
        $labels = [
            'kas_masuk' => 'Kas Masuk',
            'kas_keluar' => 'Kas Keluar',
            'mutasi_bank' => 'Mutasi Bank',
            'pembelian' => 'Pembelian',
            'penjualan' => 'Penjualan',
            'manual' => 'Jurnal Manual',
            'tutup_buku' => 'Tutup Buku',
            'hpp' => 'HPP',
            'pembayaran' => 'Pembayaran',
            'retur_penjualan' => 'Retur Penjualan',
            'retur_pembelian' => 'Retur Pembelian',
            'penyesuaian_stok' => 'Penyesuaian Stok',
            'perolehan_aset' => 'Pembelian Aset',
            'penyusutan' => 'Penyusutan',
            'penghapusan_aset' => 'Penghapusan Aset',
        ];

        return $labels[$this->tipe] ?? $this->tipe;
    }

    /**
     * Kondisi query untuk mengecualikan jurnal yang dibatalkan (voided_at
     * terisi) beserta jurnal baliknya (yang mereferensikan jurnal tersebut)
     * dari laporan/detail buku besar. Dipakai lewat scope tanpaVoid maupun
     * query join yang memakai kolom jurnal_umum, agar duet void+balik (net 0)
     * tidak terlihat sebagai baris ganda.
     */
    public static function kondisiBukanJurnalVoid(): Closure
    {
        return function ($query) {
            $query
                ->whereNull('jurnal_umum.voided_at')
                ->where(function ($q) {
                    $q->where(function ($q) {
                        $q->where('jurnal_umum.ref_type', '!=', self::class)
                            ->orWhereNull('jurnal_umum.ref_type');
                    })->orWhereNotExists(function ($sub) {
                        $sub->selectRaw('1')
                            ->from('jurnal_umum as jurnal_void')
                            ->whereColumn('jurnal_void.id', 'jurnal_umum.ref_id')
                            ->whereNotNull('jurnal_void.voided_at');
                    });
                });
        };
    }

    public function scopeTanpaVoid(Builder $query): Builder
    {
        return $query->where(self::kondisiBukanJurnalVoid());
    }
}
