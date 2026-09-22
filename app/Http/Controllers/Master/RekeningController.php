<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\AkunPerkiraan;
use App\Models\Aset;
use App\Models\JurnalItem;
use App\Models\KasKeluar;
use App\Models\KasMasuk;
use App\Models\MutasiBank;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Rekening;
use App\Services\JournalService;
use App\Services\KolomTabelService;
use App\Services\PengaturanSistemService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekeningController extends Controller
{
    public const KOLOM_OPTIONS = [
        'jenis' => 'Jenis',
        'nama' => 'Nama',
        'nomor' => 'No. Rekening / Pemilik',
        'saldo' => 'Saldo',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['jenis', 'nama', 'nomor', 'saldo', 'aksi'];

    public function index(Request $request)
    {
        $rekenings = Rekening::with('akun')->orderBy('jenis')->orderBy('nama')
            ->when($request->filled('cari'), fn ($q) => $q->where('nama', 'like', '%'.$request->cari.'%'))
            ->when($request->filled('jenis'), fn ($q) => $q->where('jenis', $request->jenis))
            ->paginate(15)->withQueryString();

        return view('master.rekening.index', [
            'rekenings' => $rekenings,
            'kolomOptions' => self::KOLOM_OPTIONS,
            'kolomAktif' => self::kolomAktif(),
        ]);
    }

    public function simpanKolom(Request $request)
    {
        KolomTabelService::simpan($request, 'rekening_kolom', self::KOLOM_OPTIONS);

        return back()->with('success', 'Pengaturan kolom berhasil disimpan.');
    }

    public static function kolomAktif(): array
    {
        return KolomTabelService::aktif('rekening_kolom', self::KOLOM_OPTIONS, self::KOLOM_DEFAULT);
    }

    public function create()
    {
        $akunList = $this->akunList();

        return view('master.rekening.form', compact('akunList'));
    }

    public function store(Request $request)
    {
        $data = $this->validate($request);

        DB::beginTransaction();
        try {
            // Tanpa akun yang dipilih, siapkan akun kas/bank turunan khusus untuk rekening ini.
            if (empty($data['akun_id'] ?? null)) {
                $data['akun_id'] = $this->provisiAkunBaru($data['jenis'], $data['nama'])->id;
            }

            $rekening = Rekening::create($data);

            // Jika ada saldo awal > 0, buat jurnal pembuka (Debit Kas/Bank, Kredit Modal Awal)
            if ((float) $rekening->saldo_awal > 0) {
                $akunModal = PengaturanSistemService::akunId('modal');
                if ($akunModal) {
                    JournalService::post(
                        'manual',
                        now()->toDateString(),
                        [
                            ['akun_id' => $rekening->akun_id, 'debit' => (float) $rekening->saldo_awal, 'kredit' => 0, 'keterangan' => 'Saldo Awal '.$rekening->nama],
                            ['akun_id' => $akunModal, 'debit' => 0, 'kredit' => (float) $rekening->saldo_awal, 'keterangan' => 'Setoran Modal Awal Rekening '.$rekening->nama],
                        ],
                        'Saldo Awal Rekening '.$rekening->nama,
                        $rekening
                    );
                }
            }

            DB::commit();

            return redirect()->route('rekening.index')->with('success', 'Rekening berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Rekening $rekening)
    {
        $akunList = $this->akunList($rekening);
        $akunTerkunci = $this->rekeningSudahDipakai($rekening);

        return view('master.rekening.form', compact('rekening', 'akunList', 'akunTerkunci'));
    }

    public function update(Request $request, Rekening $rekening)
    {
        // Akun perkiraan tidak boleh diubah jika rekening sudah memakai akun GL/transaksi.
        if ($this->rekeningSudahDipakai($rekening)
            && filled($request->input('akun_id'))
            && (int) ($request->input('akun_id') ?? 0) !== (int) $rekening->akun_id) {
            return back()->withErrors(['akun_id' => 'Akun perkiraan tidak dapat diubah karena rekening sudah dipakai transaksi/jurnal.'])->withInput();
        }

        $validated = $this->validate($request, $rekening);

        // Bidang akun dikosongkan artinya mempertahankan akun yang sudah terpasang.
        $validated['akun_id'] = $validated['akun_id'] ?? $rekening->akun_id;

        $rekening->update($validated);

        return redirect()->route('rekening.index')->with('success', 'Rekening berhasil diperbarui.');
    }

    public function destroy(Rekening $rekening)
    {
        // Proteksi jika sudah digunakan dalam transaksi
        if (KasMasuk::where('rekening_id', $rekening->id)->exists() ||
            KasKeluar::where('rekening_id', $rekening->id)->exists() ||
            Pembelian::where('rekening_id', $rekening->id)->exists() ||
            Penjualan::where('rekening_id', $rekening->id)->exists() ||
            MutasiBank::where('rekening_asal_id', $rekening->id)->orWhere('rekening_tujuan_id', $rekening->id)->exists()) {
            return back()->with('error', 'Rekening ini telah memiliki riwayat transaksi dan tidak dapat dihapus.');
        }

        // Jurnal yang menyentuh akun rekening ini (termasuk jurnal saldo awal)
        // harus dicegah agar tidak meninggalkan jurnal yatim.
        if ($rekening->akun_id && JurnalItem::where('akun_id', $rekening->akun_id)->exists()) {
            return back()->with('error', 'Rekening ini terhubung dengan jurnal (termasuk saldo awal) dan tidak dapat dihapus.');
        }

        $rekening->delete();

        return redirect()->route('rekening.index')->with('success', 'Rekening berhasil dihapus.');
    }

    private function validate(Request $request, ?Rekening $rekening = null): array
    {
        return $request->validate([
            'jenis' => 'required|in:kas,bank',
            'nama' => 'required|string|max:150',
            'nomor_rekening' => 'nullable|string|max:50',
            'nama_pemilik' => 'nullable|string|max:150',
            'akun_id' => [
                'nullable', 'integer', 'exists:akun_perkiraan,id',
                function (string $attribute, mixed $value, Closure $fail) use ($rekening): void {
                    if ($value === null || $value === '' || $value === 0) {
                        return;
                    }

                    $akunId = (int) $value;

                    // Akun yang sudah terpasang pada rekening ini (termasuk akun legacy) tetap diizinkan.
                    if ($rekening && (int) $rekening->akun_id === $akunId) {
                        return;
                    }

                    if (! $this->akunDiizinkan($akunId)) {
                        $fail('Akun perkiraan harus berupa akun Kas/Bank yang tersedia.');

                        return;
                    }

                    if (Rekening::where('akun_id', $akunId)->exists()) {
                        $fail('Akun perkiraan tersebut sudah dipakai rekening lain. Setiap rekening wajib memakai akun yang berbeda.');
                    }
                },
            ],
            'saldo_awal' => 'required|numeric|min:0',
            'is_aktif' => 'boolean',
        ]) + ['is_aktif' => $request->boolean('is_aktif', true)];
    }

    /**
     * Akun Kas/Bank yang tersedia untuk rekening (leaf aset di bawah akun
     * role kas/bank). Label opsi yang sudah dipakai rekening lain diberi tanda.
     */
    private function akunList(?Rekening $kecuali = null): array
    {
        $eligible = AkunPerkiraan::leaf()->where('jenis', 'aset')->get()
            ->filter(fn (AkunPerkiraan $a) => $this->akunDiizinkan((int) $a->id));

        $used = Rekening::whereIn('akun_id', $eligible->pluck('id'))
            ->when($kecuali, fn ($q) => $q->whereKeyNot($kecuali->id))
            ->get(['akun_id', 'nama'])
            ->groupBy('akun_id')
            ->map(fn ($g) => $g->pluck('nama')->join(', '));

        return $eligible->mapWithKeys(fn (AkunPerkiraan $a) => [
            $a->id => $used->has($a->id) ? $a->nama.' (dipakai: '.$used[$a->id].')' : $a->nama,
        ])->all();
    }

    /**
     * Siapkan akun perkiraan kas/bank khusus untuk rekening baru bila tidak ada
     * akun bebas yang dipilih: akun leaf aset turunan akun kas/bank dengan kode
     * berikutnya (mis. 1123), agar setiap rekening memakai akun yang berbeda.
     */
    private function provisiAkunBaru(string $jenis, string $nama): AkunPerkiraan
    {
        $isBank = $jenis === 'bank';
        $root = AkunPerkiraan::whereKey((int) PengaturanSistemService::akunId($isBank ? 'bank' : 'kas'))->first();

        $kodeBasis = $root?->kode ?? ($isBank ? '112' : '111');
        $parentId = $root?->parent_id ?: AkunPerkiraan::where('kode', '11')->value('id');
        $namaAkun = ($isBank ? 'Bank' : 'Kas').' '.$nama;

        $n = 1;

        do {
            $kode = $kodeBasis.$n;
            $n++;
        } while (AkunPerkiraan::where('kode', $kode)->exists());

        return AkunPerkiraan::create([
            'kode' => $kode,
            'nama' => $namaAkun,
            'jenis' => 'aset',
            'saldo_normal' => 'debit',
            'is_header' => false,
            'parent_id' => $parentId,
            'keterangan' => 'Dibuat otomatis untuk rekening '.$nama,
            'is_aktif' => true,
        ]);
    }

    private function akunDiizinkan(int $akunId): bool
    {
        $akun = AkunPerkiraan::find($akunId);

        if (! $akun || $akun->is_header || $akun->jenis !== 'aset') {
            return false;
        }

        $kasKode = (string) (AkunPerkiraan::find((int) PengaturanSistemService::akunId('kas'))?->kode ?? '');
        $bankKode = (string) (AkunPerkiraan::find((int) PengaturanSistemService::akunId('bank'))?->kode ?? '');

        // Tanpa konfigurasi akun kas/bank, pertahankan perilaku lama (semua leaf aset).
        if ($kasKode === '' && $bankKode === '') {
            return true;
        }

        return ($kasKode !== '' && str_starts_with($akun->kode, $kasKode))
            || ($bankKode !== '' && str_starts_with($akun->kode, $bankKode));
    }

    private function rekeningSudahDipakai(Rekening $rekening): bool
    {
        return JurnalItem::where('akun_id', $rekening->akun_id)->exists()
            || KasMasuk::where('rekening_id', $rekening->id)->exists()
            || KasKeluar::where('rekening_id', $rekening->id)->exists()
            || Pembelian::where('rekening_id', $rekening->id)->exists()
            || Penjualan::where('rekening_id', $rekening->id)->exists()
            || MutasiBank::where('rekening_asal_id', $rekening->id)->orWhere('rekening_tujuan_id', $rekening->id)->exists()
            || Aset::where('rekening_id', $rekening->id)->exists();
    }
}
