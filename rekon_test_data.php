<?php

/**
 * Script Rekonsiliasi Data Test
 * Jalankan dari root project keuangan:
 *   php rekon_test_data.php           <- mode analisis saja
 *   php rekon_test_data.php --fix     <- mode fix (eksekusi perbaikan)
 */
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\AkunPerkiraan;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\Rekening;
use App\Services\JournalService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

echo "=== DUMP DATA SAAT INI ===\n\n";

echo "--- REKENING ---\n";
$rekenings = DB::table('rekenings')->get();
foreach ($rekenings as $r) {
    echo "ID:{$r->id} | {$r->nama} | saldo_awal:{$r->saldo_awal} | akun_id:{$r->akun_id}\n";
}

echo "\n--- JURNAL UMUM ---\n";
$jurnals = DB::table('jurnal_umum')->orderBy('id')->get();
foreach ($jurnals as $j) {
    echo "ID:{$j->id} | {$j->nomor} | {$j->keterangan} | posted:{$j->is_posted} | ref:{$j->ref_type}#{$j->ref_id}\n";
}

echo "\n--- JURNAL ITEMS ---\n";
$items = DB::table('jurnal_items')
    ->join('akun_perkiraan', 'akun_perkiraan.id', '=', 'jurnal_items.akun_id')
    ->select('jurnal_items.*', 'akun_perkiraan.kode', 'akun_perkiraan.nama as akun_nama')
    ->orderBy('jurnal_items.jurnal_id')
    ->get();
foreach ($items as $ji) {
    echo "  jrn:{$ji->jurnal_id} | {$ji->kode} {$ji->akun_nama} | D:{$ji->debit} K:{$ji->kredit}\n";
}

echo "\n--- BARANG ---\n";
$barangs = DB::table('barang')->get();
foreach ($barangs as $b) {
    echo "ID:{$b->id} | {$b->nama} | stok:{$b->stok}\n";
}

echo "\n--- BB PERSEDIAAN ---\n";
$bbs = DB::table('bb_persediaan')->orderBy('id')->get();
foreach ($bbs as $bb) {
    echo "ID:{$bb->id} | barang_id:{$bb->barang_id} | {$bb->tanggal} | {$bb->keterangan} | masuk:{$bb->masuk_qty} | keluar:{$bb->keluar_qty} | saldo:{$bb->saldo_qty}\n";
}

echo "\n=== ANALISIS ===\n";

echo "\n--- Cek Opening Balance Journal ---\n";
foreach ($rekenings as $r) {
    if ($r->saldo_awal > 0) {
        $existingJurnal = DB::table('jurnal_umum')
            ->where('ref_type', 'opening_balance')
            ->where('ref_id', $r->id)
            ->first();

        if ($existingJurnal) {
            echo "REK {$r->id} ({$r->nama}): sudah ada opening balance jurnal ID:{$existingJurnal->id}\n";
        } else {
            echo "REK {$r->id} ({$r->nama}) BELUM punya opening balance journal! saldo_awal:{$r->saldo_awal}\n";
        }
    }
}

echo "\n--- Cek Akun 114 di jurnal ---\n";
$akun114 = AkunPerkiraan::where('kode', '114')->first();
$akun115 = AkunPerkiraan::where('kode', '115')->first();
if ($akun114 && $akun115) {
    $items114 = DB::table('jurnal_items')->where('akun_id', $akun114->id)->count();
    echo "Akun 114 (id:{$akun114->id}) ada di {$items114} jurnal items\n";
    echo "Akun 115 id:{$akun115->id}\n";
} else {
    echo "Akun 114 atau 115 tidak ditemukan\n";
}

echo "\n--- Cek Missing BB Persediaan dari Penjualan ---\n";
$penjualans = DB::table('penjualan')->get();
foreach ($penjualans as $pj) {
    $pjItems = DB::table('penjualan_items')->where('penjualan_id', $pj->id)->get();
    foreach ($pjItems as $pjItem) {
        $bbKeluar = DB::table('bb_persediaan')
            ->where('barang_id', $pjItem->barang_id)
            ->where('keterangan', 'like', '%'.$pj->nomor.'%')
            ->where('qty_keluar', '>', 0)
            ->count();
        if ($bbKeluar == 0) {
            $barang = DB::table('barang')->where('id', $pjItem->barang_id)->first();
            echo "MISSING: penjualan {$pj->nomor} | barang:".($barang ? $barang->nama : 'id:'.$pjItem->barang_id)." | qty:{$pjItem->qty}\n";
        }
    }
}

echo "\n--- Trial Balance Cepat ---\n";
$totalDebit = DB::table('jurnal_items')
    ->join('jurnal_umum', 'jurnal_umum.id', '=', 'jurnal_items.jurnal_id')
    ->where('jurnal_umum.is_posted', true)
    ->sum('debit');
$totalKredit = DB::table('jurnal_items')
    ->join('jurnal_umum', 'jurnal_umum.id', '=', 'jurnal_items.jurnal_id')
    ->where('jurnal_umum.is_posted', true)
    ->sum('kredit');
echo "Total Debit: {$totalDebit} | Total Kredit: {$totalKredit} | Selisih: ".($totalDebit - $totalKredit)."\n";

