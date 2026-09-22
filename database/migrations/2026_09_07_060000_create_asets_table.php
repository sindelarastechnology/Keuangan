<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asets', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 150);
            $table->string('kategori', 100)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('lokasi', 100)->nullable();
            $table->date('tanggal_perolehan');
            $table->decimal('harga_perolehan', 18, 2);
            $table->decimal('nilai_residu', 18, 2)->default(0);
            $table->unsignedSmallInteger('masa_manfaat_bulan');
            $table->foreignId('akun_aset_id')->constrained('akun_perkiraan');
            $table->foreignId('akun_akumulasi_id')->constrained('akun_perkiraan');
            $table->foreignId('akun_beban_id')->constrained('akun_perkiraan');
            $table->foreignId('sumber_dana_id')->nullable()->constrained('akun_perkiraan')->nullOnDelete();
            $table->boolean('catat_perolehan')->default(false);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asets');
    }
};
