<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laba Rugi</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 16px; margin: 0; }
        .muted { color: #6b7280; }
        .header { border-bottom: 2px solid #10b981; padding-bottom: 8px; margin-bottom: 16px; }
        .row { display: flex; justify-content: space-between; padding: 3px 0; }
        .section { font-weight: bold; margin-top: 12px; text-transform: uppercase; font-size: 11px; }
        .total { border-top: 1px solid #d1d5db; margin-top: 4px; padding-top: 4px; font-weight: bold; }
        .laba { border: 1px solid #10b981; padding: 8px; margin-top: 16px; font-weight: bold; }
        .laba .row { font-size: 14px; }
        .green { color: #059669; }
        .red { color: #dc2626; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $namaPerusahaan }}</h1>
        <p class="muted">Laporan Laba Rugi: {{ formatTanggalSingkat($dari) }} - {{ formatTanggalSingkat($sampai) }}</p>
    </div>

    <div class="section">Pendapatan</div>
    @forelse($data['pendapatan'] as $p)
        <div class="row"><span>{{ $p['akun']->nama }}</span><span>{{ formatRupiah($p['saldo']) }}</span></div>
    @empty
        <div class="row muted"><span>Tidak ada pendapatan</span></div>
    @endforelse
    <div class="row total"><span>Total Pendapatan</span><span class="green">{{ formatRupiah($data['total_pendapatan']) }}</span></div>

    <div class="section">Beban</div>
    @forelse($data['beban'] as $b)
        <div class="row"><span>{{ $b['akun']->nama }}</span><span>{{ formatRupiah($b['saldo']) }}</span></div>
    @empty
        <div class="row muted"><span>Tidak ada beban</span></div>
    @endforelse
    <div class="row total"><span>Total Beban</span><span class="red">{{ formatRupiah($data['total_beban']) }}</span></div>

    <div class="laba">
        <div class="row">
            <span>Laba / Rugi Bersih</span>
            <span class="{{ $data['laba_rugi'] >= 0 ? 'green' : 'red' }}">{{ formatRupiah($data['laba_rugi']) }}</span>
        </div>
    </div>
</body>
</html>
