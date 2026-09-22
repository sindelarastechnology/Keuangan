<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel rekening: penggabungan kas + bank menjadi satu entitas.
        Schema::create('rekenings', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['kas', 'bank'])->default('kas');
            $table->string('nama', 150);
            $table->string('nomor_rekening', 50)->nullable();
            $table->string('nama_pemilik', 150)->nullable();
            $table->foreignId('akun_id')->constrained('akun_perkiraan');
            $table->decimal('saldo_awal', 18, 2)->default(0);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // Salin data kas ke rekening (jenis = kas), pertahankan id.
        if (Schema::hasTable('kas')) {
            $kasRows = DB::table('kas')->get();
            foreach ($kasRows as $row) {
                DB::table('rekenings')->insert([
                    'id' => $row->id,
                    'jenis' => 'kas',
                    'nama' => $row->nama,
                    'akun_id' => $row->akun_id,
                    'saldo_awal' => $row->saldo_awal,
                    'is_aktif' => $row->is_aktif,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }
        }

        // Salin data bank ke rekening (jenis = bank), catat pemetaan id lama -> baru.
        $bankMap = [];
        if (Schema::hasTable('banks')) {
            $bankRows = DB::table('banks')->get();
            $nextId = DB::table('rekenings')->max('id') ?: 0;
            foreach ($bankRows as $row) {
                $newId = ++$nextId;
                DB::table('rekenings')->insert([
                    'id' => $newId,
                    'jenis' => 'bank',
                    'nama' => $row->nama,
                    'nomor_rekening' => $row->nomor_rekening,
                    'nama_pemilik' => $row->nama_pemilik,
                    'akun_id' => $row->akun_id,
                    'saldo_awal' => $row->saldo_awal,
                    'is_aktif' => $row->is_aktif,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
                $bankMap[$row->id] = $newId;
            }
        }

        $addFk = function (Blueprint $table, string $column) {
            $table->foreignId($column)->nullable()->after('id')->constrained('rekenings')->nullOnDelete();
        };

        // kas_masuk & kas_keluar: kas_id -> rekening_id
        foreach (['kas_masuk', 'kas_keluar'] as $t) {
            if (! Schema::hasTable($t)) {
                continue;
            }
            Schema::table($t, function (Blueprint $table) use ($addFk) {
                $addFk($table, 'rekening_id');
            });
            $rows = DB::table($t)->get();
            foreach ($rows as $row) {
                DB::table($t)->where('id', $row->id)->update([
                    'rekening_id' => $row->kas_id,
                ]);
            }
        }

        // mutasi_bank: bank_asal_id/bank_tujuan_id -> rekening_asal_id/rekening_tujuan_id
        if (Schema::hasTable('mutasi_bank')) {
            Schema::table('mutasi_bank', function (Blueprint $table) {
                $table->foreignId('rekening_asal_id')->nullable()->after('tanggal')->constrained('rekenings')->nullOnDelete();
                $table->foreignId('rekening_tujuan_id')->nullable()->after('rekening_asal_id')->constrained('rekenings')->nullOnDelete();
            });
            $rows = DB::table('mutasi_bank')->get();
            foreach ($rows as $row) {
                DB::table('mutasi_bank')->where('id', $row->id)->update([
                    'rekening_asal_id' => $bankMap[$row->bank_asal_id] ?? $row->bank_asal_id,
                    'rekening_tujuan_id' => $bankMap[$row->bank_tujuan_id] ?? $row->bank_tujuan_id,
                ]);
            }
        }

        // pembelians & penjualans: kas_id/bank_id -> rekening_id (prioritaskan bank)
        foreach (['pembelians', 'penjualans'] as $t) {
            if (! Schema::hasTable($t)) {
                continue;
            }
            Schema::table($t, function (Blueprint $table) use ($addFk) {
                $addFk($table, 'rekening_id');
            });
            $rows = DB::table($t)->get();
            foreach ($rows as $row) {
                $rekeningId = null;
                if ($row->bank_id) {
                    $rekeningId = $bankMap[$row->bank_id] ?? $row->bank_id;
                } elseif ($row->kas_id) {
                    $rekeningId = $row->kas_id;
                }
                if ($rekeningId !== null) {
                    DB::table($t)->where('id', $row->id)->update(['rekening_id' => $rekeningId]);
                }
            }
        }
    }

    public function down(): void
    {
        foreach (['pembelians', 'penjualans'] as $t) {
            if (Schema::hasTable($t) && Schema::hasColumn($t, 'rekening_id')) {
                Schema::table($t, fn (Blueprint $table) => $table->dropConstrainedForeignId('rekening_id'));
            }
        }
        foreach (['kas_masuk', 'kas_keluar'] as $t) {
            if (Schema::hasTable($t) && Schema::hasColumn($t, 'rekening_id')) {
                Schema::table($t, fn (Blueprint $table) => $table->dropConstrainedForeignId('rekening_id'));
            }
        }
        if (Schema::hasTable('mutasi_bank')) {
            if (Schema::hasColumn('mutasi_bank', 'rekening_tujuan_id')) {
                Schema::table('mutasi_bank', fn (Blueprint $table) => $table->dropConstrainedForeignId('rekening_tujuan_id'));
            }
            if (Schema::hasColumn('mutasi_bank', 'rekening_asal_id')) {
                Schema::table('mutasi_bank', fn (Blueprint $table) => $table->dropConstrainedForeignId('rekening_asal_id'));
            }
        }
        Schema::dropIfExists('rekenings');
    }
};
