<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Neraca</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 16px; margin: 0; }
        .muted { color: #6b7280; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 8px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-weight: bold; text-transform: uppercase; font-size: 10px; color: #6b7280; border-bottom: 1px solid #d1d5db; }
        th.r, td.r { text-align: right; }
        .section { font-weight: bold; padding-top: 12px; }
        .aset td.section { color: #059669; }
        .kew td.section { color: #d97706; }
        .modal td.section { color: #2563eb; }
        td { padding: 3px 0; }
        td.item { padding-left: 12px; }
        td.total { font-weight: bold; border-bottom: 1px solid #d1d5db; padding-bottom: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $namaPerusahaan }}</h1>
        <p class="muted">Neraca per {{ formatTanggal($sampai) }}</p>
    </div>

    <table>
        <thead>
            <tr><th>Pos</th><th class="r">Debit</th><th class="r">Kredit</th></tr>
        </thead>
        <tbody>
            <tr class="aset"><td colspan="3" class="section">ASET</td></tr>
            @forelse($data['aset'] as $a)
                <tr><td class="item">{{ $a['akun']->nama }}</td><td class="r">{{ formatRupiah($a['saldo']) }}</td><td></td></tr>
            @empty
                <tr><td class="item muted">Tidak ada aset</td><td></td><td></td></tr>
            @endforelse
            <tr><td class="total item">Total Aset</td><td class="total r">{{ formatRupiah($data['total_aset']) }}</td><td></td></tr>

            <tr class="kew"><td colspan="3" class="section">KEWAJIBAN</td></tr>
            @forelse($data['kewajiban'] as $k)
                <tr><td class="item">{{ $k['akun']->nama }}</td><td></td><td class="r">{{ formatRupiah($k['saldo']) }}</td></tr>
            @empty
                <tr><td class="item muted">Tidak ada kewajiban</td><td></td><td></td></tr>
            @endforelse
            <tr><td class="total item">Total Kewajiban</td><td></td><td class="total r">{{ formatRupiah($data['total_kewajiban']) }}</td></tr>

            <tr class="modal"><td colspan="3" class="section">MODAL</td></tr>
            @forelse($data['modal'] as $m)
                <tr><td class="item">{{ $m['akun']->nama }}</td><td></td><td class="r">{{ formatRupiah($m['saldo']) }}</td></tr>
            @empty
                <tr><td class="item muted">Tidak ada modal</td><td></td><td></td></tr>
            @endforelse
            <tr><td class="total item">Total Modal</td><td></td><td class="total r">{{ formatRupiah($data['total_modal']) }}</td></tr>
        </tbody>
    </table>
</body>
</html>
