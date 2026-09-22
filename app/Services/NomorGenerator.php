<?php

namespace App\Services;

use App\Models\Pengaturan;
use Illuminate\Support\Facades\DB;

class NomorGenerator
{
    /**
     * Tabel dokumen per prefix nomor, dipakai untuk menghitung nomor terakhir
     * bulan berjalan saat key seq di Pengaturan belum tersedia.
     */
    private const TABLE_PREFIX = [
        'KM' => 'kas_masuk',
        'KK' => 'kas_keluar',
        'MB' => 'mutasi_bank',
        'PB' => 'pembelians',
        'PJ' => 'penjualans',
        'RPJ' => 'retur_penjualan',
        'RPB' => 'retur_pembelian',
        'SO' => 'stok_opname',
        'PS' => 'perubahan_stok',
        'TG' => 'transfer_gudang',
        'KAS' => 'jurnal_umum',
        'MUT' => 'jurnal_umum',
        'PEM' => 'jurnal_umum',
        'PEN' => 'jurnal_umum',
        'PER' => 'jurnal_umum',
        'RET' => 'jurnal_umum',
        'MAN' => 'jurnal_umum',
        'HPP' => 'jurnal_umum',
        'TUT' => 'jurnal_umum',
    ];

    /**
     * Format: {PREFIX}/{BULAN}/{TAHUN}/{SEQ 4 digit}
     *
     * Seq di-reset per bulan (key per prefix + bulan + tahun). Ketika key
     * belum ada, di-seed dari nomor terbesar yang sudah ada di bulan yang
     * sama sehingga tidak pernah menghasilkan nomor duplikat.
     */
    public static function generate(string $prefix, ?string $tanggal = null): string
    {
        $tanggal = $tanggal ?: now()->toDateString();
        $bulan = now()->parse($tanggal)->format('m');
        $tahun = now()->parse($tanggal)->format('Y');

        // Dalam satu transaksi + FOR UPDATE agar dua permintaan bersamaan
        // tidak membaca sequence yang sama (anti nomor duplikat).
        return DB::transaction(function () use ($prefix, $bulan, $tahun) {
            $key = 'seq_'.$prefix.'_'.$bulan.'_'.$tahun;

            Pengaturan::firstOrCreate(['key' => $key], ['value' => 0]);
            $lastSeq = (int) (Pengaturan::where('key', $key)->lockForUpdate()->value('value') ?? 0);

            if ($lastSeq <= 0) {
                $lastSeq = self::maxSeqBulan($prefix, $bulan, $tahun);
            }

            $seq = $lastSeq + 1;
            Pengaturan::atur($key, $seq);

            return sprintf('%s/%s/%s/%04d', $prefix, $bulan, $tahun, $seq);
        });
    }

    /**
     * Nomor urut terbesar pada bulan yang sama di tabel terkait prefix.
     */
    protected static function maxSeqBulan(string $prefix, string $bulan, string $tahun): int
    {
        $table = self::TABLE_PREFIX[$prefix] ?? null;
        if (! $table) {
            return 0;
        }

        $rows = DB::table($table)
            ->when(auth()->check(), fn ($q) => $q->where('user_id', auth()->id()))
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->pluck('nomor');

        $max = 0;
        foreach ($rows as $nomor) {
            $parts = explode('/', (string) $nomor);
            $seq = (int) end($parts);
            if ($parts[0] === $prefix && $seq > $max) {
                $max = $seq;
            }
        }

        return $max;
    }
}
