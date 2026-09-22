<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->boolean('sync_harga')->default(false)->after('keterangan');
            $table->enum('status', ['posted', 'draft', 'pending'])->default('posted')->change();
        });

        Schema::table('pembelians', function (Blueprint $table) {
            $table->boolean('sync_harga')->default(false)->after('keterangan');
            $table->enum('status', ['posted', 'draft', 'pending'])->default('posted')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Status draft (pending) belumlah berdampak finansial; kembalikan ke posted.
        DB::table('penjualans')->where('status', 'pending')->update(['status' => 'posted']);
        DB::table('pembelians')->where('status', 'pending')->update(['status' => 'posted']);

        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn('sync_harga');
            $table->enum('status', ['posted', 'draft'])->default('posted')->change();
        });

        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn('sync_harga');
            $table->enum('status', ['posted', 'draft'])->default('posted')->change();
        });
    }
};
