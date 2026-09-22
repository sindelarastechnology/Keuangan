<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\AkunPerkiraan;
use App\Models\JurnalItem;
use App\Models\JurnalUmum;
use App\Services\ReportService;
use Illuminate\Contracts\Console\Kernel;

echo "=== DEBUG NERACA - Saldo per akun ===\n\n";

$asetAkun = AkunPerkiraan::where('jenis', 'aset')->leaf()->orderBy('kode')->get();
echo "ASET AKUN:\n";
foreach ($asetAkun as $akun) {
    $saldo = ReportService::saldoAkunKumulatif($akun->id, now()->toDateString());
    $debit = (float) JurnalItem::where('akun_id', $akun->id)->whereHas('jurnal', fn ($q) => $q->where('is_posted', true))->sum('debit');
    $kredit = (float) JurnalItem::where('akun_id', $akun->id)->whereHas('jurnal', fn ($q) => $q->where('is_posted', true))->sum('kredit');
    echo "  {$akun->kode} {$akun->nama}: D={$debit}, K={$kredit}, Saldo={$saldo}\n";
}

echo "\nMODAL AKUN:\n";
$modalAkun = AkunPerkiraan::where('jenis', 'modal')->leaf()->orderBy('kode')->get();
foreach ($modalAkun as $akun) {
    $saldo = ReportService::saldoAkunKumulatif($akun->id, now()->toDateString());
    $debit = (float) JurnalItem::where('akun_id', $akun->id)->whereHas('jurnal', fn ($q) => $q->where('is_posted', true))->sum('debit');
    $kredit = (float) JurnalItem::where('akun_id', $akun->id)->whereHas('jurnal', fn ($q) => $q->where('is_posted', true))->sum('kredit');
    echo "  {$akun->kode} {$akun->nama}: D={$debit}, K={$kredit}, Saldo={$saldo}\n";
}

echo "\nBEBAN AKUN:\n";
$bebanAkun = AkunPerkiraan::where('jenis', 'beban')->leaf()->orderBy('kode')->get();
foreach ($bebanAkun as $akun) {
    $saldo = ReportService::saldoAkunKumulatif($akun->id, now()->toDateString());
    $debit = (float) JurnalItem::where('akun_id', $akun->id)->whereHas('jurnal', fn ($q) => $q->where('is_posted', true))->sum('debit');
    $kredit = (float) JurnalItem::where('akun_id', $akun->id)->whereHas('jurnal', fn ($q) => $q->where('is_posted', true))->sum('kredit');
    if ($debit > 0 || $kredit > 0) {
        echo "  {$akun->kode} {$akun->nama}: D={$debit}, K={$kredit}, Saldo={$saldo}\n";
    }
}

echo "\nPENDAPATAN AKUN:\n";
$pendAkun = AkunPerkiraan::where('jenis', 'pendapatan')->leaf()->orderBy('kode')->get();
foreach ($pendAkun as $akun) {
    $saldo = ReportService::saldoAkunKumulatif($akun->id, now()->toDateString());
    $debit = (float) JurnalItem::where('akun_id', $akun->id)->whereHas('jurnal', fn ($q) => $q->where('is_posted', true))->sum('debit');
    $kredit = (float) JurnalItem::where('akun_id', $akun->id)->whereHas('jurnal', fn ($q) => $q->where('is_posted', true))->sum('kredit');
    if ($debit > 0 || $kredit > 0) {
        echo "  {$akun->kode} {$akun->nama}: D={$debit}, K={$kredit}, Saldo={$saldo}\n";
    }
}

echo "\nSEMUA JURNAL:\n";
$jurnals = JurnalUmum::with('items.akun')->orderBy('tanggal')->get();
foreach ($jurnals as $j) {
    echo "  {$j->nomor} [{$j->tipe}] {$j->tanggal}: {$j->keterangan}\n";
    foreach ($j->items as $ji) {
        echo "    {$ji->akun->kode} {$ji->akun->nama}: D={$ji->debit} K={$ji->kredit}\n";
    }
}
