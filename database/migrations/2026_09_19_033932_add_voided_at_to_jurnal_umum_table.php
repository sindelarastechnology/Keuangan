<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Penanda waktu saat jurnal dibatalkan. Jurnal void tetap berstatus
        // is_posted (dengan jurnal balik) agar pembalikan meniadakan net 0,
        // tetapi pasangan void+balik disembunyikan dari laporan/detail.
        Schema::table('jurnal_umum', function (Blueprint $table) {
            $table->timestamp('voided_at')->nullable()->after('is_posted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_umum', function (Blueprint $table) {
            $table->dropColumn('voided_at');
        });
    }
};
