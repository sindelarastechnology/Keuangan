<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\KasKeluar;
use App\Models\KasMasuk;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Illuminate\Contracts\Console\Kernel;

$grafik = [];
for ($i = 5; $i >= 0; $i--) {
    $tgl = now()->startOfMonth()->subMonths($i);
    $awal = $tgl->copy()->startOfMonth()->toDateString();
    $akhir = $tgl->copy()->endOfMonth()->toDateString();
    $masuk = (float) KasMasuk::whereBetween('tanggal', [$awal, $akhir])->sum('grand_total')
        + (float) Penjualan::where('metode_bayar', 'tunai')->whereBetween('tanggal', [$awal, $akhir])->sum('total');
    $keluar = (float) KasKeluar::whereBetween('tanggal', [$awal, $akhir])->sum('grand_total')
        + (float) Pembelian::where('metode_bayar', 'tunai')->whereBetween('tanggal', [$awal, $akhir])->sum('total');
    $grafik[] = [
        'bulan' => $tgl->format('M'),
        'masuk' => $masuk,
        'keluar' => $keluar,
    ];
}

print_r($grafik);

$max = collect($grafik)->flatMap(fn ($x) => [$x['masuk'], $x['keluar']])->max();
$max = $max > 0 ? $max : 1;
echo 'Max: '.$max."\n";

foreach ($grafik as $g) {
    $hMasuk = max(2, round(($g['masuk'] / $max) * 130));
    $hKeluar = max(2, round(($g['keluar'] / $max) * 130));
    echo $g['bulan'].' -> masuk: '.$hMasuk.'px, keluar: '.$hKeluar."px\n";
}
