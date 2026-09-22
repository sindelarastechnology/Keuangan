<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        DB::table('suppliers')->updateOrInsert(
            ['kode' => 'UMUM'],
            ['nama' => 'UMUM', 'is_aktif' => true, 'created_at' => $now, 'updated_at' => $now]
        );

        DB::table('customers')->updateOrInsert(
            ['kode' => 'UMUM'],
            ['nama' => 'UMUM', 'is_aktif' => true, 'created_at' => $now, 'updated_at' => $now]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('suppliers')->where('kode', 'UMUM')->delete();
        DB::table('customers')->where('kode', 'UMUM')->delete();
    }
};
