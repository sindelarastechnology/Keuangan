<?php

namespace App\Traits;

use App\Models\AuditTrail;
use App\Models\User;

trait HasApprovalWorkflow
{
    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', 'pending_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }

    public function scopeDraft($query)
    {
        return $query->where('approval_status', 'draft');
    }

    public function approve(User $approver, ?string $reason = null): void
    {
        $this->forceFill([
            'approval_status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'approval_reason' => $reason,
        ])->save();

        if (method_exists($this, 'postApproval')) {
            $this->postApproval();
        }

        AuditTrail::log($this, 'approved', null, ['status' => 'approved'], $reason);
    }

    public function reject(User $rejector, string $reason): void
    {
        $this->forceFill([
            'approval_status' => 'rejected',
            'approved_by' => $rejector->id,
            'approved_at' => now(),
            'approval_reason' => $reason,
        ])->save();

        AuditTrail::log($this, 'rejected', null, ['status' => 'rejected'], $reason);
    }

    public function requestApproval(): void
    {
        $oldStatus = $this->approval_status;
        $this->forceFill(['approval_status' => 'pending_review'])->save();
        AuditTrail::log($this, 'updated', ['status' => $oldStatus], ['status' => 'pending_review']);
    }

    public function revertToDraft(): void
    {
        $this->forceFill([
            'approval_status' => 'draft',
            'approved_by' => null,
            'approved_at' => null,
            'approval_reason' => null,
        ])->save();

        AuditTrail::log($this, 'updated', ['status' => 'pending_review'], ['status' => 'draft']);
    }

    public function canApprove(User $user): bool
    {
        // Single-role: seluruh user yang login berhak menyetujui.
        return true;
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isPendingApproval(): bool
    {
        return $this->approval_status === 'pending_review';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    public function isDraft(): bool
    {
        return $this->approval_status === 'draft';
    }
}
