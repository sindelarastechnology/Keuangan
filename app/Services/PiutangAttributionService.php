<?php

namespace App\Services;

use App\Models\BbPiutang;
use App\Models\Customer;
use App\Models\Penjualan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PiutangAttributionService
{
    /**
     * Sisa piutang sebuah penjualan kredit (selisih debit-kredit
     * pada baris bb_piutang yang ter-atribusi ke faktur tersebut).
     */
    public static function sisa(Penjualan $penjualan): float
    {
        return (float) BbPiutang::where('penjualan_id', $penjualan->id)->sum(DB::raw('debit - kredit'));
    }

    /**
     * Faktur kredit customer yang masih memiliki sisa tagihan, urut lama ke baru (FIFO).
     *
     * @return Collection<int, Penjualan>
     */
    public static function fakturBelumLunas(Customer $customer)
    {
        return Penjualan::query()
            ->where('customer_id', $customer->id)
            ->where('status', 'posted')
            ->where('metode_bayar', 'kredit')
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get()
            ->filter(fn (Penjualan $p) => static::sisa($p) > 0.005);
    }

    /**
     * Alokasikan nominal pembayaran piutang ke faktur-faktur customer secara FIFO
     * dan catat ke bb_piutang (kredit mengurangi piutang).
     *
     * Saldo berjalan per customer tetap dipertahankan: setiap baris yang dibuat
     * memuat saldo kumulatif dari baris sebelumnya. Sisa nilai yang tidak bisa
     * dialokasikan ke faktur tertentu tercatat di level customer (penjualan_id null).
     */
    public static function alokasikan(
        Customer $customer,
        float $nominal,
        string $keterangan,
        string $tanggal,
        ?int $jurnalId = null
    ): void {
        $nominal = round($nominal, 2);
        if ($nominal <= 0) {
            return;
        }

        $lastSaldo = (float) (BbPiutang::where('customer_id', $customer->id)->orderByDesc('id')->value('saldo') ?? 0);
        $sisaNominal = $nominal;

        foreach (static::fakturBelumLunas($customer) as $penjualan) {
            if ($sisaNominal <= 0.005) {
                break;
            }

            $sisaFaktur = static::sisa($penjualan);
            $porsi = min($sisaFaktur, $sisaNominal);
            if ($porsi <= 0.005) {
                continue;
            }

            $lastSaldo -= $porsi;
            $sisaNominal -= $porsi;

            BbPiutang::create([
                'customer_id' => $customer->id,
                'penjualan_id' => $penjualan->id,
                'jurnal_id' => $jurnalId,
                'tanggal' => $tanggal,
                'keterangan' => $keterangan,
                'debit' => 0,
                'kredit' => $porsi,
                'saldo' => round($lastSaldo, 2),
            ]);
        }

        // Sisa yang tidak ter-alokasi ke faktur tertentu → catat di level customer.
        if ($sisaNominal > 0.005) {
            $lastSaldo -= $sisaNominal;

            BbPiutang::create([
                'customer_id' => $customer->id,
                'penjualan_id' => null,
                'jurnal_id' => $jurnalId,
                'tanggal' => $tanggal,
                'keterangan' => $keterangan,
                'debit' => 0,
                'kredit' => $sisaNominal,
                'saldo' => round($lastSaldo, 2),
            ]);
        }
    }

    /**
     * Batalkan (reversal) pelunasan yang tercatat pada sebuah jurnal kas masuk.
     * Mengembalikan debit ke faktur yang sama dengan nilai yang ter-atribusi
     * saat pembayaran dicatat, sehingga sisa tagihan faktur kembali penuh.
     */
    public static function batalkan(Customer $customer, int $jurnalId, string $keterangan, string $tanggal): void
    {
        $rows = BbPiutang::where('jurnal_id', $jurnalId)
            ->where('customer_id', $customer->id)
            ->orderBy('id')
            ->get();

        if ($rows->isEmpty()) {
            return;
        }

        $lastSaldo = (float) (BbPiutang::where('customer_id', $customer->id)->orderByDesc('id')->value('saldo') ?? 0);

        foreach ($rows as $row) {
            $amt = (float) $row->kredit;
            if ($amt <= 0) {
                continue;
            }

            $lastSaldo += $amt;

            BbPiutang::create([
                'customer_id' => $customer->id,
                'penjualan_id' => $row->penjualan_id,
                'jurnal_id' => null,
                'tanggal' => $tanggal,
                'keterangan' => $keterangan,
                'debit' => $amt,
                'kredit' => 0,
                'saldo' => round($lastSaldo, 2),
            ]);
        }
    }
}
