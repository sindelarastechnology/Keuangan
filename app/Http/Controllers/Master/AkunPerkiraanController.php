<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Models\AkunPerkiraan;
use App\Models\Aset;
use App\Models\Rekening;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class AkunPerkiraanController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'akun_perkiraan_kolom';

    public const KOLOM_OPTIONS = [
        'kode' => 'Kode',
        'nama' => 'Nama Akun',
        'jenis' => 'Jenis',
        'kelompok' => 'Kelompok',
        'posisi' => 'Saldo Normal',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['kode', 'nama', 'jenis', 'kelompok', 'posisi'];

    public const KOLOM_WAJIB = ['aksi'];

    public function index(Request $request)
    {
        $query = AkunPerkiraan::with('parent')->orderBy('kode');

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->cari.'%')
                    ->orWhere('kode', 'like', '%'.$request->cari.'%');
            });
        }

        $akun = $query->paginate(15)->withQueryString();

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif();

        return view('master.akun-perkiraan.index', compact('akun', 'kolomOptions', 'kolomAktif'));
    }

    public function create()
    {
        $parents = AkunPerkiraan::orderBy('kode')->get();
        $akun = null;

        return view('master.akun-perkiraan.form', compact('parents', 'akun'));
    }

    public function store(Request $request)
    {
        $data = $this->validate($request);
        AkunPerkiraan::create($data);

        return redirect()->route('akun-perkiraan.index')->with('success', 'Akun perkiraan berhasil ditambahkan.');
    }

    public function edit(AkunPerkiraan $akunPerkiraan)
    {
        $akun = $akunPerkiraan;
        $parents = AkunPerkiraan::where('id', '!=', $akun->id)->orderBy('kode')->get();

        return view('master.akun-perkiraan.form', compact('akun', 'parents'));
    }

    public function update(Request $request, AkunPerkiraan $akunPerkiraan)
    {
        $data = $this->validate($request, $akunPerkiraan->id);

        if (! empty($data['parent_id']) && $this->terlibatSiklusParent($akunPerkiraan, (int) $data['parent_id'])) {
            return back()->withErrors(['parent_id' => 'Parent tidak boleh merupakan akun itu sendiri atau salah satu turunannya.'])->withInput();
        }

        $akunPerkiraan->update($data);

        return redirect()->route('akun-perkiraan.index')->with('success', 'Akun perkiraan berhasil diperbarui.');
    }

    public function destroy(AkunPerkiraan $akunPerkiraan)
    {
        $used = $akunPerkiraan->jurnalItems()->exists()
            || Rekening::where('akun_id', $akunPerkiraan->id)->exists()
            || Aset::where('akun_aset_id', $akunPerkiraan->id)
                ->orWhere('akun_akumulasi_id', $akunPerkiraan->id)
                ->orWhere('akun_beban_id', $akunPerkiraan->id)
                ->exists();

        if ($used) {
            return back()->with('error', 'Akun memiliki transaksi / dipakai data lain dan tidak dapat dihapus.');
        }

        try {
            $akunPerkiraan->delete();
        } catch (QueryException $e) {
            return back()->with('error', 'Akun masih direferensikan data lain dan tidak dapat dihapus.');
        }

        return redirect()->route('akun-perkiraan.index')->with('success', 'Akun perkiraan berhasil dihapus.');
    }

    /**
     * Cek apakah memilih parent tertentu akan menciptakan siklus (parent adalah
     * turunan dari akun saat ini, langsung maupun tidak langsung).
     */
    private function terlibatSiklusParent(AkunPerkiraan $akun, int $calonParentId): bool
    {
        $current = (int) $calonParentId;

        while ($current > 0) {
            if ($current === (int) $akun->id) {
                return true;
            }

            $current = (int) AkunPerkiraan::where('id', $current)->value('parent_id');
        }

        return false;
    }

    private function validate(Request $request, $id = null): array
    {
        return $request->validate([
            'kode' => 'required|string|max:20|unique:akun_perkiraan,kode'.($id ? ",$id" : ''),
            'nama' => 'required|string|max:150',
            'jenis' => 'required|in:aset,kewajiban,modal,pendapatan,beban',
            'saldo_normal' => 'required|in:debit,kredit',
            'is_header' => 'boolean',
            'parent_id' => 'nullable|exists:akun_perkiraan,id',
            'is_aktif' => 'boolean',
            'keterangan' => 'nullable|string',
        ]) + ['is_header' => $request->boolean('is_header'), 'is_aktif' => $request->boolean('is_aktif', true)];
    }
}
