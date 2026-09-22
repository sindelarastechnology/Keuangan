<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Just add the new unique index. MySQL allows multiple unique indexes.
        // The old unique index on supplier_id only will remain but is redundant.
        Schema::table('daftar_harga', function (Blueprint $table) {
            $table->unique(['supplier_id', 'barang_id'], 'daftar_harga_supplier_id_barang_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('daftar_harga', function (Blueprint $table) {
            $table->dropUnique('daftar_harga_supplier_id_barang_id_unique');
        });
    }
};
