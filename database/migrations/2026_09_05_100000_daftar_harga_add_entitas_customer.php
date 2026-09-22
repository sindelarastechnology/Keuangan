<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        // 1. Tambah kolom entitas (supplier / customer)
        Schema::table('daftar_harga', function (Blueprint $table) {
            $table->string('entitas', 20)->default('supplier')->after('id');
        });

        // 2. Tambah kolom customer_id (nullable, hanya dipakai saat entitas = customer)
        Schema::table('daftar_harga', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('supplier_id');
        });

        // 3. Jadikan supplier_id nullable (karena kini bisa berisi data customer)
        if ($driver === 'mysql') {
            Schema::table('daftar_harga', function (Blueprint $table) {
                $table->dropForeign(['supplier_id']);
                $table->unsignedBigInteger('supplier_id')->nullable()->change();
                $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete();
            });
        } else {
            Schema::table('daftar_harga', function (Blueprint $table) {
                $table->unsignedBigInteger('supplier_id')->nullable()->change();
            });
        }

        // 4. Foreign key customer_id (MySQL only; SQLite handled via konvensi/native rebuild)
        if ($driver === 'mysql') {
            Schema::table('daftar_harga', function (Blueprint $table) {
                $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
                $table->index(['entitas', 'supplier_id', 'customer_id', 'barang_id']);
            });
        } else {
            Schema::table('daftar_harga', function (Blueprint $table) {
                $table->index(['entitas', 'supplier_id', 'customer_id', 'barang_id']);
            });
        }

        // 5. Backfill data existing -> entitas='supplier'
        DB::table('daftar_harga')->whereNull('entitas')->update(['entitas' => 'supplier']);
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            Schema::table('daftar_harga', function (Blueprint $table) {
                $table->dropForeign(['customer_id']);
                $table->dropIndex(['entitas', 'supplier_id', 'customer_id', 'barang_id']);
                $table->dropForeign(['supplier_id']);
                $table->unsignedBigInteger('supplier_id')->nullable(false)->change();
                $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete();
            });
        } else {
            Schema::table('daftar_harga', function (Blueprint $table) {
                $table->dropIndex(['entitas', 'supplier_id', 'customer_id', 'barang_id']);
                $table->unsignedBigInteger('supplier_id')->nullable(false)->change();
            });
        }

        Schema::table('daftar_harga', function (Blueprint $table) {
            $table->dropColumn(['entitas', 'customer_id']);
        });
    }
};
