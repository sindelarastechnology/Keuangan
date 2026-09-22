<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\BbPersediaan;
use RuntimeException;

class StockService
{
    /**
     * Catat barang MASUK (pembelian), update stok + ratt (harga rata-rata).
     *
     * @return array [stokBaru, rattBaru, deltaNilai]
     */
    public static function masukBarang(Barang $barang, float $qty, float $harga, string $tanggal, string $keterangan, $ref = null): array
    {
        if ($qty <= 0) {
            throw new RuntimeException('Jumlah harus lebih dari 0.');
        }

        $oldStok = (float) $barang->stok;
        $oldAvg = (float) $barang->harga_avg;
        $oldSaldoHarga = round($oldStok * $oldAvg, 2);
        $newStok = $oldStok + $qty;
        $newAvg = $oldStok > 0
            ? (($oldStok * $oldAvg) + ($qty * $harga)) / $newStok
            : $harga;

        $newAvg = round($newAvg, 2);
        $newSaldoHarga = round($newStok * $newAvg, 2);

        // Update barang
        $barang->update([
            'stok' => $newStok,
            'harga_avg' => $newAvg,
        ]);

        // Catat ke BB Persediaan
        BbPersediaan::create([
            'barang_id' => $barang->id,
            'ref_type' => $ref ? get_class($ref) : null,
            'ref_id' => $ref?->id,
            'tanggal' => $tanggal,
            'keterangan' => $keterangan,
            'masuk_qty' => $qty,
            'masuk_harga' => $harga,
            'keluar_qty' => 0,
            'keluar_harga' => 0,
            'saldo_qty' => $newStok,
            'saldo_harga' => $newSaldoHarga,
            'ratt' => $newAvg,
        ]);

        return [$newStok, $newAvg, $newSaldoHarga - $oldSaldoHarga];
    }

    /**
     * Catat barang KELUAR (digunakan), hitung nilai HPP berdasar rata-rata.
     *
     * @return array [stokBaru, rattBaru, hpp, hppTotal]
     */
    public static function keluarBarang(Barang $barang, float $qty, string $tanggal, string $keterangan, $ref = null, ?float $hppOverride = null): array
    {
        $oldStok = (float) $barang->stok;
        $oldAvg = (float) $barang->harga_avg;

        if ($qty <= 0) {
            throw new RuntimeException('Jumlah harus lebih dari 0.');
        }

        if ($qty > $oldStok) {
            throw new RuntimeException(
                "Stok tidak mencukupi. Tersedia: {$oldStok} {$barang->satuan}, diminta: {$qty}."
            );
        }

        $ratt = $hppOverride !== null ? $hppOverride : $oldAvg;
        $hppTotal = round($qty * $ratt, 2);
        $newStok = $oldStok - $qty;
        $newSaldoHarga = round($newStok * $ratt, 2);

        // Update barang
        $barang->update([
            'stok' => $newStok,
        ]);

        // Catat ke BB Persediaan
        BbPersediaan::create([
            'barang_id' => $barang->id,
            'ref_type' => $ref ? get_class($ref) : null,
            'ref_id' => $ref?->id,
            'tanggal' => $tanggal,
            'keterangan' => $keterangan,
            'masuk_qty' => 0,
            'masuk_harga' => 0,
            'keluar_qty' => $qty,
            'keluar_harga' => $ratt,
            'saldo_qty' => $newStok,
            'saldo_harga' => $newSaldoHarga,
            'ratt' => $ratt,
        ]);

        return [$newStok, $ratt, $ratt, $hppTotal];
    }

    /**
     * Batalkan barang MASUK (pembatalan pembelian): kurangi stok dan
     * kembalikan nilai & harga rata-rata seperti sebelum transaksi masuk.
     *
     * @return array [stokBaru, rattBaru]
     */
    public static function reverseMasuk(Barang $barang, float $qty, float $harga, string $tanggal, string $keterangan, $ref = null): array
    {
        $oldStok = (float) $barang->stok;
        $oldAvg = (float) $barang->harga_avg;

        if ($qty <= 0) {
            throw new RuntimeException('Jumlah harus lebih dari 0.');
        }

        if ($qty > $oldStok) {
            throw new RuntimeException(
                "Stok tidak mencukupi untuk pembatalan. Tersedia: {$oldStok} {$barang->satuan}, diminta: {$qty}."
            );
        }

        $newStok = $oldStok - $qty;
        $newAvg = $newStok > 0
            ? round(($oldStok * $oldAvg - $qty * $harga) / $newStok, 2)
            : 0;
        $newSaldoHarga = round($newStok * $newAvg, 2);

        // Update barang
        $barang->update([
            'stok' => $newStok,
            'harga_avg' => $newAvg,
        ]);

        // Catat ke BB Persediaan
        BbPersediaan::create([
            'barang_id' => $barang->id,
            'ref_type' => $ref ? get_class($ref) : null,
            'ref_id' => $ref?->id,
            'tanggal' => $tanggal,
            'keterangan' => $keterangan,
            'masuk_qty' => 0,
            'masuk_harga' => 0,
            'keluar_qty' => $qty,
            'keluar_harga' => $harga,
            'saldo_qty' => $newStok,
            'saldo_harga' => $newSaldoHarga,
            'ratt' => $newAvg,
        ]);

        return [$newStok, $newAvg];
    }

    /**
     * Catat barang terjual -> kurangi stok barang.
     */
    public static function kurangiBarang(Barang $barang, float $qty): void
    {
        $oldStok = (float) $barang->stok;

        if ($qty > $oldStok) {
            throw new RuntimeException(
                "Stok barang {$barang->nama} tidak mencukupi. Tersedia: {$oldStok}."
            );
        }

        $barang->update(['stok' => $oldStok - $qty]);
    }

    /**
     * Tambah stok barang (untuk pembatalan penjualan).
     */
    public static function tambahBarang(Barang $barang, float $qty): void
    {
        $oldStok = (float) $barang->stok;
        $barang->update(['stok' => $oldStok + $qty]);
    }
}
