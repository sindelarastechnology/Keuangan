<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penyusutan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }
        h1 { font-size: 16px; margin: 0; }
        .muted { color: #6b7280; }
        .header { border-bottom: 2px solid #10b981; padding-bottom: 8px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f9fafb; text-align: left; font-size: 9px; text-transform: uppercase; padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
        td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; }
        .num { text-align: right; }
        .bold { font-weight: bold; }
        tfoot td { border-top: 2px solid #d1d5db; border-bottom: none; font-weight: bold; }
        tfoot .total { font-size: 10px; text-transform: uppercase; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $namaPerusahaan }}</h1>
        <p class="muted">Laporan Penyusutan Aset Tetap - Sampai dengan {{ formatTanggal($sampai) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Aset</th>
                <th>Tgl Beli</th>
                <th class="num">Harga Beli</th>
                <th class="num">Biaya/Bulan</th>
                <th class="num">Total Penyusutan</th>
                <th class="num">Sisa Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['rows'] as $row)
                @php $aset = $row['aset']; @endphp
                <tr>
                    <td>{{ $aset->kode }}</td>
                    <td>{{ $aset->nama }}</td>
                    <td>{{ formatTanggalSingkat($aset->tanggal_perolehan) }}</td>
                    <td class="num">{{ number_format((float) $aset->harga_perolehan, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format((float) $aset->beban_bulanan, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($row['akumulasi'], 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($row['nilai_buku'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center; padding: 24px;">Belum ada data aset.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="total">Total</td>
                <td class="num">{{ number_format($data['total_perolehan'], 0, ',', '.') }}</td>
                <td></td>
                <td class="num">{{ number_format($data['total_akumulasi'], 0, ',', '.') }}</td>
                <td class="num">{{ number_format($data['total_nilai_buku'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>