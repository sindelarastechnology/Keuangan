<?php

namespace App\Models;

use App\Services\TenantDatabase;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditTrail extends Model
{
    use BelongsToUser;

    protected $table = 'audit_trails';

    /**
     * `user_id` wajib ada di sini: kolomnya NOT NULL, dan tanpa ini nilai dari
     * AuditTrail::log() dibuang diam-diam oleh mass-assignment guard sehingga
     * penulisan dari luar HTTP (command/queue) gagal dengan
     * "NOT NULL constraint failed".
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id', 'auditable_type', 'auditable_id', 'action',
        'old_values', 'new_values', 'reason', 'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * Catat jejak audit untuk sebuah model.
     *
     * Pemilik ditentukan dari user yang terautentikasi, dengan fallback ke
     * tenant yang sedang aktif (command/queue tidak punya Auth). Tanpa
     * fallback, penulisan dari luar HTTP gagal karena `user_id` NOT NULL.
     */
    public static function log($auditable, string $action, ?array $oldValues = null, ?array $newValues = null, ?string $reason = null, ?string $ipAddress = null)
    {
        return self::create([
            'user_id' => auth()->id() ?? TenantDatabase::activeUserId(),
            'auditable_type' => get_class($auditable),
            'auditable_id' => $auditable->id,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'reason' => $reason,
            'ip_address' => $ipAddress ?? request()->ip(),
        ]);
    }

    public function getChangesSummaryAttribute(): string
    {
        if (! $this->old_values || ! $this->new_values) {
            return ucfirst($this->action).' by '.($this->user?->name ?? 'Unknown');
        }

        $changes = [];
        foreach ($this->new_values as $key => $newVal) {
            $oldVal = $this->old_values[$key] ?? null;
            if ($oldVal !== $newVal) {
                $changes[] = "$key: $oldVal → $newVal";
            }
        }

        return count($changes) > 0 ? implode(', ', array_slice($changes, 0, 3)) : 'No changes';
    }
}
