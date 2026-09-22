<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NeracaSaldoExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $sampai;

    protected $data;

    public function __construct(string $sampai)
    {
        $this->sampai = $sampai;
        $this->data = ReportService::neracaSaldo($sampai);
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->data['rows'] as $row) {
            $rows[] = [
                $row['akun']->kode,
                $row['akun']->nama,
                $row['debit'] > 0 ? $row['debit'] : '',
                $row['kredit'] > 0 ? $row['kredit'] : '',
            ];
        }

        $rows[] = [];
        $rows[] = ['', 'TOTAL', $this->data['total_debit'], $this->data['total_kredit']];
        $rows[] = [];
        $rows[] = ['', 'Status', $this->data['is_balanced'] ? 'Neraca Seimbang (Balanced)' : 'Neraca Tidak Seimbang'];

        return $rows;
    }

    public function headings(): array
    {
        return [
            ['Neraca Saldo (Trial Balance)'],
            ['Per '.formatTanggalSingkat($this->sampai)],
            [],
            ['Kode', 'Nama Akun', 'Debit', 'Kredit'],
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
        return 'Neraca Saldo';
    }
}
