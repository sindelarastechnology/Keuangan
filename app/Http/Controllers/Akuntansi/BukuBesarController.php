<?php

namespace App\Http\Controllers\Akuntansi;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Models\AkunPerkiraan;
use App\Models\JurnalItem;
use App\Models\JurnalUmum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BukuBesarController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'buku_besar_kolom';

    public const KOLOM_OPTIONS = [
        'kode' => 'Kode',
        'nama' => 'Nama Akun',
        'jenis' => 'Jenis',
        'normal' => 'Normal',
        'debit' => 'Total Debit',
        'kredit' => 'Total Kredit',
        'saldo' => 'Saldo',
        'detail' => 'Detail',
    ];

    public const KOLOM_DEFAULT = ['kode', 'nama', 'jenis', 'normal', 'debit', 'kredit', 'saldo', 'detail'];

    public const KOLOM_WAJIB = ['detail'];

    public function index(Request $request)
    {
        $akunList = AkunPerkiraan::leaf()->orderBy('kode')->get();
        $akun = null;
        $entries = collect();
        $saldoAwal = 0;
        $saldoAkhir = 0;

        $akunId = $request->filled('akun_id') ? $request->akun_id : null;
        $dari = $request->filled('dari') ? $request->dari : null;
        $sampai = $request->filled('sampai') ? $request->sampai : now()->toDateString();

        if ($akunId) {
            $akun = AkunPerkiraan::findOrFail($akunId);

            if ($dari) {
                $debitAwal = (float) JurnalItem::where('akun_id', $akun->id)
                    ->whereHas('jurnal', fn ($q) => $q->tanpaVoid()->where('is_posted', true)->where('tanggal', '<', $dari))
                    ->sum('debit');
                $kreditAwal = (float) JurnalItem::where('akun_id', $akun->id)
                    ->whereHas('jurnal', fn ($q) => $q->tanpaVoid()->where('is_posted', true)->where('tanggal', '<', $dari))
                    ->sum('kredit');
                $saldoAwal = $akun->saldo_normal === 'debit' ? ($debitAwal - $kreditAwal) : ($kreditAwal - $debitAwal);
            }

            $entries = JurnalItem::with(['akun'])
                ->where('akun_id', $akunId)
                ->whereHas('jurnal', function ($q) use ($dari, $sampai) {
                    $q->tanpaVoid()->where('is_posted', true);
                    if ($dari) {
                        $q->whereBetween('tanggal', [$dari, $sampai]);
                    } else {
                        $q->where('tanggal', '<=', $sampai);
                    }
                })
                ->join('jurnal_umum', 'jurnal_items.jurnal_id', '=', 'jurnal_umum.id')
                ->select('jurnal_items.*', 'jurnal_umum.tanggal as jtanggal', 'jurnal_umum.nomor as jnomor', 'jurnal_umum.keterangan as jket')
                ->orderBy('jurnal_umum.tanggal')
                ->orderBy('jurnal_items.id')
                ->get();

            $saldo = $saldoAwal;
            foreach ($entries as $e) {
                if ($akun->saldo_normal === 'debit') {
                    $saldo += (float) $e->debit - (float) $e->kredit;
                } else {
                    $saldo += (float) $e->kredit - (float) $e->debit;
                }
                $e->saldo_berjalan = $saldo;
            }
            $saldoAkhir = $saldo;
        } else {
            $entries = AkunPerkiraan::leaf()->orderBy('kode')
                ->leftJoin('jurnal_items', 'akun_perkiraan.id', '=', 'jurnal_items.akun_id')
                ->leftJoin('jurnal_umum', function ($join) use ($dari, $sampai) {
                    $join->on('jurnal_items.jurnal_id', '=', 'jurnal_umum.id')
                        ->where('jurnal_umum.is_posted', true)
                        ->where(JurnalUmum::kondisiBukanJurnalVoid());
                    if ($dari) {
                        $join->whereBetween('jurnal_umum.tanggal', [$dari, $sampai]);
                    } else {
                        $join->where('jurnal_umum.tanggal', '<=', $sampai);
                    }
                })
                ->groupBy('akun_perkiraan.id', 'akun_perkiraan.kode', 'akun_perkiraan.nama', 'akun_perkiraan.jenis', 'akun_perkiraan.saldo_normal')
                ->select([
                    'akun_perkiraan.id',
                    'akun_perkiraan.kode',
                    'akun_perkiraan.nama',
                    'akun_perkiraan.jenis',
                    'akun_perkiraan.saldo_normal',
                    DB::raw('COALESCE(SUM(jurnal_items.debit), 0) as total_debit'),
                    DB::raw('COALESCE(SUM(jurnal_items.kredit), 0) as total_kredit'),
                ])
                ->get();
        }

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif();

        return view('akuntansi.buku-besar.index', compact('akunList', 'akun', 'entries', 'saldoAwal', 'saldoAkhir', 'dari', 'sampai', 'kolomOptions', 'kolomAktif'));
    }
}
