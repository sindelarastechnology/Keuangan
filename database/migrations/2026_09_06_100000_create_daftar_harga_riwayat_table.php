<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_harga_riwayat', function (Blueprint $table) {
            $table->id();
            $table->string('entitas', 20);
            $table->unsignedBigInteger('barang_id');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('tipe', 20);
            $table->decimal('harga_lama', 15, 2)->nullable();
            $table->decimal('harga_baru', 15, 2)->nullable();
            $table->decimal('min_qty_lama', 15, 2)->nullable();
            $table->decimal('max_qty_lama', 15, 2)->nullable();
            $table->decimal('min_qty_baru', 15, 2)->nullable();
            $table->decimal('max_qty_baru', 15, 2)->nullable();
            $table->date('tanggal_mulai_lama')->nullable();
            $table->date('tanggal_mulai_baru')->nullable();
            $table->string('catatan')->nullable();
            $table->timestamps();

            $table->index(['entitas', 'barang_id']);
            $table->index(['entitas', 'supplier_id', 'barang_id']);
            $table->index(['entitas', 'customer_id', 'barang_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_harga_riwayat');
    }
};
