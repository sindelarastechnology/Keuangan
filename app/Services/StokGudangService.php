<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\StokGudang;
use RuntimeException;

/**
 * Mutasi stok per gudang sambil menjaga invariant:
 * sum(stok_gudang) == barang.stok, dengan gudang utama barang sebagai
 * baris penyeimbang (residual).
 */
class StokGudangService
{
    /**
     * Tambah qty ke gudang tujuan (pembelian).
     *
     * Urutan: stok barang dinaikkan dulu (menambah gudang utama), lalu qty
     * dipindah ke gudang tujuan dan keseimbangan disusun ulang sehingga
     * pertambahan benar-benar berada di gudang tujuan.
     *
     * Bila gudang tujuan adalah gudang utama barang (gudang penyeimbang/
     * residual), tidak ada yang perlu dilakukan karena perubahan stok global
     * sudah diserap baris gudang utama oleh seimbangkanStokGudang().
     */
    public static function tambah(Barang $barang, int $gudangId, float $qty): void
    {
        if ($qty <= 0 || $gudangId === (int) $barang->gudang_id) {
            return;
        }

        $row = StokGudang::firstOrCreate(
            ['barang_id' => $barang->id, 'gudang_id' => $gudangId],
            ['qty' => 0]
        );

        $row->qty = round((float) $row->qty + $qty, 2);
        $row->save();

        $barang->seimbangkanStokGudang();
    }

    /**
     * Kurangi qty dari gudang terpilih (penjualan). Gagal bila stok gudang
     * tersebut tidak cukup.
     *
     * Bila gudang terpilih adalah gudang utama barang (gudang penyeimbang/
     * residual), tidak ada yang perlu dilakukan karena pengurangan stok global
     * sudah diserap baris gudang utama oleh seimbangkanStokGudang().
     */
    public static function kurangi(Barang $barang, int $gudangId, float $qty): void
    {
        if ($qty <= 0 || $gudangId === (int) $barang->gudang_id) {
            return;
        }

        $row = StokGudang::where('barang_id', $barang->id)->where('gudang_id', $gudangId)->first();

        if (! $row || (float) $row->qty <= 0) {
            throw new RuntimeException("Stok {$barang->nama} di gudang terpilih tidak tersedia.");
        }

        $baru = round((float) $row->qty - $qty, 2);

        if ($baru < -0.005) {
            throw new RuntimeException(
                "Stok {$barang->nama} di gudang terpilih tidak mencukupi. Tersedia: {$row->qty} {$barang->satuan}."
            );
        }

        $row->qty = max(0, $baru);
        $row->save();

        $barang->seimbangkanStokGudang();
    }

    /**
     * Stok tersedia sebuah barang di gudang tertentu.
     */
    public static function stokDiGudang(int $barangId, int $gudangId): float
    {
        return (float) (StokGudang::where('barang_id', $barangId)
            ->where('gudang_id', $gudangId)
            ->value('qty') ?? 0);
    }

    /**
     * Peta stok per barang untuk satu gudang (dipakai galeri kasir).
     *
     * @param  int[]  $barangIds
     * @return array<int, float>
     */
    public static function petaStokGudang(array $barangIds, int $gudangId): array
    {
        if (empty($barangIds)) {
            return [];
        }

        return StokGudang::where('gudang_id', $gudangId)
            ->whereIn('barang_id', $barangIds)
            ->pluck('qty', 'barang_id')
            ->map(fn ($qty) => (float) $qty)
            ->all();
    }
}
