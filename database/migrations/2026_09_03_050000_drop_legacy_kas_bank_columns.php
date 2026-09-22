<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus kolom kas/bank lama yang sudah digantikan rekening_id / rekening_asal_id / rekening_tujuan_id.
        $dropCols = [
            'kas_masuk' => ['kas_id'],
            'kas_keluar' => ['kas_id'],
            'mutasi_bank' => ['bank_asal_id', 'bank_tujuan_id'],
            'pembelians' => ['kas_id', 'bank_id'],
            'penjualans' => ['kas_id', 'bank_id'],
        ];

        foreach ($dropCols as $table => $cols) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            Schema::table($table, function (Blueprint $table) use ($cols) {
                foreach ($cols as $col) {
                    if (Schema::hasColumn($table->getTable(), $col)) {
                        $table->dropConstrainedForeignId($col);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        // Tidak ada rollback otomatis; menambah kembali kolom tidak praktis karena data asal telah disalin.
    }
};
