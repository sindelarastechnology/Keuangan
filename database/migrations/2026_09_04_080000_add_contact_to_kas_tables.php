<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kas_masuk', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('rekening_id')->constrained('customers')->nullOnDelete();
        });

        Schema::table('kas_keluar', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('rekening_id')->constrained('suppliers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('kas_masuk', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
        });

        Schema::table('kas_keluar', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
        });
    }
};
