<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice Penjualan - {{ $penjualan->nomor }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 8px; margin-bottom: 14px; }
        h1 { font-size: 16px; margin: 0; }
        .title { font-size: 14px; font-weight: bold; color: #2563eb; margin: 2px 0 0 0; }
        .muted { color: #6b7280; }
        .meta { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .meta td { padding: 2px 0; vertical-align: top; }
        .meta .label { color: #6b7280; width: 1%; white-space: nowrap; padding-right: 12px; }
        table.items { width: 100%; border-collapse: collapse; }
        table.items th { text-align: left; font-weight: bold; text-transform: uppercase; font-size: 10px; color: #6b7280; border-bottom: 1px solid #d1d5db; padding: 4px 6px; }
        table.items th.r, table.items td.r { text-align: right; }
        table.items td { padding: 5px 6px; border-bottom: 1px solid #f3f4f6; }
        table.items .it { text-align: center; }
        tfoot td { padding: 4px 6px; }
        tfoot .total-line td { font-weight: bold; border-top: 1px solid #d1d5db; }
        tfoot .grand td { font-weight: bold; font-size: 13px; background: #f3f4f6; }
        .footer { margin-top: 24px; color: #6b7280; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $namaPerusahaan }}</h1>
        <p class="muted">{{ $alamatPerusahaan }}</p>
        <p class="title">INVOICE PENJUALAN</p>
        <p class="muted">No. {{ $penjualan->nomor }}</p>
    </div>

    <table class="meta">
        <tr>
            <td>
                <table>
                    <tr><td class="label">Tanggal</td><td>{{ formatTanggal($penjualan->tanggal) }}</td></tr>
                    <tr><td class="label">Customer</td><td>{{ $penjualan->customer->nama }}</td></tr>
                    <tr><td class="label">Metode Bayar</td><td>{{ ucfirst($penjualan->metode_bayar) }}</td></tr>
                    @if($penjualan->metode_bayar === 'tunai' && $penjualan->rekening)
                        <tr><td class="label">Via</td><td>{{ $penjualan->rekening->nama }}</td></tr>
                    @endif
                    @if($penjualan->keterangan)
                        <tr><td class="label">Ket</td><td>{{ $penjualan->keterangan }}</td></tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Barang</th>
                <th class="it">Jumlah</th>
                <th class="r">Harga</th>
                <th class="r">Diskon</th>
                <th class="r">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan->items as $item)
                <tr>
                    <td>{{ $item->barang?->label ?? '-' }}</td>
                    <td class="it">{{ formatKuantitas($item->jumlah) }}</td>
                    <td class="r">{{ formatRupiah($item->harga_satuan) }}</td>
                    <td class="r">{{ $item->diskon > 0 ? formatRupiah($item->diskon) : '-' }}</td>
                    <td class="r">{{ formatRupiah($item->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-line"><td colspan="4" class="r">Subtotal</td><td class="r">{{ formatRupiah($penjualan->subtotal) }}</td></tr>
            @if($penjualan->diskon_nominal > 0)
                <tr><td colspan="4" class="r">Diskon</td><td class="r">- {{ formatRupiah($penjualan->diskon_nominal) }}</td></tr>
            @endif
            @if($penjualan->pajak_nominal > 0)
                <tr><td colspan="4" class="r">Pajak</td><td class="r">{{ formatRupiah($penjualan->pajak_nominal) }}</td></tr>
            @endif
            @if((float) $penjualan->ongkir > 0)
                <tr><td colspan="4" class="r">Ongkir</td><td class="r">{{ formatRupiah($penjualan->ongkir) }}</td></tr>
            @endif
            <tr class="grand"><td colspan="4" class="r">GRAND TOTAL</td><td class="r">{{ formatRupiah($penjualan->total) }}</td></tr>
        </tfoot>
    </table>

    <div class="footer">Dicetak {{ date('d/m/Y H:i') }} — Dokumen ini dihasilkan otomatis oleh sistem.</div>
</body>
</html>
