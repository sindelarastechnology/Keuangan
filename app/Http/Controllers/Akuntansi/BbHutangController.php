<?php

namespace App\Http\Controllers\Akuntansi;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Models\BbHutang;
use App\Models\Supplier;
use Illuminate\Http\Request;

class BbHutangController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'bb_hutang_kolom';

    public const KOLOM_OPTIONS = [
        'nama' => 'Supplier',
        'debit' => 'Debit',
        'kredit' => 'Kredit',
        'saldo' => 'Saldo',
        'detail' => 'Detail',
    ];

    public const KOLOM_DEFAULT = ['nama', 'debit', 'kredit', 'saldo', 'detail'];

    public const KOLOM_WAJIB = ['detail'];

    public function index(Request $request)
    {
        $suppliers = Supplier::orderBy('nama')->get();
        $supplier = null;
        $hutang = collect();
        $saldoAwal = 0;
        $saldoAkhir = 0;

        $supplierId = $request->filled('supplier_id') ? $request->supplier_id : null;
        $dari = $request->filled('dari') ? $request->dari : null;
        $sampai = $request->filled('sampai') ? $request->sampai : now()->toDateString();

        if ($supplierId) {
            $supplier = Supplier::findOrFail($supplierId);

            if ($dari) {
                $lastBefore = BbHutang::where('supplier_id', $supplier->id)
                    ->where('tanggal', '<', $dari)
                    ->orderByDesc('tanggal')->orderByDesc('id')
                    ->value('saldo');
                $saldoAwal = (float) ($lastBefore ?? 0);
            }

            $hutang = BbHutang::with('supplier')
                ->where('supplier_id', $supplier->id)
                ->orderBy('tanggal')->orderBy('id')
                ->when($dari, fn ($q) => $q->whereBetween('tanggal', [$dari, $sampai]), fn ($q) => $q->where('tanggal', '<=', $sampai))
                ->get();

            $last = BbHutang::where('supplier_id', $supplier->id)
                ->where('tanggal', '<=', $sampai)
                ->orderByDesc('tanggal')->orderByDesc('id')->value('saldo');
            $saldoAkhir = (float) ($last ?? $saldoAwal);
        }

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif();

        return view('akuntansi.bb-hutang.index', compact('suppliers', 'supplier', 'hutang', 'saldoAwal', 'saldoAkhir', 'dari', 'sampai', 'kolomOptions', 'kolomAktif'));
    }
}