// ==== FIX MODE ====
if (in_array('--fix', $argv ?? [])) {
    echo "\n=== MENJALANKAN FIX ===\n";
    DB::beginTransaction();
    try {
        $tanggalAwal = '2026-09-03';
        $akunModal = AkunPerkiraan::where('kode', '31')->first();

        // FIX 1: Opening balance journal untuk rekening yang belum punya
        foreach ($rekenings as $r) {
            if ($r->saldo_awal <= 0) {
                continue;
            }

            $existingJurnal = DB::table('jurnal_umum')
                ->where('ref_type', 'opening_balance')
                ->where('ref_id', $r->id)
                ->first();

            if ($existingJurnal) {
                echo "SKIP REK {$r->id}: sudah ada jurnal\n";

                continue;
            }

            $rekModel = Rekening::find($r->id);
            if (! $rekModel || ! $rekModel->akun_id) {
                echo "SKIP REK {$r->id}: tidak ada akun_id\n";

                continue;
            }

            $jurnalItems = [
                ['akun_id' => $rekModel->akun_id, 'debit' => $r->saldo_awal, 'kredit' => 0, 'keterangan' => 'Saldo awal '.$r->nama],
            ];
            if ($akunModal) {
                $jurnalItems[] = ['akun_id' => $akunModal->id, 'debit' => 0, 'kredit' => $r->saldo_awal, 'keterangan' => 'Saldo awal '.$r->nama];
            }

            $jurnal = JournalService::post('opening_balance', $tanggalAwal, $jurnalItems, 'Saldo Awal '.$r->nama, $rekModel);
            echo "CREATED opening balance jurnal ID:{$jurnal->id} untuk REK {$r->id} ({$r->nama}) = {$r->saldo_awal}\n";
        }

        // FIX 2: Ganti akun 114 -> 115 di jurnal items
        $akun114 = AkunPerkiraan::where('kode', '114')->first();
        $akun115 = AkunPerkiraan::where('kode', '115')->first();
        if ($akun114 && $akun115) {
            $updated = DB::table('jurnal_items')->where('akun_id', $akun114->id)->update(['akun_id' => $akun115->id]);
            echo "UPDATED {$updated} jurnal_items dari akun 114 -> 115\n";
        }

        // FIX 3: Insert missing BB persediaan untuk penjualan menggunakan StockService
        $penjualans = DB::table('penjualan')->get();
        foreach ($penjualans as $pj) {
            $pjItems = DB::table('penjualan_items')->where('penjualan_id', $pj->id)->get();
            foreach ($pjItems as $pjItem) {
                $bbKeluar = DB::table('bb_persediaan')
                    ->where('barang_id', $pjItem->barang_id)
                    ->where('keterangan', 'like', '%'.$pj->nomor.'%')
                    ->where('keluar_qty', '>', 0)
                    ->count();

                if ($bbKeluar == 0) {
                    $barangModel = Barang::find($pjItem->barang_id);
                    if (! $barangModel) {
                        continue;
                    }

                    $penjualanModel = Penjualan::find($pj->id);

                    // Gunakan StockService untuk konsistensi
                    // Stok saat ini sudah dikurangi dari pembelian, tapi BB persediaan keluar tidak tercatat
                    // Tambah dulu stok sementara, lalu keluarkan dengan StockService
                    $barangModel->stok += $pjItem->qty;
                    $barangModel->save();

                    [$newStok, $ratt, , $hppTotal] = StockService::keluarBarang(
                        $barangModel,
                        (float) $pjItem->qty,
                        $pj->tanggal,
                        'Penjualan '.$pj->nomor,
                        $penjualanModel
                    );

                    echo "CREATED bb_persediaan keluar: {$pj->nomor} | barang:{$barangModel->nama} | qty:{$pjItem->qty} | saldo:{$newStok}\n";
                }
            }
        }

        // FIX 4: Sinkronisasi stok barang dengan bb_persediaan terakhir
        $barangs = DB::table('barang')->get();
        foreach ($barangs as $b) {
            $lastSaldo = DB::table('bb_persediaan')
                ->where('barang_id', $b->id)
                ->orderByDesc('id')
                ->value('saldo_qty');

            if ($lastSaldo !== null && (float) $lastSaldo != (float) $b->stok) {
                DB::table('barang')->where('id', $b->id)->update(['stok' => $lastSaldo]);
                echo "SYNC stok barang id:{$b->id} ({$b->nama}): {$b->stok} -> {$lastSaldo}\n";
            }
        }

        DB::commit();
        echo "\n=== FIX SELESAI ===\n";

        // Trial balance setelah fix
        echo "\n--- Trial Balance Setelah Fix ---\n";
        $totalDebit = DB::table('jurnal_items')
            ->join('jurnal_umum', 'jurnal_umum.id', '=', 'jurnal_items.jurnal_id')
            ->where('jurnal_umum.is_posted', true)
            ->sum('debit');
        $totalKredit = DB::table('jurnal_items')
            ->join('jurnal_umum', 'jurnal_umum.id', '=', 'jurnal_items.jurnal_id')
            ->where('jurnal_umum.is_posted', true)
            ->sum('kredit');
        echo "Total Debit: {$totalDebit} | Total Kredit: {$totalKredit} | Selisih: ".($totalDebit - $totalKredit)."\n";

    } catch (Throwable $e) {
        DB::rollBack();
        echo 'ERROR: '.$e->getMessage()."\n";
        echo $e->getTraceAsString()."\n";
    }
}
