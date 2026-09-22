<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LabaRugiExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $dari;

    protected $sampai;

    protected $data;

    public function __construct(string $dari, string $sampai)
    {
        $this->dari = $dari;
        $this->sampai = $sampai;
        $this->data = ReportService::labaRugi($dari, $sampai);
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = ['PENDAPATAN'];
        foreach ($this->data['pendapatan'] as $p) {
            $rows[] = [$p['akun']->kode, $p['akun']->nama, $p['saldo']];
        }
        $rows[] = ['', 'Total Pendapatan', $this->data['total_pendapatan']];
        $rows[] = [];

        $rows[] = ['BEBAN'];
        foreach ($this->data['beban'] as $b) {
            $rows[] = [$b['akun']->kode, $b['akun']->nama, $b['saldo']];
        }
        $rows[] = ['', 'Total Beban', $this->data['total_beban']];
        $rows[] = [];

        $rows[] = ['', 'LABA / RUGI BERSIH', $this->data['laba_rugi']];

        return $rows;
    }

    public function headings(): array
    {
        return [
            ['Laporan Laba Rugi'],
            ['Periode: '.formatTanggalSingkat($this->dari).' - '.formatTanggalSingkat($this->sampai)],
            [],
            ['Kode', 'Akun', 'Nominal'],
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
        return 'Laba Rugi';
    }
}
