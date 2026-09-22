<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use App\Traits\HasApprovalWorkflow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ReturPenjualan extends Model
{
    use BelongsToUser;
    use HasApprovalWorkflow;

    protected $table = 'retur_penjualan';

    protected $fillable = [
        'nomor', 'tanggal', 'penjualan_id', 'keterangan', 'status',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReturPenjualanItem::class, 'retur_penjualan_id');
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
}
