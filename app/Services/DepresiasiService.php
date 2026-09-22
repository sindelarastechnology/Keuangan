<?php

namespace App\Services;

use App\Models\Aset;
use App\Models\JurnalUmum;
use App\Models\Penyusutan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DepresiasiService
{
    /**
     * Beban penyusutan per bulan (metode garis lurus).
     */
    public static function bebanBulanan(Aset $aset): float
    {
        return $aset->beban_bulanan;
    }

    /**
     * Beban aktual untuk sebuah aset pada periode (membatasi bulan terakhir).
     */
    public static function bebanUntukPeriode(Aset $aset, string $periode): float
    {
        if (! $aset->bisaDisusutkan($periode)) {
            return 0.0;
        }

        return min(self::bebanBulanan($aset), max(0.0, $aset->nilai_sisa_disusutkan));
    }

    /**
     * Proses penyusutan untuk sebuah periode (bulan dengan format Y-m).
     * Membuat satu jurnal berisi baris per aset dan mencatat riwayat tiap aset.
     */
    public static function proses(string $periode): ?JurnalUmum
    {
        $tanggal = Carbon::createFromFormat('Y-m', $periode)->endOfMonth()->toDateString();
        $asets = Aset::query()
            ->withSum('penyusutan', 'beban')
            ->where('status', 'aktif')
            ->whereDoesntHave('penyusutan', fn ($q) => $q->where('periode', $periode))
            ->get()
            ->filter(fn (Aset $aset) => $aset->bisaDisusutkan($periode));

        if ($asets->isEmpty()) {
            return null;
        }

        $items = [];
        $rencana = [];
        foreach ($asets as $aset) {
            $beban = self::bebanUntukPeriode($aset, $periode);
            if ($beban <= 0) {
                continue;
            }
            $items[] = ['akun_id' => $aset->akun_beban_id, 'debit' => $beban, 'kredit' => 0, 'keterangan' => $aset->kode.' '.$aset->nama];
            $items[] = ['akun_id' => $aset->akun_akumulasi_id, 'debit' => 0, 'kredit' => $beban, 'keterangan' => $aset->kode.' '.$aset->nama];
            $rencana[] = ['aset' => $aset, 'beban' => $beban];
        }

        if (empty($items)) {
            return null;
        }

        $keterangan = 'Penyusutan '.namaBulan((int) substr($periode, 5, 2)).' '.substr($periode, 0, 4);

        return DB::transaction(function () use ($periode, $tanggal, $items, $rencana, $keterangan) {
            $jurnal = JournalService::post('penyusutan', $tanggal, $items, $keterangan);

            foreach ($rencana as $r) {
                /** @var Aset $aset */
                $aset = $r['aset'];
                $akumulasiSetelah = round((float) $aset->penyusutan_sum_beban + $r['beban'], 2);
                $aset->penyusutan()->create([
                    'periode' => $periode,
                    'tanggal' => $tanggal,
                    'beban' => $r['beban'],
                    'akumulasi_setelah' => $akumulasiSetelah,
                    'nilai_buku_setelah' => round((float) $aset->harga_perolehan - $akumulasiSetelah, 2),
                    'jurnal_id' => $jurnal->id,
                ]);
            }

            return $jurnal;
        });
    }

    /**
     * Batalkan penyusutan sebuah periode: posting jurnal balik bertanggal sama,
     * hapus riwayat, lalu tandai jurnal asli sebagai dibatalkan.
     */
    public static function batalkan(string $periode): void
    {
        $catatan = Penyusutan::where('periode', $periode)->get();
        if ($catatan->isEmpty()) {
            throw new RuntimeException('Tidak ada penyusutan untuk periode '.$periode.'.');
        }

        $jurnalAsli = JurnalUmum::where('id', $catatan->first()->jurnal_id)
            ->where('keterangan', 'not like', '[DIBATALKAN]%')
            ->first();

        if (! $jurnalAsli) {
            throw new RuntimeException('Jurnal penyusutan periode '.$periode.' tidak ditemukan.');
        }

        $reversed = $jurnalAsli->items->map(function ($item) {
            return [
                'akun_id' => $item->akun_id,
                'debit' => $item->kredit,
                'kredit' => $item->debit,
                'keterangan' => 'BALIK: '.$item->keterangan,
            ];
        })->toArray();

        DB::transaction(function () use ($periode, $jurnalAsli, $reversed) {
            JournalService::post(
                'penyusutan',
                $jurnalAsli->tanggal,
                $reversed,
                'Batalkan penyusutan '.$periode.' — '.$jurnalAsli->nomor,
                $jurnalAsli
            );

            $jurnalAsli->update([
                'keterangan' => '[DIBATALKAN] '.$jurnalAsli->keterangan,
                'voided_at' => now(),
            ]);

            Penyusutan::where('periode', $periode)->delete();
        });
    }
}
