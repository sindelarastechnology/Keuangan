<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Jembatan tambahan (forward-only, aman) untuk DB MySQL yang ketinggalan satu
 * generasi skema: menambahkan kolom `approval_status` pada tabel transaksi yang
 * dibutuhkan dashboard (pending_review / draft / disetujui).
 *
 * Sifat:
 *  - hasTable + hasColumn guard  -> idempotent, aman dijalankan berulang
 *  - HANYA menambah kolom        -> tidak menyentuh data/baris/tabel lain
 *  - tidak drop, tidak migrate penuh
 */
return new class extends Migration
{
    /**
     * Tabel tenant transaksi yang pembaca dashboard pakai kolom approval_status.
     *
     * @var array<int, string>
     */
    private const TABLES = [
        'kas_masuk',
        'kas_keluar',
        'jurnal_umum',
        'penjualans',
        'pembelians',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'approval_status')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->string('approval_status', 20)
                    ->default('approved')
                    ->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'approval_status')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) {
                if (Schema::hasIndex($table, ['approval_status'])) {
                    $blueprint->dropIndex(['approval_status']);
                }
                $blueprint->dropColumn('approval_status');
            });
        }
    }
};
