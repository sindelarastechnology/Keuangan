<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Chart of Accounts
        Schema::create('akun_perkiraan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 150);
            $table->enum('jenis', ['aset', 'kewajiban', 'modal', 'pendapatan', 'beban']);
            $table->enum('saldo_normal', ['debit', 'kredit']);
            $table->boolean('is_header')->default(false);
            $table->foreignId('parent_id')->nullable()->constrained('akun_perkiraan')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // Banks
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('nomor_rekening', 50)->nullable();
            $table->string('nama_pemilik', 150)->nullable();
            $table->foreignId('akun_id')->constrained('akun_perkiraan');
            $table->decimal('saldo_awal', 18, 2)->default(0);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // Kas
        Schema::create('kas', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->foreignId('akun_id')->constrained('akun_perkiraan');
            $table->decimal('saldo_awal', 18, 2)->default(0);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // Suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 150);
            $table->string('alamat', 255)->nullable();
            $table->string('telepon', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('npwp', 30)->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // Customers
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 150);
            $table->string('alamat', 255)->nullable();
            $table->string('telepon', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('npwp', 30)->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // Produk
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 150);
            $table->string('satuan', 20)->nullable();
            $table->decimal('stok', 18, 2)->default(0);
            $table->decimal('harga_avg', 18, 2)->default(0);
            $table->decimal('harga_jual', 18, 2)->default(0);
            $table->enum('tipe', ['barang', 'jasa'])->default('barang');
            $table->text('keterangan')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // Bahan Baku
        Schema::create('bahan_baku', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 150);
            $table->string('satuan', 20)->nullable();
            $table->decimal('stok', 18, 2)->default(0);
            $table->decimal('harga_avg', 18, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // Daftar Harga per Supplier
        Schema::create('daftar_harga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('bahan_baku_id')->constrained('bahan_baku')->cascadeOnDelete();
            $table->decimal('harga', 18, 2);
            $table->boolean('is_aktif')->default(true);
            $table->unique(['supplier_id', 'bahan_baku_id']);
            $table->timestamps();
        });

        // Pajak
        Schema::create('pajak', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->decimal('rate', 5, 2)->default(11);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // Pengaturan
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Periode Akuntansi
        Schema::create('periode_akuntansi', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->char('bulan', 2);
            $table->char('tahun', 4);
            $table->boolean('is_open')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->date('tanggal_buka')->nullable();
            $table->date('tanggal_tutup')->nullable();
            $table->unique(['bulan', 'tahun']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_akuntansi');
        Schema::dropIfExists('pengaturan');
        Schema::dropIfExists('pajak');
        Schema::dropIfExists('daftar_harga');
        Schema::dropIfExists('bahan_baku');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('kas');
        Schema::dropIfExists('banks');
        Schema::dropIfExists('akun_perkiraan');
    }
};
