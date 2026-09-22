<?php

namespace App\Http\Controllers\Akuntansi;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Models\BbPiutang;
use App\Models\Customer;
use Illuminate\Http\Request;

class BbPiutangController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'bb_piutang_kolom';

    public const KOLOM_OPTIONS = [
        'nama' => 'Customer',
        'debit' => 'Debit',
        'kredit' => 'Kredit',
        'saldo' => 'Saldo',
        'detail' => 'Detail',
    ];

    public const KOLOM_DEFAULT = ['nama', 'debit', 'kredit', 'saldo', 'detail'];

    public const KOLOM_WAJIB = ['detail'];

    public function index(Request $request)
    {
        $customers = Customer::orderBy('nama')->get();
        $customer = null;
        $piutang = collect();
        $saldoAwal = 0;
        $saldoAkhir = 0;

        $customerId = $request->filled('customer_id') ? $request->customer_id : null;
        $dari = $request->filled('dari') ? $request->dari : null;
        $sampai = $request->filled('sampai') ? $request->sampai : now()->toDateString();

        if ($customerId) {
            $customer = Customer::findOrFail($customerId);

            if ($dari) {
                $lastBefore = BbPiutang::where('customer_id', $customer->id)
                    ->where('tanggal', '<', $dari)
                    ->orderByDesc('tanggal')->orderByDesc('id')
                    ->value('saldo');
                $saldoAwal = (float) ($lastBefore ?? 0);
            }

            $piutang = BbPiutang::with('customer')
                ->where('customer_id', $customer->id)
                ->orderBy('tanggal')->orderBy('id')
                ->when($dari, fn ($q) => $q->whereBetween('tanggal', [$dari, $sampai]), fn ($q) => $q->where('tanggal', '<=', $sampai))
                ->get();

            $last = BbPiutang::where('customer_id', $customer->id)
                ->where('tanggal', '<=', $sampai)
                ->orderByDesc('tanggal')->orderByDesc('id')->value('saldo');
            $saldoAkhir = (float) ($last ?? $saldoAwal);
        }

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif();

        return view('akuntansi.bb-piutang.index', compact('customers', 'customer', 'piutang', 'saldoAwal', 'saldoAkhir', 'dari', 'sampai', 'kolomOptions', 'kolomAktif'));
    }
}
