<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Neraca Saldo</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }
        h1 { font-size: 16px; margin: 0; }
        .muted { color: #6b7280; }
        .header { border-bottom: 2px solid #10b981; padding-bottom: 8px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th { background: #f1f5f9; padding: 6px 8px; text-align: left; font-weight: bold; border-bottom: 1px solid #cbd5e1; }
        td { padding: 4px 8px; border-bottom: 1px solid #e5e7eb; }
        .text-right { text-align: right; }
        .total { font-weight: bold; background: #f8fafc; border-top: 2px solid #475569; }
        .balanced { background: #d1fae5; color: #065f46; padding: 6px; margin-bottom: 12px; border: 1px solid #a7f3d0; }
        .unbalanced { background: #fee2e2; color: #991b1b; padding: 6px; margin-bottom: 12px; border: 1px solid #fecaca; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $namaPerusahaan }}</h1>
        <p class="muted">Neraca Saldo per {{ formatTanggalSingkat($sampai) }}</p>
    </div>

    @if($data['is_balanced'])
        <div class="balanced">✓ Neraca Seimbang (Balanced)</div>
    @else
        <div class="unbalanced">⚠ Neraca Tidak Seimbang - Selisih: {{ formatRupiah(abs($data['total_debit'] - $data['total_kredit'])) }}</div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Kode</th>
                <th style="width: 50%;">Nama Akun</th>
                <th class="text-right" style="width: 20%;">Debit</th>
                <th class="text-right" style="width: 20%;">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['rows'] as $row)
                <tr>
                    <td>{{ $row['akun']->kode }}</td>
                    <td>{{ $row['akun']->nama }}</td>
                    <td class="text-right">{{ $row['debit'] > 0 ? formatRupiah($row['debit']) : '-' }}</td>
                    <td class="text-right">{{ $row['kredit'] > 0 ? formatRupiah($row['kredit']) : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 16px;" class="muted">Tidak ada data akun</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="2">Total</td>
                <td class="text-right">{{ formatRupiah($data['total_debit']) }}</td>
                <td class="text-right">{{ formatRupiah($data['total_kredit']) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
