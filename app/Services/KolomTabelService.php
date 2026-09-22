<?php

namespace App\Services;

use App\Models\Pengaturan;
use Illuminate\Http\Request;

/**
 * Baca & simpan konfigurasi kolom tabel per halaman.
 *
 * Konfigurasi disimpan sebagai JSON di tabel pengaturan dengan kunci
 * seperti 'data_nama_kolom', 'gudang_kolom', 'rekening_kolom', dsb.
 */
class KolomTabelService
{
    /**
     * Kolom aktif untuk sebuah halaman (selalu mengikutsertakan kolom wajib).
     *
     * @param  array<string, string>  $opsi  Peta [key => label]
     * @param  array<int, string>  $default  Kolom bawaan saat belum pernah diatur
     * @param  array<int, string>  $selaluTampil  Kolom yang tidak bisa disembunyikan
     * @return array<int, string>
     */
    public static function aktif(string $kunci, array $opsi, array $default, array $selaluTampil = ['aksi']): array
    {
        $tersimpan = Pengaturan::tampil($kunci);

        if (! $tersimpan) {
            return self::gabung($default, $opsi, $selaluTampil);
        }

        $decoded = json_decode($tersimpan, true);

        if (! is_array($decoded)) {
            return self::gabung($default, $opsi, $selaluTampil);
        }

        return self::gabung($decoded, $opsi, $selaluTampil);
    }

    /**
     * Simpan konfigurasi kolom dari form modal.
     *
     * @param  array<string, string>  $opsi  Peta [key => label]
     * @param  array<int, string>  $selaluTampil  Kolom yang selalu dipaksa tampil
     */
    public static function simpan(Request $request, string $kunci, array $opsi, array $selaluTampil = ['aksi']): void
    {
        $kolom = $request->validate([
            'kolom' => 'sometimes|array',
            'kolom.*' => 'string',
        ])['kolom'] ?? [];

        $kolom = array_values(array_intersect($kolom, array_keys($opsi)));

        foreach ($selaluTampil as $wajib) {
            if (! in_array($wajib, $kolom, true)) {
                $kolom[] = $wajib;
            }
        }

        Pengaturan::atur($kunci, json_encode($kolom));
    }

    /**
     * @param  array<int, string>  $kolom
     * @param  array<string, string>  $opsi
     * @param  array<int, string>  $selaluTampil
     * @return array<int, string>
     */
    private static function gabung(array $kolom, array $opsi, array $selaluTampil): array
    {
        return array_values(array_unique(array_merge(array_intersect($kolom, array_keys($opsi)), $selaluTampil)));
    }
}
