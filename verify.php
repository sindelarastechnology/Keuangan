<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Barang;
use App\Models\Rekening;
use App\Services\ReportService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

echo "=== VERIFIKASI NERACA ===\n";
$tanggal = now()->toDateString();
$neraca = ReportService::neraca($tanggal);
echo 'Total Aset       : Rp '.number_format($neraca['total_aset'])."\n";
echo 'Total Kewajiban  : Rp '.number_format($neraca['total_kewajiban'])."\n";
echo 'Total Modal      : Rp '.number_format($neraca['total_modal'])."\n";
if (isset($neraca['laba_rugi_berjalan'])) {
    echo 'Laba/(Rugi)      : Rp '.number_format($neraca['laba_rugi_berjalan'])."\n";
}
if (isset($neraca['total_kewajiban_modal'])) {
    echo 'Total Kew+Modal  : Rp '.number_format($neraca['total_kewajiban_modal'])."\n";
}
$selisih = isset($neraca['selisih']) ? $neraca['selisih'] : ($neraca['total_aset'] - $neraca['total_kewajiban'] - $neraca['total_modal']);
echo 'Selisih          : Rp '.number_format(abs($selisih))."\n";
echo 'BALANCED         : '.(abs($selisih) < 1 ? 'YES - NERACA SEIMBANG!' : 'NO - MASIH SELISIH')."\n";

echo "\n=== SALDO REKENING ===\n";
$rekenings = Rekening::all();
foreach ($rekenings as $r) {
    echo $r->nama.': Rp '.number_format($r->saldo)."\n";
}

echo "\n=== LABA RUGI (bulan ini) ===\n";
$lr = ReportService::labaRugi(now()->startOfMonth()->toDateString(), now()->toDateString());
echo 'Pendapatan: Rp '.number_format($lr['total_pendapatan'])."\n";
echo 'HPP       : Rp '.number_format($lr['total_hpp'] ?? 0)."\n";
echo 'Beban     : Rp '.number_format($lr['total_beban'])."\n";
echo 'Laba/Rugi : Rp '.number_format($lr['laba_rugi'])."\n";

echo "\n=== BB PERSEDIAAN ===\n";
$bb = DB::table('bb_persediaan')->select('barang_id', DB::raw('MAX(saldo_qty) as saldo_qty'))->groupBy('barang_id')->get();
foreach ($bb as $b) {
    $barang = Barang::find($b->barang_id);
    echo ($barang ? $barang->nama : "ID:{$b->barang_id}").': '.$b->saldo_qty." pcs\n";
}
