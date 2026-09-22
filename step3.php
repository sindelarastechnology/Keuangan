<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\BbPersediaan;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

echo "=== STEP 3: Insert missing bb_persediaan exit ===\n";

DB::beginTransaction();
try {
    $items = DB::table('penjualan_items')
        ->join('penjualans', 'penjualan_items.penjualan_id', '=', 'penjualans.id')
        ->select('penjualan_items.*', 'penjualans.nomor as nomor_penj', 'penjualans.tanggal')
        ->get();

    echo 'Total penjualan items: '.$items->count()."\n";

    foreach ($items as $pi) {
        $exists = BbPersediaan::where('barang_id', $pi->barang_id)
            ->where('keterangan', 'LIKE', '%'.$pi->nomor_penj.'%')
            ->where('keluar_qty', '>', 0)
            ->exists();

        if ($exists) {
            echo 'SKIP '.$pi->nomor_penj."\n";

            continue;
        }

        $last = BbPersediaan::where('barang_id', $pi->barang_id)->orderByDesc('id')->first();
        $lastQty = $last ? (float) $last->saldo_qty : 0;
        $ratt = $last ? (float) $last->ratt : (float) $pi->harga_satuan;
        $newQty = $lastQty - $pi->jumlah;
        $keluar_harga = $pi->jumlah * $ratt;

        BbPersediaan::create([
            'barang_id' => $pi->barang_id,
            'ref_type' => 'App\\Models\\Penjualan',
            'ref_id' => $pi->penjualan_id,
            'tanggal' => $pi->tanggal,
            'keterangan' => 'Penjualan '.$pi->nomor_penj,
            'masuk_qty' => 0,
            'masuk_harga' => 0,
            'keluar_qty' => $pi->jumlah,
            'keluar_harga' => $keluar_harga,
            'saldo_qty' => $newQty,
            'saldo_harga' => $newQty * $ratt,
            'ratt' => $ratt,
        ]);

        echo 'OK barang_id='.$pi->barang_id.' keluar='.$pi->jumlah.' saldo_qty='.$newQty."\n";
    }

    DB::commit();
    echo "DONE\n";
} catch (Throwable $e) {
    DB::rollBack();
    echo '[ERROR] '.$e->getMessage()."\n";
    echo $e->getFile().':'.$e->getLine()."\n";
}
