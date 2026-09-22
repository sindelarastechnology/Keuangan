<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NeracaExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $sampai;

    protected $data;

    public function __construct(string $sampai)
    {
        $this->sampai = $sampai;
        $this->data = ReportService::neraca($sampai);
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = ['ASET'];
        foreach ($this->data['aset'][0] as $a) {
            $rows[] = [$a['akun']->kode, $a['akun']->nama, $a['saldo']];
        }
        $rows[] = ['', 'Total Aset', $this->data['aset'][1]];
        $rows[] = [];

        $rows[] = ['KEWAJIBAN'];
        foreach ($this->data['kewajiban'][0] as $k) {
            $rows[] = [$k['akun']->kode, $k['akun']->nama, $k['saldo']];
        }
        $rows[] = ['', 'Total Kewajiban', $this->data['kewajiban'][1]];
        $rows[] = [];

        $rows[] = ['EKUITAS'];
        foreach ($this->data['modal'][0] as $m) {
            $rows[] = [$m['akun']->kode, $m['akun']->nama, $m['saldo']];
        }
        $rows[] = ['', 'Total Ekuitas', $this->data['modal'][1]];
        $rows[] = [];

        $rows[] = ['', 'TOTAL KEWAJIBAN + EKUITAS', $this->data['kewajiban'][1] + $this->data['modal'][1]];
        $rows[] = [];
        $rows[] = ['', 'Status', $this->data['is_balanced'] ? 'Neraca Seimbang (Balanced)' : 'Neraca Tidak Seimbang'];

        return $rows;
    }

    public function headings(): array
    {
        return [
            ['Laporan Neraca'],
            ['Per '.formatTanggalSingkat($this->sampai)],
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
        return 'Neraca';
    }
}
