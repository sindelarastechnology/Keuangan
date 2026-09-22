<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use App\Traits\HasApprovalWorkflow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

class Penjualan extends Model
{
    use BelongsToUser;
    use HasApprovalWorkflow, HasFactory;

    protected $table = 'penjualans';

    protected $fillable = [
        'nomor', 'tanggal', 'customer_id', 'gudang_id', 'metode_bayar',
        'rekening_id', 'subtotal', 'diskon', 'diskon_tipe',
        'diskon_nominal', 'pajak_id', 'pajak_nominal', 'ongkir', 'total',
        'hpp_total', 'status', 'keterangan', 'created_by', 'updated_by',
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
        'hpp_total' => 'decimal:2',
        'sync_harga' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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
        return $this->hasMany(PenjualanItem::class, 'penjualan_id');
    }

    public function retur(): HasMany
    {
        return $this->hasMany(ReturPenjualan::class, 'penjualan_id');
    }

    public function bbPiutang(): HasMany
    {
        return $this->hasMany(BbPiutang::class, 'penjualan_id');
    }

    public function jurnal(): MorphMany
    {
        return $this->morphMany(JurnalUmum::class, 'ref');
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
     * Sisa piutang yang belum dilunasi untuk transaksi kredit ini.
     * Dihitung dari selisih debit-kredit baris bb_piutang yang ter-atribusi.
     */
    public function getSisaPiutangAttribute(): float
    {
        return (float) BbPiutang::where('penjualan_id', $this->id)->sum(DB::raw('debit - kredit'));
    }

    /**
     * Apakah pencatatan piutang transaksi ini sudah benar-benar ter-atribusi
     * ke faktur (baris bb_piutang memuat penjualan_id). Transaksi lama
     * (sebelum fitur pelunasan per faktur) bernilai false.
     */
    public function getPiutangTerverifikasiAttribute(): bool
    {
        return BbPiutang::where('penjualan_id', $this->id)->exists();
    }
}
