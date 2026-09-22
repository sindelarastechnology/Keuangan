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
        Schema::create('satuan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50)->unique();
            $table->timestamps();
        });

        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->timestamps();
        });

        foreach (DB::table('barang')->distinct()->pluck('satuan')->filter() as $nama) {
            DB::table('satuan')->insertOrIgnore(['nama' => $nama, 'created_at' => now(), 'updated_at' => now()]);
        }

        foreach (DB::table('barang')->distinct()->pluck('kategori')->filter() as $nama) {
            DB::table('kategori')->insertOrIgnore(['nama' => $nama, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori');
        Schema::dropIfExists('satuan');
    }
};
