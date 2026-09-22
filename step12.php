<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\AkunPerkiraan;
use App\Models\JurnalItem;
use App\Models\JurnalUmum;
use App\Models\Pembelian;
use App\Models\Rekening;
use App\Services\JournalService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

echo "=== STEP 1 & 2 ===\n";
DB::beginTransaction();
try {
    $rekenings = Rekening::where('saldo_awal', '>', 0)->get();
    $akunModal = AkunPerkiraan::where('kode', '31')->first() ?? AkunPerkiraan::where('kode', 'LIKE', '31%')->first();

    foreach ($rekenings as $rek) {
        if (! $rek->akun_id || ! $akunModal) {
            continue;
        }
        $existing = JurnalUmum::where('keterangan', 'LIKE', '%Saldo Awal Rekening #'.$rek->id.'%')->exists();
        if ($existing) {
            continue;
        }
        JournalService::post('manual', $rek->created_at->toDateString(), [
            ['akun_id' => $rek->akun_id, 'debit' => $rek->saldo_awal, 'kredit' => 0, 'keterangan' => 'Saldo Awal '.$rek->nama],
            ['akun_id' => $akunModal->id, 'debit' => 0, 'kredit' => $rek->saldo_awal, 'keterangan' => 'Saldo Awal '.$rek->nama],
        ], 'Saldo Awal Rekening #'.$rek->id.' - '.$rek->nama);
        echo "OK Saldo Awal Rek {$rek->id}\n";
    }

    $akun114 = AkunPerkiraan::where('kode', '114')->first();
    $akun115 = AkunPerkiraan::where('kode', '115')->first();
    if ($akun114 && $akun115) {
        $wrongItems = JurnalItem::where('akun_id', $akun114->id)->get();
        foreach ($wrongItems as $wi) {
            $wi->update(['akun_id' => $akun115->id]);
            echo "OK Fix Akun {$wi->id}\n";
        }
    }

    // Check Jurnal 6 item from 114 to 115 if any specifically for pembelian item if not fixed
    $pembelian = Pembelian::all();
    foreach ($pembelian as $p) {
        if ($p->jurnal) {
            foreach ($p->jurnal->items as $ji) {
                if ($ji->akun_id == $akun114->id) {
                    $ji->update(['akun_id' => $akun115->id]);
                    echo "OK Fix Akun on Pembelian {$p->id}\n";
                }
            }
        }
    }
    DB::commit();
    echo "DONE\n";
} catch (Exception $e) {
    DB::rollBack();
    echo 'ERR: '.$e->getMessage()."\n";
}
