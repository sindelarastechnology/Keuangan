<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\ProvisionTenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@keuangan.test'],
            ['name' => 'Administrator', 'password' => Hash::make('admin123'), 'email_verified_at' => now(), 'status' => 'aktif', 'plan' => 'pro']
        )->refresh();

        // Data master milik tenant admin.
        Auth::login($admin);
        ProvisionTenant::provision($admin);
    }
}
