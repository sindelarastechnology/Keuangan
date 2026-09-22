<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jurnal Umum
        Schema::create('jurnal_umum', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 50)->unique();
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->enum('tipe', [
                'kas_masuk', 'kas_keluar', 'mutasi_bank', 'pembelian',
                'penjualan', 'manual', 'tutup_buku', 'hpp', 'pembayaran',
            ])->default('manual');
            $table->nullableMorphs('ref'); // ref_type + ref_id
            $table->boolean('is_posted')->default(true);
            $table->timestamps();
        });

        Schema::create('jurnal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_id')->constrained('jurnal_umum')->cascadeOnDelete();
            $table->foreignId('akun_id')->constrained('akun_perkiraan');
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('kredit', 18, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Buku Besar Pembantu Piutang
        Schema::create('bb_piutang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('jurnal_id')->nullable()->constrained('jurnal_umum')->nullOnDelete();
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('kredit', 18, 2)->default(0);
            $table->decimal('saldo', 18, 2)->default(0);
            $table->timestamps();
        });

        // Buku Besar Pembantu Hutang
        Schema::create('bb_hutang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('jurnal_id')->nullable()->constrained('jurnal_umum')->nullOnDelete();
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('kredit', 18, 2)->default(0);
            $table->decimal('saldo', 18, 2)->default(0);
            $table->timestamps();
        });

        // Buku Besar Pembantu Persediaan
        Schema::create('bb_persediaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_baku_id')->constrained('bahan_baku');
            $table->nullableMorphs('ref'); // ref_type + ref_id (pembelian/penjualan)
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->decimal('masuk_qty', 18, 2)->default(0);
            $table->decimal('masuk_harga', 18, 2)->default(0);
            $table->decimal('keluar_qty', 18, 2)->default(0);
            $table->decimal('keluar_harga', 18, 2)->default(0);
            $table->decimal('saldo_qty', 18, 2)->default(0);
            $table->decimal('saldo_harga', 18, 2)->default(0);
            $table->decimal('ratt', 18, 2)->default(0);
            $table->timestamps();
        });

        // Tutup Buku
        Schema::create('tutup_buku', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_akuntansi');
            $table->date('tanggal');
            $table->decimal('laba_rugi', 18, 2)->default(0);
            $table->decimal('total_pendapatan', 18, 2)->default(0);
            $table->decimal('total_beban', 18, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutup_buku');
        Schema::dropIfExists('bb_persediaan');
        Schema::dropIfExists('bb_hutang');
        Schema::dropIfExists('bb_piutang');
        Schema::dropIfExists('jurnal_items');
        Schema::dropIfExists('jurnal_umum');
    }
};
