<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use App\Traits\HasApprovalWorkflow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class KasKeluar extends Model
{
    use BelongsToUser;
    use HasApprovalWorkflow, HasFactory;

    protected $table = 'kas_keluar';

    protected $fillable = [
        'nomor', 'tanggal', 'rekening_id', 'supplier_id', 'keterangan', 'total',
        'pajak_id', 'pajak_nominal', 'grand_total',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'pajak_nominal' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
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
        return $this->hasMany(KasKeluarItem::class, 'kas_keluar_id');
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
}
