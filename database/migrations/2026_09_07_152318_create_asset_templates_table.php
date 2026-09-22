<?php

use Database\Seeders\AssetTemplateSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_templates', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori', 100)->unique();
            $table->unsignedSmallInteger('masa_manfaat_bulan');
            $table->decimal('persen_residu', 5, 2)->default(0);
            $table->unsignedBigInteger('akun_aset_id')->nullable();
            $table->unsignedBigInteger('akun_akumulasi_id')->nullable();
            $table->unsignedBigInteger('akun_beban_id')->nullable();
            $table->timestamps();

            $table->foreign('akun_aset_id')->references('id')->on('akun_perkiraan')->onDelete('set null');
            $table->foreign('akun_akumulasi_id')->references('id')->on('akun_perkiraan')->onDelete('set null');
            $table->foreign('akun_beban_id')->references('id')->on('akun_perkiraan')->onDelete('set null');
        });

        (new AssetTemplateSeeder)->run();
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_templates');
    }
};
