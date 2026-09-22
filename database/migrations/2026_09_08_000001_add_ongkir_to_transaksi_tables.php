<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['pembelians', 'penjualans'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->decimal('ongkir', 18, 2)->default(0);
            });
        }
    }

    public function down(): void
    {
        foreach (['pembelians', 'penjualans'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('ongkir');
            });
        }
    }
};
