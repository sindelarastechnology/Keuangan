<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asets', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('keterangan');
            $table->unsignedBigInteger('rekening_id')->nullable()->after('sumber_dana_id');
            $table->unsignedBigInteger('supplier_id')->nullable()->after('rekening_id');
            $table->string('disposisi_alasan')->nullable()->after('status');
            $table->decimal('disposisi_harga_jual', 18, 2)->nullable()->after('disposisi_alasan');
            $table->decimal('disposisi_laba', 18, 2)->nullable()->after('disposisi_harga_jual');
            $table->date('disposisi_tanggal')->nullable()->after('disposisi_laba');
            $table->enum('status', ['aktif', 'nonaktif', 'selesai'])->default('aktif')->change();

            $table->foreign('rekening_id')->references('id')->on('rekenings')->onDelete('set null');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('asets', function (Blueprint $table) {
            $table->dropForeign(['rekening_id']);
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['foto', 'rekening_id', 'supplier_id', 'disposisi_alasan', 'disposisi_harga_jual', 'disposisi_laba', 'disposisi_tanggal']);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->change();
        });
    }
};
