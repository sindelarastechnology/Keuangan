<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\StoreMutasiBankRequest;
use App\Models\MutasiBank;
use App\Models\Rekening;
use App\Services\JournalService;
use App\Services\NomorGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutasiBankController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'mutasi_bank_kolom';

    public const KOLOM_OPTIONS = [
        'nomor' => 'Nomor',
        'tanggal' => 'Tanggal',
        'dari' => 'Transfer Dari',
        'tujuan' => 'Ke',
        'nominal' => 'Nominal',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['nomor', 'tanggal', 'dari', 'tujuan', 'nominal'];

    public const KOLOM_WAJIB = ['aksi'];

    public function index(Request $request)
    {
        $mutasi = MutasiBank::with(['rekeningAsal', 'rekeningTujuan'])
            ->when($request->filled('dari'), fn ($q) => $q->where('tanggal', '>=', $request->dari))
            ->when($request->filled('sampai'), fn ($q) => $q->where('tanggal', '<=', $request->sampai))
            ->orderByDesc('tanggal')
            ->paginate(15)->withQueryString();

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif();

        return view('transaksi.mutasi-bank.index', compact('mutasi', 'kolomOptions', 'kolomAktif'));
    }

    public function create()
    {
        $rekenings = Rekening::aktif()->orderBy('nama')->get();
        $saldoMap = $rekenings->pluck('saldo', 'id');
        $namaMap = $rekenings->pluck('nama', 'id');

        return view('transaksi.mutasi-bank.create', compact('rekenings', 'saldoMap', 'namaMap'));
    }

    public function store(StoreMutasiBankRequest $request)
    {
        $data = $request->validated();

        $rekeningAsal = Rekening::findOrFail($data['rekening_asal_id']);
        $rekeningTujuan = Rekening::findOrFail($data['rekening_tujuan_id']);

        $saldoKurang = (float) $rekeningAsal->saldo < (float) $data['nominal'];

        DB::beginTransaction();
        try {
            $mutasi = new MutasiBank;
            $mutasi->nomor = NomorGenerator::generate('MB', $data['tanggal']);
            $mutasi->tanggal = $data['tanggal'];
            $mutasi->rekening_asal_id = $data['rekening_asal_id'];
            $mutasi->rekening_tujuan_id = $data['rekening_tujuan_id'];
            $mutasi->nominal = $data['nominal'];
            $mutasi->keterangan = $data['keterangan'] ?? null;
            $mutasi->save();

            $jurnalItems = [
                ['akun_id' => $rekeningTujuan->akun_id, 'debit' => $data['nominal'], 'kredit' => 0],
                ['akun_id' => $rekeningAsal->akun_id, 'debit' => 0, 'kredit' => $data['nominal']],
            ];

            JournalService::post('mutasi_bank', $data['tanggal'], $jurnalItems, $data['keterangan'] ?? 'Transfer Rekening '.$mutasi->nomor, $mutasi);

            DB::commit();

            $redirect = redirect()->route('mutasi-bank.index')->with('success', 'Transfer rekening berhasil dicatat.');

            if ($saldoKurang) {
                return $redirect->with('warning', 'Saldo rekening asal tidak mencukupi ('.formatRupiah($rekeningAsal->saldo).'). Transfer tetap dicatat, namun saldo rekening menjadi minus.');
            }

            return $redirect;
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(MutasiBank $mutasiBank)
    {
        $mutasiBank->load(['rekeningAsal', 'rekeningTujuan', 'jurnal.items.akun']);

        return view('transaksi.mutasi-bank.show', compact('mutasiBank'));
    }

    public function destroy(MutasiBank $mutasiBank)
    {
        DB::beginTransaction();
        try {
            if ($mutasiBank->jurnal) {
                JournalService::void($mutasiBank->jurnal, 'Pembatalan transfer rekening');
            }
            $mutasiBank->delete();
            DB::commit();

            return redirect()->route('mutasi-bank.index')->with('success', 'Mutasi bank berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }
}
