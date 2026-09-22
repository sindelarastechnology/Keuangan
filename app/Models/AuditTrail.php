<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditTrail extends Model
{
    use BelongsToUser;

    protected $table = 'audit_trails';

    protected $fillable = [
        'auditable_type', 'auditable_id', 'action',
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

    public static function log($auditable, string $action, ?array $oldValues = null, ?array $newValues = null, ?string $reason = null, ?string $ipAddress = null)
    {
        return self::create([
            'user_id' => auth()->id(),
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
