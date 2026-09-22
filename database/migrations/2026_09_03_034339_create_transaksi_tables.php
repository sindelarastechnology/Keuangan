<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kas Masuk
        Schema::create('kas_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('kas_id')->constrained('kas');
            $table->text('keterangan')->nullable();
            $table->decimal('total', 18, 2)->default(0);
            $table->foreignId('pajak_id')->nullable()->constrained('pajak')->nullOnDelete();
            $table->decimal('pajak_nominal', 18, 2)->default(0);
            $table->decimal('grand_total', 18, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('kas_masuk_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kas_masuk_id')->constrained('kas_masuk')->cascadeOnDelete();
            $table->foreignId('akun_id')->constrained('akun_perkiraan');
            $table->text('keterangan')->nullable();
            $table->decimal('nominal', 18, 2)->default(0);
            $table->timestamps();
        });

        // Kas Keluar
        Schema::create('kas_keluar', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('kas_id')->constrained('kas');
            $table->text('keterangan')->nullable();
            $table->decimal('total', 18, 2)->default(0);
            $table->foreignId('pajak_id')->nullable()->constrained('pajak')->nullOnDelete();
            $table->decimal('pajak_nominal', 18, 2)->default(0);
            $table->decimal('grand_total', 18, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('kas_keluar_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kas_keluar_id')->constrained('kas_keluar')->cascadeOnDelete();
            $table->foreignId('akun_id')->constrained('akun_perkiraan');
            $table->text('keterangan')->nullable();
            $table->decimal('nominal', 18, 2)->default(0);
            $table->timestamps();
        });

        // Mutasi Bank
        Schema::create('mutasi_bank', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('bank_asal_id')->constrained('banks');
            $table->foreignId('bank_tujuan_id')->constrained('banks');
            $table->decimal('nominal', 18, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Pembelian
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->enum('metode_bayar', ['tunai', 'kredit'])->default('tunai');
            $table->foreignId('kas_id')->nullable()->constrained('kas')->nullOnDelete();
            $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('diskon', 18, 2)->default(0);
            $table->enum('diskon_tipe', ['nominal', 'persen'])->default('nominal');
            $table->decimal('diskon_nominal', 18, 2)->default(0);
            $table->foreignId('pajak_id')->nullable()->constrained('pajak')->nullOnDelete();
            $table->decimal('pajak_nominal', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);
            $table->enum('status', ['posted', 'draft'])->default('posted');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('pembelian_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelians')->cascadeOnDelete();
            $table->foreignId('bahan_baku_id')->constrained('bahan_baku');
            $table->decimal('jumlah', 18, 2);
            $table->decimal('harga_satuan', 18, 2);
            $table->decimal('diskon', 18, 2)->default(0);
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->timestamps();
        });

        // Penjualan
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('customer_id')->constrained('customers');
            $table->enum('metode_bayar', ['tunai', 'kredit'])->default('tunai');
            $table->foreignId('kas_id')->nullable()->constrained('kas')->nullOnDelete();
            $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('diskon', 18, 2)->default(0);
            $table->enum('diskon_tipe', ['nominal', 'persen'])->default('nominal');
            $table->decimal('diskon_nominal', 18, 2)->default(0);
            $table->foreignId('pajak_id')->nullable()->constrained('pajak')->nullOnDelete();
            $table->decimal('pajak_nominal', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);
            $table->decimal('hpp_total', 18, 2)->default(0);
            $table->enum('status', ['posted', 'draft'])->default('posted');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('penjualan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualans')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk');
            $table->decimal('jumlah', 18, 2);
            $table->decimal('harga_satuan', 18, 2);
            $table->decimal('diskon', 18, 2)->default(0);
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('hpp', 18, 2)->default(0);
            $table->decimal('hpp_total', 18, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_items');
        Schema::dropIfExists('penjualans');
        Schema::dropIfExists('pembelian_items');
        Schema::dropIfExists('pembelians');
        Schema::dropIfExists('mutasi_bank');
        Schema::dropIfExists('kas_keluar_items');
        Schema::dropIfExists('kas_keluar');
        Schema::dropIfExists('kas_masuk_items');
        Schema::dropIfExists('kas_masuk');
    }
};
