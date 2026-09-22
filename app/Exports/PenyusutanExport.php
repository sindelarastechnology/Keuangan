<?php

namespace App\Exports;

use App\Services\ReportService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenyusutanExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $sampai;

    protected $data;

    public function __construct(string $sampai)
    {
        $this->sampai = $sampai;
        $this->data = ReportService::penyusutan($sampai);
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->data['rows'] as $row) {
            $aset = $row['aset'];
            $rows[] = [
                $aset->kode,
                $aset->nama,
                $aset->kategori ?? '-',
                $aset->tanggal_perolehan ? Carbon::parse($aset->tanggal_perolehan)->format('d/m/Y') : '-',
                (float) $aset->harga_perolehan,
                (float) ($aset->nilai_residu ?? 0),
                $aset->masa_manfaat_bulan,
                $aset->beban_bulanan,
                $row['akumulasi'],
                $row['nilai_buku'],
            ];
        }

        $rows[] = [];
        $rows[] = ['Total', '', '', '', $this->data['total_perolehan'], '', '', '', $this->data['total_akumulasi'], $this->data['total_nilai_buku']];

        return $rows;
    }

    public function headings(): array
    {
        return [
            ['Laporan Penyusutan Aset Tetap'],
            ['Sampai dengan: '.formatTanggalSingkat($this->sampai)],
            [],
            ['Kode', 'Nama Aset', 'Kategori', 'Tgl Beli', 'Harga Beli', 'Perkiraan Jual Nanti', 'Lama (bln)', 'Biaya/Bulan', 'Total Penyusutan', 'Sisa Nilai'],
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
        return 'Penyusutan';
    }
}
