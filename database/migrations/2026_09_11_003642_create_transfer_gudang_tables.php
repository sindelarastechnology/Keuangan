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
        Schema::create('transfer_gudang', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('gudang_asal')->constrained('gudangs');
            $table->foreignId('gudang_tujuan')->constrained('gudangs');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['posted', 'draft'])->default('posted');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('transfer_gudang_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_gudang_id')->constrained('transfer_gudang')->cascadeOnDelete();
            $table->foreignId('barang_id')->nullable()->constrained('barang')->nullOnDelete();
            $table->decimal('jumlah', 18, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_gudang_items');
        Schema::dropIfExists('transfer_gudang');
    }
};
