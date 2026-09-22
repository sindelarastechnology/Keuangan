<?php

namespace App\Services;

use App\Models\AkunPerkiraan;
use App\Models\JurnalItem;
use App\Models\JurnalUmum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class JournalService
{
    /**
     * Membuat jurnal dengan validasi double-entry (debit == kredit).
     *
     * @param  array  $items  [[akun_id, debit, kredit, keterangan], ...]
     */
    public static function post(
        string $tipe,
        string $tanggal,
        array $items,
        ?string $keterangan = null,
        $ref = null,
        bool $isPosted = true
    ): JurnalUmum {
        PeriodeService::pastikanDapatDiposting($tanggal);

        DB::beginTransaction();

        try {
            // Validasi minimal 2 baris dan default debet/kredit
            $normalized = [];
            $totalDebit = 0;
            $totalKredit = 0;

            foreach ($items as $item) {
                $debit = (float) ($item['debit'] ?? 0);
                $kredit = (float) ($item['kredit'] ?? 0);

                if (isset($item['akun_id'])) {
                    $normalized[] = [
                        'akun_id' => $item['akun_id'],
                        'debit' => $debit,
                        'kredit' => $kredit,
                        'keterangan' => $item['keterangan'] ?? null,
                    ];
                    $totalDebit += $debit;
                    $totalKredit += $kredit;
                }
            }

            if ($totalDebit <= 0 && $totalKredit <= 0) {
                throw new RuntimeException('Jurnal tidak memiliki nilai yang valid.');
            }

            if (abs($totalDebit - $totalKredit) > 0.01) {
                throw new RuntimeException(
                    'Jurnal tidak seimbang! Total Debit: '.formatRupiah($totalDebit).
                    ' != Total Kredit: '.formatRupiah($totalKredit)
                );
            }

            $nomor = NomorGenerator::generate(Str::upper(Str::substr($tipe, 0, 3)), $tanggal);

            $jurnal = new JurnalUmum;
            $jurnal->nomor = $nomor;
            $jurnal->tanggal = $tanggal;
            $jurnal->keterangan = $keterangan;
            $jurnal->tipe = $tipe;
            $jurnal->is_posted = $isPosted;
            $jurnal->created_by = auth()->id();
            $jurnal->updated_by = auth()->id();
            $jurnal->approval_status = $isPosted ? 'approved' : 'pending_review';
            $jurnal->approved_by = $isPosted ? auth()->id() : null;
            $jurnal->approved_at = $isPosted ? now() : null;
            if ($ref) {
                $jurnal->ref()->associate($ref);
            }
            $jurnal->save();

            foreach ($normalized as $item) {
                $jurnal->items()->create($item);
            }

            DB::commit();

            return $jurnal;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Membatalkan (void) sebuah jurnal dengan membuat jurnal balik (reversal).
     */
    public static function void(JurnalUmum $jurnal, string $keterangan = 'Pembatalan jurnal'): JurnalUmum
    {
        // Cek apakah sudah pernah dibatalkan
        if (str_starts_with($jurnal->keterangan ?? '', '[DIBATALKAN]') || $jurnal->voided_at) {
            throw new RuntimeException('Jurnal ini sudah pernah dibatalkan sebelumnya.');
        }

        // Siapkan jurnal balik (debit dan kredit ditukar)
        $reversed = $jurnal->items->map(function ($item) {
            return [
                'akun_id' => $item->akun_id,
                'debit' => $item->kredit,
                'kredit' => $item->debit,
                'keterangan' => 'BALIK: '.($item->keterangan ?: ''),
            ];
        })->toArray();

        // Jurnal balik mereferensikan jurnal asli agar pasangan void+balik dapat
        // dipisahkan dari laporan (lihat JurnalUmum::kondisiBukanJurnalVoid).
        $new = self::post(
            $jurnal->tipe,
            now()->toDateString(),
            $reversed,
            $keterangan.' — '.$jurnal->nomor,
            $jurnal,
            true
        );

        // Tandai jurnal lama sebagai dibatalkan tanpa meng-unpost agar pembalikan saling meniadakan (net 0)
        $jurnal->update([
            'keterangan' => '[DIBATALKAN] '.$jurnal->keterangan,
            'voided_at' => now(),
        ]);

        return $new;
    }

    /**
     * Saldo sebuah akun pada tanggal tertentu.
     */
    public static function saldoAkun(int $akunId, ?string $sampaiTanggal = null): float
    {
        $akun = AkunPerkiraan::findOrFail($akunId);
        $query = JurnalItem::where('jurnal_items.akun_id', $akunId)
            ->join('jurnal_umum', 'jurnal_items.jurnal_id', '=', 'jurnal_umum.id')
            ->where('jurnal_umum.is_posted', true)
            ->where(JurnalUmum::kondisiBukanJurnalVoid());

        if ($sampaiTanggal) {
            $query->where('jurnal_umum.tanggal', '<=', $sampaiTanggal);
        }

        $debit = (float) $query->sum('jurnal_items.debit');
        $kredit = (float) $query->sum('jurnal_items.kredit');

        if ($akun->saldo_normal === 'debit') {
            return $debit - $kredit;
        }

        return $kredit - $debit;
    }
}
