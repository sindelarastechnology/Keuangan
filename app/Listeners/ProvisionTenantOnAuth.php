<?php

namespace App\Listeners;

use App\Services\ProvisionTenant;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Verified;

/**
 * Pastikan data master tenant tersedia saat user pertama kali login
 * atau diverifikasi. Idempotent — hanya jalan bila belum ada chart of accounts.
 */
class ProvisionTenantOnAuth
{
    /**
     * Handle login or email-verified events.
     */
    public function handle(Login|Verified $event): void
    {
        $user = $event instanceof Login ? $event->user : $event->user;

        if ($user) {
            ProvisionTenant::provision($user);
        }
    }
}
