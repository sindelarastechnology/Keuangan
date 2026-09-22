<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Perbaiki basis data yang berjalan (tidak menjalankan seluruh pending
 * migration — hanya menambahkan kolom yang benar-benar diperlukan) sehingga
 * login Google dan fitur paket berfungsi tanpa mengubah tabel lain.
 *
 * Semua penambahan dijaga dengan hasColumn agar aman dijalankan berulang kali
 * dan tidak bertabrakan dengan skema yang sudah ada di server.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->unique();
            }

            if (! Schema::hasColumn('users', 'plan')) {
                $table->string('plan', 20)->default('pro');
            }

            if (! Schema::hasColumn('users', 'plan_expires_at')) {
                $table->timestamp('plan_expires_at')->nullable();
            }

            if (! Schema::hasColumn('users', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable();
            }

            if (! Schema::hasColumn('users', 'status')) {
                $table->string('status', 20)->default('aktif');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['google_id', 'plan', 'plan_expires_at', 'trial_ends_at', 'status'];

            foreach (array_reverse($columns) as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
