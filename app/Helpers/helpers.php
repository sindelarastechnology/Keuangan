<?php

use App\Models\Pengaturan;
use Carbon\Carbon;

if (! function_exists('formatRupiah')) {
    function formatRupiah($nilai): string
    {
        return 'Rp '.number_format((float) $nilai, 0, ',', '.');
    }
}

if (! function_exists('formatAngka')) {
    function formatAngka($nilai, int $desimal = 0): string
    {
        return number_format((float) $nilai, $desimal, ',', '.');
    }
}

if (! function_exists('formatKuantitas')) {
    function formatKuantitas($nilai): string
    {
        $hasil = number_format((float) $nilai, 2, '.', '');

        return str_replace('.', ',', rtrim(rtrim($hasil, '0'), '.'));
    }
}

if (! function_exists('formatTanggal')) {
    function formatTanggal($tanggal): string
    {
        if (! $tanggal) {
            return '-';
        }
        $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $date = Carbon::parse($tanggal);

        return $date->day.' '.$bulan[$date->month].' '.$date->year;
    }
}

if (! function_exists('formatTanggalSingkat')) {
    function formatTanggalSingkat($tanggal): string
    {
        if (! $tanggal) {
            return '-';
        }
        $date = Carbon::parse($tanggal);

        return $date->format('d/m/Y');
    }
}

if (! function_exists('namaBulan')) {
    function namaBulan($bulan): string
    {
        $names = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return $names[(int) $bulan] ?? $bulan;
    }
}

if (! function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        return Pengaturan::tampil($key, $default);
    }
}
