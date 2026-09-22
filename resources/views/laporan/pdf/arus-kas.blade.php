<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Arus Kas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 16px; margin: 0; }
        .muted { color: #6b7280; }
        .header { border-bottom: 2px solid #10b981; padding-bottom: 8px; margin-bottom: 16px; }
        .row { display: flex; justify-content: space-between; padding: 3px 0; }
        .indent { padding-left: 16px; }
        .section { font-weight: bold; margin-top: 12px; text-transform: uppercase; font-size: 11px; }
        .total { border-top: 1px solid #d1d5db; margin-top: 4px; padding-top: 4px; font-weight: bold; }
        .summary { border: 1px solid #10b981; padding: 8px; margin-top: 16px; }
        .summary .row { font-weight: bold; font-size: 13px; }
        .green { color: #059669; }
        .red { color: #dc2626; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $namaPerusahaan }}</h1>
        <p class="muted">Laporan Arus Kas: {{ formatTanggalSingkat($dari) }} - {{ formatTanggalSingkat($sampai) }}</p>
    </div>

    <div class="row total"><span>Kas Awal Periode</span><span>{{ formatRupiah($data['kas_awal']) }}</span></div>

    <div class="section">Arus Kas dari Aktivitas Operasional</div>
    <div class="row indent"><span>Laba Rugi</span><span class="{{ $data['laba_rugi'] >= 0 ? 'green' : 'red' }}">{{ formatRupiah($data['laba_rugi']) }}</span></div>
    <div class="row indent"><span>Penyusutan (Non-Kas)</span><span class="green">{{ formatRupiah($data['penyusutan']) }}</span></div>
    <div class="row indent"><span>Perubahan Piutang</span><span class="{{ $data['perubahan_piutang'] <= 0 ? 'green' : 'red' }}">{{ formatRupiah(-$data['perubahan_piutang']) }}</span></div>
    <div class="row indent"><span>Perubahan Persediaan</span><span class="{{ $data['perubahan_persediaan'] <= 0 ? 'green' : 'red' }}">{{ formatRupiah(-$data['perubahan_persediaan']) }}</span></div>
    <div class="row indent"><span>Perubahan Hutang</span><span class="{{ $data['perubahan_hutang'] >= 0 ? 'green' : 'red' }}">{{ formatRupiah($data['perubahan_hutang']) }}</span></div>
    <div class="row indent"><span>Perubahan PPN Masukan</span><span class="{{ $data['perubahan_ppn_masukan'] <= 0 ? 'green' : 'red' }}">{{ formatRupiah(-$data['perubahan_ppn_masukan']) }}</span></div>
    <div class="row indent"><span>Perubahan PPN Keluaran</span><span class="{{ $data['perubahan_ppn_keluaran'] >= 0 ? 'green' : 'red' }}">{{ formatRupiah($data['perubahan_ppn_keluaran']) }}</span></div>
    <div class="row indent"><span>{{ $data['laba_disposisi'] >= 0 ? 'Laba' : 'Rugi' }} Penjualan Aset</span><span class="{{ $data['laba_disposisi'] >= 0 ? 'green' : 'red' }}">{{ formatRupiah(-$data['laba_disposisi']) }}</span></div>
    <div class="row total"><span>Kas dari Operasional</span><span class="{{ $data['kas_operasional'] >= 0 ? 'green' : 'red' }}">{{ formatRupiah($data['kas_operasional']) }}</span></div>

    <div class="section">Arus Kas dari Aktivitas Investasi</div>
    <div class="row indent"><span>Pembelian/Penjualan Aset Tetap</span><span class="{{ $data['kas_investasi'] >= 0 ? 'green' : 'red' }}">{{ formatRupiah($data['kas_investasi']) }}</span></div>
    <div class="row total"><span>Kas dari Investasi</span><span class="{{ $data['kas_investasi'] >= 0 ? 'green' : 'red' }}">{{ formatRupiah($data['kas_investasi']) }}</span></div>

    <div class="section">Arus Kas dari Aktivitas Pendanaan</div>
    <div class="row indent"><span>Perubahan Modal</span><span class="{{ $data['kas_pendanaan'] >= 0 ? 'green' : 'red' }}">{{ formatRupiah($data['kas_pendanaan']) }}</span></div>
    <div class="row total"><span>Kas dari Pendanaan</span><span class="{{ $data['kas_pendanaan'] >= 0 ? 'green' : 'red' }}">{{ formatRupiah($data['kas_pendanaan']) }}</span></div>

    <div class="summary">
        <div class="row"><span>Perubahan Kas Bersih</span><span class="{{ $data['perubahan_kas'] >= 0 ? 'green' : 'red' }}">{{ formatRupiah($data['perubahan_kas']) }}</span></div>
        <div class="row" style="margin-top: 8px; font-size: 14px;"><span>Kas Akhir Periode</span><span>{{ formatRupiah($data['kas_akhir']) }}</span></div>
        @if($data['selisih'] > 0.01)
        <div class="row muted" style="font-size: 9px; margin-top: 4px;"><span>Selisih Rekonsiliasi</span><span>{{ formatRupiah($data['selisih']) }}</span></div>
        @endif
    </div>
</body>
</html>
