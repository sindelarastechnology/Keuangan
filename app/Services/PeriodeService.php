<?php

namespace App\Services;

use App\Models\PeriodeAkuntansi;
use Carbon\Carbon;

class PeriodeService
{
    /**
     * Siapkan periode untuk tahun tertentu jika belum ada (Januari-Desember).
     */
    public static function siapkanPerTahun(int $tahun): void
    {
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $kode = sprintf('%s%02d', $tahun, $bulan);
            PeriodeAkuntansi::firstOrCreate(
                ['bulan' => str_pad((string) $bulan, 2, '0', STR_PAD_LEFT), 'tahun' => (string) $tahun],
                ['kode' => $kode]
            );
        }
    }

    /**
     * Buka periode akuntansi (tutup yang lain).
     */
    public static function buka(PeriodeAkuntansi $periode): void
    {
        if ($periode->is_closed) {
            throw new \RuntimeException('Periode sudah ditutup dan tidak bisa dibuka ulang.');
        }

        PeriodeAkuntansi::where('is_open', true)->update(['is_open' => false]);

        $periode->update([
            'is_open' => true,
            'tanggal_buka' => now()->toDateString(),
        ]);
    }

    /**
     * Periode yang sedang aktif.
     */
    public static function aktif(): ?PeriodeAkuntansi
    {
        return PeriodeAkuntansi::where('is_open', true)
            ->where('is_closed', false)
            ->first();
    }

    /**
     * Tanggal awal & akhir sebuah periode.
     */
    public static function rentang(PeriodeAkuntansi $periode): array
    {
        $awal = Carbon::create((int) $periode->tahun, (int) $periode->bulan, 1)->startOfMonth();
        $akhir = $awal->copy()->endOfMonth();

        return [$awal->toDateString(), $akhir->toDateString()];
    }

    /**
     * Pastikan periode pada tanggal tertentu boleh diposting (tidak dikunci).
     *
     * @throws \RuntimeException
     */
    public static function pastikanDapatDiposting(string $tanggal): void
    {
        $tgl = Carbon::parse($tanggal);
        $bulan = str_pad((string) $tgl->month, 2, '0', STR_PAD_LEFT);
        $tahun = (string) $tgl->year;

        $periode = PeriodeAkuntansi::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        // Auto-buat baris periode bila belum tersedia (mis. bulan masa depan)
        // agar guard kunci/tutup buku tetap berlaku untuk tanggal tersebut.
        if (! $periode) {
            $periode = PeriodeAkuntansi::firstOrCreate(
                ['bulan' => $bulan, 'tahun' => $tahun],
                ['kode' => sprintf('%s%02d', $tahun, (int) $tgl->month)]
            );
        }

        if ($periode->is_locked || $periode->is_closed) {
            throw new \RuntimeException('Periode '.$periode->label.' sedang dikunci. Transaksi tidak bisa diposting.');
        }
    }

    /**
     * Kunci sebuah periode agar tidak ada transaksi baru.
     */
    public static function kunci(PeriodeAkuntansi $periode, ?string $reason = null): void
    {
        $periode->update([
            'is_locked' => true,
            'locked_at' => now(),
            'locked_by' => auth()->id(),
            'lock_reason' => $reason,
        ]);
    }

    /**
     * Buka kunci sebuah periode.
     */
    public static function bukaKunci(PeriodeAkuntansi $periode): void
    {
        $periode->update([
            'is_locked' => false,
            'locked_at' => null,
            'locked_by' => null,
            'lock_reason' => null,
        ]);
    }
}
