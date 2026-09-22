<?php

namespace App\Services;

use App\Models\AkunPerkiraan;
use App\Models\Gudang;
use App\Models\Pengaturan;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Pengaturan sistem per-tenant (per-akun).
 *
 * Menyediakan akun penting (akun perkiraan default yang dipakai algoritma
 * posting jurnal, kas-kas, dan laporan) serta gudang default transaksi
 * pembelian/penjualan. Nilai diambil dari tabel pengaturan, menggantikan
 * pencarian akun hardcoded per kode.
 */
class PengaturanSistemService
{
    /**
     * Katalog akun penting yang dipakai sistem.
     *
     * @return array<string, array{label: string, kode: string, kodes?: string[], jenis: string[]}>
     */
    public static function katalogAkunPenting(): array
    {
        return [
            'kas' => ['label' => 'Kas', 'kode' => '111', 'jenis' => ['aset']],
            'bank' => ['label' => 'Bank', 'kode' => '112', 'jenis' => ['aset']],
            'piutang' => ['label' => 'Piutang Usaha', 'kode' => '113', 'jenis' => ['aset']],
            'persediaan' => ['label' => 'Persediaan', 'kode' => '115', 'kodes' => ['114'], 'jenis' => ['aset']],
            'ppn_masukan' => ['label' => 'PPN Masukan', 'kode' => '213', 'jenis' => ['aset']],
            'aset_tetap' => ['label' => 'Aset Tetap', 'kode' => '121', 'jenis' => ['aset']],
            'akumulasi_penyusutan' => ['label' => 'Akumulasi Penyusutan', 'kode' => '122', 'jenis' => ['aset']],
            'utang' => ['label' => 'Hutang Usaha', 'kode' => '211', 'jenis' => ['kewajiban']],
            'ppn_keluaran' => ['label' => 'PPN Keluaran', 'kode' => '212', 'jenis' => ['kewajiban']],
            'modal' => ['label' => 'Modal', 'kode' => '31', 'jenis' => ['modal']],
            'laba_ditahan' => ['label' => 'Laba Ditahan', 'kode' => '32', 'jenis' => ['modal']],
            'penjualan' => ['label' => 'Penjualan (Barang)', 'kode' => '411', 'kodes' => ['421'], 'jenis' => ['pendapatan']],
            'penjualan_jasa' => ['label' => 'Pendapatan Jasa', 'kode' => '412', 'kodes' => ['421'], 'jenis' => ['pendapatan']],
            'pendapatan_lain' => ['label' => 'Pendapatan Lain-lain', 'kode' => '421', 'jenis' => ['pendapatan']],
            'hpp' => ['label' => 'Harga Pokok Penjualan (HPP)', 'kode' => '51', 'jenis' => ['beban']],
            'beban_umum' => ['label' => 'Beban Umum', 'kode' => '52', 'kodes' => ['531'], 'jenis' => ['beban']],
            'beban_gaji' => ['label' => 'Beban Gaji', 'kode' => '521', 'kodes' => ['52', '531'], 'jenis' => ['beban']],
            'beban_sewa' => ['label' => 'Beban Sewa', 'kode' => '522', 'kodes' => ['52', '531'], 'jenis' => ['beban']],
            'beban_operasional' => ['label' => 'Beban Operasional', 'kode' => '523', 'kodes' => ['52', '531'], 'jenis' => ['beban']],
            'beban_transport' => ['label' => 'Beban Transportasi', 'kode' => '524', 'kodes' => ['52', '531'], 'jenis' => ['beban']],
            'beban_penyusutan' => ['label' => 'Beban Penyusutan', 'kode' => '526', 'jenis' => ['beban']],
            'rugi_lain' => ['label' => 'Rugi Lain-lain', 'kode' => '531', 'jenis' => ['beban']],
        ];
    }

    /**
     * Kode (kunci) pengaturan untuk sebuah role akun penting.
     */
    public static function keyAkun(string $role): string
    {
        return 'akun_'.$role;
    }

    /**
     * ID akun perkiraan untuk sebuah role akun penting, dengan prioritas:
     * pengaturan tersimpan -> kode default -> kode cadangan.
     */
    public static function akunId(string $role): ?int
    {
        $def = self::katalogAkunPenting()[$role] ?? null;

        if (! $def) {
            return null;
        }

        $saved = (int) Pengaturan::tampil(self::keyAkun($role), 0);

        if ($saved > 0 && AkunPerkiraan::whereKey($saved)->exists()) {
            return $saved;
        }

        foreach (array_merge([$def['kode']], $def['kodes'] ?? []) as $kode) {
            $id = AkunPerkiraan::where('kode', $kode)->value('id');

            if ($id) {
                return (int) $id;
            }
        }

        return null;
    }

    /**
     * ID akun untuk role yang wajib ada; exception bila tidak tersedia.
     */
    public static function akunIdOrFail(string $role): int
    {
        return self::akunId($role) ?? throw new RuntimeException(
            "Akun perkiraan '{$role}' belum tersedia. Tetapkan di Pengaturan > Pengaturan Sistem > Akun Penting."
        );
    }

    /**
     * Akun perkiraan kas/bank yang dipakai sistem: semua leaf aset yang kodenya
     * diawali kode akun kas atau bank terkonfigurasi, termasuk akun anak hasil
     * isolasi rekening (mis. 1121/1122/1111). Tanpa konfigurasi, kembalikan
     * semua leaf aset (perilaku lama).
     *
     * @return Collection<int, AkunPerkiraan>
     */
    public static function akunKasBank(): Collection
    {
        $kode = [];

        foreach ([self::akunId('kas'), self::akunId('bank')] as $id) {
            if (! $id) {
                continue;
            }

            $akun = AkunPerkiraan::find((int) $id);

            if ($akun) {
                $kode[] = (string) $akun->kode;
            }
        }

        if ($kode === []) {
            return AkunPerkiraan::leaf()->where('jenis', 'aset')->get();
        }

        return AkunPerkiraan::leaf()->where('jenis', 'aset')
            ->where(function ($q) use ($kode) {
                foreach ($kode as $prefix) {
                    $q->orWhere('kode', 'like', $prefix.'%');
                }
            })
            ->get();
    }

    /**
     * Gudang default transaksi.
     */
    public static function gudangId(string $key): ?int
    {
        $id = (int) Pengaturan::tampil($key, 0);

        if ($id > 0 && Gudang::whereKey($id)->exists()) {
            return $id;
        }

        return Gudang::utama()?->id;
    }

    public static function gudangPembelian(): ?int
    {
        return self::gudangId('gudang_default_pembelian');
    }

    public static function gudangPenjualan(): ?int
    {
        return self::gudangId('gudang_default_penjualan');
    }
}
