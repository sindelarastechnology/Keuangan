<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ArusKasExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $dari;

    protected $sampai;

    protected $data;

    public function __construct(string $dari, string $sampai)
    {
        $this->dari = $dari;
        $this->sampai = $sampai;
        $this->data = ReportService::arusKas($dari, $sampai);
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = ['Kas Awal Periode', $this->data['kas_awal']];
        $rows[] = [];

        $rows[] = ['ARUS KAS DARI AKTIVITAS OPERASIONAL'];
        $rows[] = ['Laba Rugi', $this->data['laba_rugi']];
        $rows[] = ['Penyusutan (Non-Kas)', $this->data['penyusutan']];
        $rows[] = ['Perubahan Piutang', -$this->data['perubahan_piutang']];
        $rows[] = ['Perubahan Persediaan', -$this->data['perubahan_persediaan']];
        $rows[] = ['Perubahan Hutang', $this->data['perubahan_hutang']];
        $rows[] = ['Perubahan PPN Masukan', -$this->data['perubahan_ppn_masukan']];
        $rows[] = ['Perubahan PPN Keluaran', $this->data['perubahan_ppn_keluaran']];
        $rows[] = ['Laba/Rugi Penjualan Aset', -$this->data['laba_disposisi']];
        $rows[] = ['Kas dari Operasional', $this->data['kas_operasional']];
        $rows[] = [];

        $rows[] = ['ARUS KAS DARI AKTIVITAS INVESTASI'];
        $rows[] = ['Pembelian/Penjualan Aset Tetap', $this->data['kas_investasi']];
        $rows[] = ['Kas dari Investasi', $this->data['kas_investasi']];
        $rows[] = [];

        $rows[] = ['ARUS KAS DARI AKTIVITAS PENDANAAN'];
        $rows[] = ['Perubahan Modal', $this->data['kas_pendanaan']];
        $rows[] = ['Kas dari Pendanaan', $this->data['kas_pendanaan']];
        $rows[] = [];

        $rows[] = ['Perubahan Kas Bersih', $this->data['perubahan_kas']];
        $rows[] = ['Kas Akhir Periode', $this->data['kas_akhir']];

        if ($this->data['selisih'] > 0.01) {
            $rows[] = [];
            $rows[] = ['Selisih Rekonsiliasi', $this->data['selisih']];
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            ['Laporan Arus Kas'],
            ['Periode: '.formatTanggalSingkat($this->dari).' - '.formatTanggalSingkat($this->sampai)],
            [],
            ['Keterangan', 'Nominal'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['italic' => true]],
            4 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Arus Kas';
    }
}
