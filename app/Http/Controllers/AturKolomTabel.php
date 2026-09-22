<?php

namespace App\Http\Controllers;

use App\Services\KolomTabelService;
use Illuminate\Http\Request;

/**
 * Kemampuan "Atur Kolom" pada halaman daftar (index).
 *
 * Setiap controller yang memakai trait ini wajib mendefinisikan konstanta:
 * - KOLOM_KUNCI  : kunci konfigurasi di tabel pengaturan (mis. 'kas_masuk_kolom')
 * - KOLOM_OPTIONS: peta [key => label] seluruh kolom yang bisa dipilih
 * - KOLOM_DEFAULT: kolom yang tampil sebelum pernah diatur
 * - KOLOM_WAJIB  : kolom yang tidak boleh disembunyikan (mis. ['aksi'])
 */
trait AturKolomTabel
{
    public function kolomAktif(?string $kunci = null): array
    {
        return KolomTabelService::aktif($kunci ?? self::KOLOM_KUNCI, self::KOLOM_OPTIONS, self::KOLOM_DEFAULT, self::KOLOM_WAJIB);
    }

    public function simpanKolom(Request $request, ?string $kunci = null)
    {
        KolomTabelService::simpan($request, $kunci ?? self::KOLOM_KUNCI, self::KOLOM_OPTIONS, self::KOLOM_WAJIB);

        return back()->with('success', 'Pengaturan kolom berhasil disimpan.');
    }
}
