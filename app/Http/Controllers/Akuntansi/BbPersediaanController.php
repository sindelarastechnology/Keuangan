<?php

namespace App\Http\Controllers\Akuntansi;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BbPersediaan;
use Illuminate\Http\Request;

class BbPersediaanController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'bb_persediaan_kolom';

    public const KOLOM_OPTIONS = [
        'kode' => 'Kode',
        'nama' => 'Nama Barang',
        'satuan' => 'Satuan',
        'debit_qty' => 'Qty Masuk',
        'kredit_qty' => 'Qty Keluar',
        'saldo_qty' => 'Saldo Qty',
        'saldo_harga' => 'Saldo (Rp)',
        'detail' => 'Detail',
    ];

    public const KOLOM_DEFAULT = ['kode', 'nama', 'satuan', 'debit_qty', 'kredit_qty', 'saldo_qty', 'saldo_harga', 'detail'];

    public const KOLOM_WAJIB = ['detail'];

    public function index(Request $request)
    {
        $barang = Barang::aktif()->barang()->orderBy('nama')->get();
        $brg = null;
        $persediaan = collect();
        $saldoAwalQty = 0;
        $saldoAwalHarga = 0;
        $saldoAkhirQty = 0;
        $saldoAkhirHarga = 0;

        $barangId = $request->filled('barang_id') ? $request->barang_id : null;
        $dari = $request->filled('dari') ? $request->dari : null;
        $sampai = $request->filled('sampai') ? $request->sampai : now()->toDateString();

        if ($barangId) {
            $brg = Barang::findOrFail($barangId);

            if ($dari) {
                $lastBefore = BbPersediaan::where('barang_id', $brg->id)
                    ->where('tanggal', '<', $dari)
                    ->orderByDesc('tanggal')->orderByDesc('id')
                    ->first();
                if ($lastBefore) {
                    $saldoAwalQty = (float) $lastBefore->saldo_qty;
                    $saldoAwalHarga = (float) $lastBefore->saldo_harga;
                }
            }

            $persediaan = BbPersediaan::with('barang')
                ->where('barang_id', $brg->id)
                ->orderBy('tanggal')->orderBy('id')
                ->when($dari, fn ($q) => $q->whereBetween('tanggal', [$dari, $sampai]), fn ($q) => $q->where('tanggal', '<=', $sampai))
                ->get();

            $last = BbPersediaan::where('barang_id', $brg->id)
                ->where('tanggal', '<=', $sampai)
                ->orderByDesc('tanggal')->orderByDesc('id')->first();
            if ($last) {
                $saldoAkhirQty = (float) $last->saldo_qty;
                $saldoAkhirHarga = (float) $last->saldo_harga;
            } else {
                $saldoAkhirQty = $saldoAwalQty;
                $saldoAkhirHarga = $saldoAwalHarga;
            }
        }

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif();

        return view('akuntansi.bb-persediaan.index', compact('barang', 'brg', 'persediaan', 'saldoAwalQty', 'saldoAwalHarga', 'saldoAkhirQty', 'saldoAkhirHarga', 'dari', 'sampai', 'kolomOptions', 'kolomAktif'));
    }
}
