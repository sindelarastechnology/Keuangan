<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ===== Perubahan Stok =====
        Schema::create('perubahan_stok', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 50)->unique();
            $table->date('tanggal');
            $table->enum('jenis', ['rusak', 'hilang', 'salah', 'lebih', 'lainnya'])->default('lainnya');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['posted', 'draft'])->default('posted');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('approval_status', ['draft', 'pending_review', 'approved', 'rejected'])->default('approved');
            $table->text('approval_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('perubahan_stok_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perubahan_stok_id')->constrained('perubahan_stok')->cascadeOnDelete();
            $table->foreignId('barang_id')->nullable()->constrained('barang')->nullOnDelete();
            $table->enum('arah', ['masuk', 'keluar'])->default('masuk');
            $table->decimal('jumlah', 18, 2);
            $table->decimal('harga', 18, 2)->default(0);
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // ===== Stok Opname =====
        Schema::create('stok_opname', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 50)->unique();
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['posted', 'draft'])->default('posted');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('approval_status', ['draft', 'pending_review', 'approved', 'rejected'])->default('approved');
            $table->text('approval_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('stok_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_opname_id')->constrained('stok_opname')->cascadeOnDelete();
            $table->foreignId('barang_id')->nullable()->constrained('barang')->nullOnDelete();
            $table->decimal('stok_sistem', 18, 2)->default(0);
            $table->decimal('stok_fisik', 18, 2)->default(0);
            $table->decimal('selisih', 18, 2)->default(0);
            $table->decimal('harga', 18, 2)->default(0);
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // ===== Retur Penjualan =====
        Schema::create('retur_penjualan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('penjualan_id')->nullable()->constrained('penjualans')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['posted', 'draft'])->default('posted');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('approval_status', ['draft', 'pending_review', 'approved', 'rejected'])->default('approved');
            $table->text('approval_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('retur_penjualan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retur_penjualan_id')->constrained('retur_penjualan')->cascadeOnDelete();
            $table->foreignId('penjualan_item_id')->nullable()->constrained('penjualan_items')->nullOnDelete();
            $table->foreignId('barang_id')->nullable()->constrained('barang')->nullOnDelete();
            $table->decimal('jumlah', 18, 2);
            $table->decimal('harga_satuan', 18, 2);
            $table->decimal('hpp', 18, 2)->default(0);
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('hpp_total', 18, 2)->default(0);
            $table->timestamps();
        });

        // ===== Retur Pembelian =====
        Schema::create('retur_pembelian', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('pembelian_id')->nullable()->constrained('pembelians')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['posted', 'draft'])->default('posted');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('approval_status', ['draft', 'pending_review', 'approved', 'rejected'])->default('approved');
            $table->text('approval_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('retur_pembelian_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retur_pembelian_id')->constrained('retur_pembelian')->cascadeOnDelete();
            $table->foreignId('pembelian_item_id')->nullable()->constrained('pembelian_items')->nullOnDelete();
            $table->foreignId('barang_id')->nullable()->constrained('barang')->nullOnDelete();
            $table->decimal('jumlah', 18, 2);
            $table->decimal('harga_satuan', 18, 2);
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retur_pembelian_items');
        Schema::dropIfExists('retur_pembelian');
        Schema::dropIfExists('retur_penjualan_items');
        Schema::dropIfExists('retur_penjualan');
        Schema::dropIfExists('stok_opname_items');
        Schema::dropIfExists('stok_opname');
        Schema::dropIfExists('perubahan_stok_items');
        Schema::dropIfExists('perubahan_stok');
    }
};
