<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use App\Traits\HasApprovalWorkflow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;

class Pembelian extends Model
{
    use BelongsToUser;
    use HasApprovalWorkflow, HasFactory;

    protected $table = 'pembelians';

    protected $fillable = [
        'nomor', 'tanggal', 'supplier_id', 'gudang_id', 'metode_bayar',
        'rekening_id', 'subtotal', 'diskon', 'diskon_tipe',
        'diskon_nominal', 'pajak_id', 'pajak_nominal', 'ongkir', 'total',
        'status', 'keterangan', 'created_by', 'updated_by',
        'sync_harga',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'subtotal' => 'decimal:2',
        'diskon' => 'decimal:2',
        'diskon_nominal' => 'decimal:2',
        'pajak_nominal' => 'decimal:2',
        'ongkir' => 'decimal:2',
        'total' => 'decimal:2',
        'sync_harga' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class);
    }

    public function rekening(): BelongsTo
    {
        return $this->belongsTo(Rekening::class, 'rekening_id');
    }

    public function pajak(): BelongsTo
    {
        return $this->belongsTo(Pajak::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PembelianItem::class, 'pembelian_id');
    }

    public function retur(): HasMany
    {
        return $this->hasMany(ReturPembelian::class, 'pembelian_id');
    }

    public function bbHutang(): HasMany
    {
        return $this->hasMany(BbHutang::class, 'pembelian_id');
    }

    public function jurnal(): MorphOne
    {
        return $this->morphOne(JurnalUmum::class, 'ref');
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
        return $this->hasMany(AuditTrail::class, 'auditable_id')
            ->where('auditable_type', self::class)
            ->orderByDesc('created_at');
    }

    /**
     * Sisa hutang yang belum dibayar untuk transaksi kredit ini.
     * Dihitung dari selisih kredit-debit baris bb_hutang yang ter-atribusi.
     */
    public function getSisaHutangAttribute(): float
    {
        return (float) BbHutang::where('pembelian_id', $this->id)->sum(DB::raw('kredit - debit'));
    }

    /**
     * Apakah pencatatan hutang transaksi ini sudah benar-benar ter-atribusi
     * ke faktur (baris bb_hutang memuat pembelian_id). Transaksi lama
     * (sebelum fitur pelunasan per faktur) bernilai false.
     */
    public function getHutangTerverifikasiAttribute(): bool
    {
        return BbHutang::where('pembelian_id', $this->id)->exists();
    }
}
