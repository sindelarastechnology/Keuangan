<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\AkunPerkiraan;
use App\Models\BbPersediaan;
use App\Models\JurnalItem;
use App\Models\JurnalUmum;
use App\Models\Rekening;
use App\Services\JournalService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

echo "=== Script Rekonsiliasi Data Test ===\n\n";

DB::beginTransaction();
try {
    // STEP 1: Buat jurnal saldo awal untuk Rekening
    echo "STEP 1: Membuat jurnal saldo awal rekening...\n";
    $rekenings = Rekening::where('saldo_awal', '>', 0)->get();
    $akunModal = AkunPerkiraan::where('kode', '31')->first()
        ?? AkunPerkiraan::where('kode', 'LIKE', '31%')->first();
    if (! $akunModal) {
        echo "  [ERROR] Akun Modal tidak ditemukan!\n";
    } else {
        echo "  Akun Modal ({$akunModal->kode}): ID={$akunModal->id}, Nama={$akunModal->nama}\n";
    }

    foreach ($rekenings as $rek) {
        echo "  Rekening: ID={$rek->id}, Nama={$rek->nama}, Saldo Awal=Rp ".number_format($rek->saldo_awal)."\n";
        if (! $rek->akun_id || ! $akunModal) {
            echo "  [SKIP] tidak ada akun\n";

            continue;
        }
        $existing = JurnalUmum::where('tipe', 'manual')->where('keterangan', 'LIKE', '%Saldo Awal Rekening #'.$rek->id.'%')->exists();
        if ($existing) {
            echo "  [SKIP] sudah ada\n";

            continue;
        }
        $akunRek = AkunPerkiraan::find($rek->akun_id);
        $jurnal = JournalService::post('manual', $rek->created_at->toDateString(), [
            ['akun_id' => $rek->akun_id, 'debit' => $rek->saldo_awal, 'kredit' => 0, 'keterangan' => 'Saldo Awal '.$rek->nama],
            ['akun_id' => $akunModal->id, 'debit' => 0, 'kredit' => $rek->saldo_awal, 'keterangan' => 'Saldo Awal '.$rek->nama],
        ], 'Saldo Awal Rekening #'.$rek->id.' - '.$rek->nama);
        echo "  [OK] Jurnal {$jurnal->nomor} (Dr {$akunRek->kode} / Cr {$akunModal->kode})\n";
    }

    // STEP 2: Fix akun 114 -> 115
    echo "\nSTEP 2: Fix jurnal item akun 114 -> 115...\n";
    $akun114 = AkunPerkiraan::where('kode', '114')->first();
    $akun115 = AkunPerkiraan::where('kode', '115')->first();
    if (! $akun114) {
        echo "  [SKIP] Akun 114 tidak ditemukan\n";
    } elseif (! $akun115) {
        echo "  [SKIP] Akun 115 tidak ditemukan\n";
    } else {
        $wrongItems = JurnalItem::where('akun_id', $akun114->id)->get();
        echo '  Ditemukan '.$wrongItems->count()." items\n";
        foreach ($wrongItems as $wi) {
            $jrn = JurnalUmum::find($wi->jurnal_id);
            echo "  - {$jrn->nomor} item ID={$wi->id}\n";
            $wi->update(['akun_id' => $akun115->id]);
            echo "    [OK] -> akun 115\n";
        }
    }

    // STEP 3: BB Persediaan exit
    echo "\nSTEP 3: Insert missing bb_persediaan exit...\n";
    $penjualanItems = DB::table('penjualan_items')
        ->join('penjualans', 'penjualan_items.penjualan_id', '=', 'penjualans.id')
        ->select('penjualan_items.*', 'penjualans.nomor as penjualan_nomor', 'penjualans.tanggal', 'penjualans.jurnal_id')
        ->get();
    echo '  Total penjualan items: '.$penjualanItems->count()."\n";

    foreach ($penjualanItems as $pi) {
        $existing = BbPersediaan::where('barang_id', $pi->barang_id)
            ->where('keterangan', 'LIKE', '%'.$pi->penjualan_nomor.'%')
            ->where('keluar_qty', '>', 0)->exists();
        if ($existing) {
            echo "  [SKIP] sudah ada untuk {$pi->penjualan_nomor}\n";

            continue;
        }

        $last = BbPersediaan::where('barang_id', $pi->barang_id)->orderByDesc('id')->first();
        $lastQty = $last ? (float) $last->saldo_qty : 0;
        $ratt = $last ? (float) $last->ratt : (float) $pi->harga_satuan;
        $newQty = $lastQty - $pi->jumlah;

        BbPersediaan::create([
            'barang_id' => $pi->barang_id,
            'ref_type' => "App\Models\Penjualan",
            'ref_id' => $pi->penjualan_id,
            'tanggal' => $pi->tanggal,
            'keterangan' => 'Penjualan '.$pi->penjualan_nomor,
            'masuk_qty' => 0, 'masuk_harga' => 0,
            'keluar_qty' => $pi->jumlah, 'keluar_harga' => $pi->jumlah * $ratt,
            'saldo_qty' => $newQty, 'saldo_harga' => $newQty * $ratt, 'ratt' => $ratt,
        ]);
        echo "  [OK] exit barang_id={$pi->barang_id}, keluar={$pi->jumlah}, saldo_qty={$newQty}\n";
    }

    // STEP 4: Verifikasi
    echo "\nSTEP 4: Verifikasi balance jurnal...\n";
    $unbalanced = DB::select('SELECT ju.nomor, SUM(ji.debit) td, SUM(ji.kredit) tk, ABS(SUM(ji.debit)-SUM(ji.kredit)) sel FROM jurnal_umum ju JOIN jurnal_items ji ON ji.jurnal_id=ju.id WHERE ju.is_posted=1 GROUP BY ju.id,ju.nomor HAVING sel > 0.01');
    if (empty($unbalanced)) {
        echo "  [OK] Semua jurnal seimbang!\n";
    } else {
        foreach ($unbalanced as $u) {
            echo "  [WARN] {$u->nomor}: D={$u->td} K={$u->tk} Selisih={$u->sel}\n";
        }
    }

    DB::commit();
    echo "\n=== REKONSILIASI SELESAI ===\n";
} catch (Throwable $e) {
    DB::rollBack();
    echo "\n[ERROR] ".$e->getMessage()."\n".$e->getFile().':'.$e->getLine()."\n";
}
