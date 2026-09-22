<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembelian_items', function (Blueprint $table) {
            $table->decimal('harga_net', 18, 2)->default(0)->after('harga_satuan');
        });
    }

    public function down(): void
    {
        Schema::table('pembelian_items', function (Blueprint $table) {
            $table->dropColumn('harga_net');
        });
    }
};
