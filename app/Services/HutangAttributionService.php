<?php

namespace App\Services;

use App\Models\BbHutang;
use App\Models\Pembelian;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HutangAttributionService
{
    /**
     * Sisa hutang sebuah pembelian kredit (selisih kredit-debit
     * pada baris bb_hutang yang ter-atribusi ke faktur tersebut).
     */
    public static function sisa(Pembelian $pembelian): float
    {
        return (float) BbHutang::where('pembelian_id', $pembelian->id)->sum(DB::raw('kredit - debit'));
    }

    /**
     * Faktur kredit supplier yang masih memiliki sisa tagihan, urut lama ke baru (FIFO).
     *
     * @return Collection<int, Pembelian>
     */
    public static function fakturBelumLunas(Supplier $supplier)
    {
        return Pembelian::query()
            ->where('supplier_id', $supplier->id)
            ->where('status', 'posted')
            ->where('metode_bayar', 'kredit')
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get()
            ->filter(fn (Pembelian $p) => static::sisa($p) > 0.005);
    }

    /**
     * Alokasikan nominal pembayaran hutang ke faktur-faktur supplier secara FIFO
     * dan catat ke bb_hutang (debit mengurangi hutang).
     *
     * Saldo berjalan per supplier tetap dipertahankan: setiap baris yang dibuat
     * memuat saldo kumulatif dari baris sebelumnya. Sisa nilai yang tidak bisa
     * dialokasikan ke faktur tertentu tercatat di level supplier (pembelian_id null).
     */
    public static function alokasikan(
        Supplier $supplier,
        float $nominal,
        string $keterangan,
        string $tanggal,
        ?int $jurnalId = null
    ): void {
        $nominal = round($nominal, 2);
        if ($nominal <= 0) {
            return;
        }

        $lastSaldo = (float) (BbHutang::where('supplier_id', $supplier->id)->orderByDesc('id')->value('saldo') ?? 0);
        $sisaNominal = $nominal;

        foreach (static::fakturBelumLunas($supplier) as $pembelian) {
            if ($sisaNominal <= 0.005) {
                break;
            }

            $sisaFaktur = static::sisa($pembelian);
            $porsi = min($sisaFaktur, $sisaNominal);
            if ($porsi <= 0.005) {
                continue;
            }

            $lastSaldo -= $porsi;
            $sisaNominal -= $porsi;

            BbHutang::create([
                'supplier_id' => $supplier->id,
                'pembelian_id' => $pembelian->id,
                'jurnal_id' => $jurnalId,
                'tanggal' => $tanggal,
                'keterangan' => $keterangan,
                'debit' => $porsi,
                'kredit' => 0,
                'saldo' => round($lastSaldo, 2),
            ]);
        }

        // Sisa yang tidak ter-alokasi ke faktur tertentu → catat di level supplier.
        if ($sisaNominal > 0.005) {
            $lastSaldo -= $sisaNominal;

            BbHutang::create([
                'supplier_id' => $supplier->id,
                'pembelian_id' => null,
                'jurnal_id' => $jurnalId,
                'tanggal' => $tanggal,
                'keterangan' => $keterangan,
                'debit' => $sisaNominal,
                'kredit' => 0,
                'saldo' => round($lastSaldo, 2),
            ]);
        }
    }

    /**
     * Batalkan (reversal) pembayaran yang tercatat pada sebuah jurnal kas keluar.
     * Mengembalikan kredit ke faktur yang sama dengan nilai yang ter-atribusi
     * saat pembayaran dicatat, sehingga sisa hutang faktur kembali penuh.
     */
    public static function batalkan(Supplier $supplier, int $jurnalId, string $keterangan, string $tanggal): void
    {
        $rows = BbHutang::where('jurnal_id', $jurnalId)
            ->where('supplier_id', $supplier->id)
            ->orderBy('id')
            ->get();

        if ($rows->isEmpty()) {
            return;
        }

        $lastSaldo = (float) (BbHutang::where('supplier_id', $supplier->id)->orderByDesc('id')->value('saldo') ?? 0);

        foreach ($rows as $row) {
            $amt = (float) $row->debit;
            if ($amt <= 0) {
                continue;
            }

            $lastSaldo += $amt;

            BbHutang::create([
                'supplier_id' => $supplier->id,
                'pembelian_id' => $row->pembelian_id,
                'jurnal_id' => null,
                'tanggal' => $tanggal,
                'keterangan' => $keterangan,
                'debit' => 0,
                'kredit' => $amt,
                'saldo' => round($lastSaldo, 2),
            ]);
        }
    }
}
