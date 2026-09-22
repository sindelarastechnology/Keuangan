<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyusutan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_id')->constrained('asets')->cascadeOnDelete();
            $table->char('periode', 7);
            $table->date('tanggal');
            $table->decimal('beban', 18, 2);
            $table->decimal('akumulasi_setelah', 18, 2);
            $table->decimal('nilai_buku_setelah', 18, 2);
            $table->foreignId('jurnal_id')->nullable()->constrained('jurnal_umum')->nullOnDelete();
            $table->string('keterangan', 255)->nullable();
            $table->unique(['aset_id', 'periode']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyusutan');
    }
};
