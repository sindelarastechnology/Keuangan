<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Models\AkunPerkiraan;
use App\Models\Aset;
use App\Models\AssetTemplate;
use App\Models\JurnalUmum;
use App\Models\Rekening;
use App\Models\Supplier;
use App\Services\AsetDisposisiService;
use App\Services\JournalService;
use App\Services\PengaturanSistemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AsetController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'aset_kolom';

    public const KOLOM_OPTIONS = [
        'kode' => 'Kode',
        'nama' => 'Nama Aset',
        'lokasi' => 'Lokasi',
        'tanggal' => 'Tgl Beli',
        'harga' => 'Harga Beli',
        'akumulasi' => 'Total Penyusutan',
        'nilai' => 'Sisa Nilai',
        'beban' => 'Biaya/Bulan',
        'status' => 'Status',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['kode', 'nama', 'lokasi', 'tanggal', 'harga', 'akumulasi', 'nilai', 'beban', 'status'];

    public const KOLOM_WAJIB = ['aksi'];

    public function index(Request $request)
    {
        $asets = Aset::withSum('penyusutan', 'beban')
            ->with(['akunAset', 'akunAkumulasi', 'akunBeban'])
            ->when($request->filled('cari'), function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('nama', 'like', '%'.$request->cari.'%')
                        ->orWhere('kode', 'like', '%'.$request->cari.'%');
                });
            })
            ->when($request->filled('kategori'), fn ($q) => $q->where('kategori', $request->kategori))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('id')
            ->paginate(15)->withQueryString();

        return view('master.aset.index', [
            'asets' => $asets,
            'kategoriList' => AssetTemplate::query()->orderBy('nama_kategori')->pluck('nama_kategori')
                ->concat(Aset::query()->whereNotNull('kategori')->whereNotIn('kategori', AssetTemplate::query()->pluck('nama_kategori'))->distinct()->pluck('kategori'))
                ->unique()
                ->values(),
            'kolomOptions' => self::KOLOM_OPTIONS,
            'kolomAktif' => $this->kolomAktif(),
        ]);
    }

    public function create()
    {
        return $this->form();
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['kode'] = Aset::generateKode();

        try {
            $data = $this->terapkanSetelanKeuangan($data, $request);

            $aset = DB::transaction(function () use ($data) {
                $aset = Aset::create($data);

                if ($aset->catat_perolehan && $aset->sumber_dana_id) {
                    $this->postingPerolehan($aset);
                }

                return $aset;
            });
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        $saldoKurang = null;
        if ($aset->catat_perolehan && $aset->rekening_id) {
            $rekeningDana = Rekening::find((int) $aset->rekening_id);
            if ($rekeningDana && (float) $rekeningDana->saldo < (float) $aset->harga_perolehan) {
                $saldoKurang = $rekeningDana;
            }
        }

        $redirect = redirect()->route('aset.show', $aset)->with('success', 'Aset berhasil ditambahkan.');

        if ($saldoKurang) {
            return $redirect->with('warning', 'Saldo rekening tidak mencukupi ('.$saldoKurang->nama.': '.formatRupiah($saldoKurang->saldo).'). Perolehan aset tetap dicatat, namun saldo rekening menjadi minus.');
        }

        return $redirect;
    }

    public function show(Aset $aset)
    {
        $aset->load(['penyusutan', 'akunAset', 'akunAkumulasi', 'akunBeban', 'sumberDana', 'rekening', 'supplier']);
        $jurnalPerolehan = JurnalUmum::where('ref_type', Aset::class)
            ->where('ref_id', $aset->id)
            ->where('tipe', 'perolehan_aset')
            ->first();
        $jurnalPenghapusan = JurnalUmum::where('ref_type', Aset::class)
            ->where('ref_id', $aset->id)
            ->where('tipe', 'penghapusan_aset')
            ->first();

        return view('master.aset.show', compact('aset', 'jurnalPerolehan', 'jurnalPenghapusan'));
    }

    public function edit(Aset $aset)
    {
        return $this->form($aset);
    }

    public function update(Request $request, Aset $aset)
    {
        $data = $this->validateData($request);
        unset($data['catat_perolehan'], $data['sumber_dana_id'], $data['rekening_id'], $data['supplier_id']);
        // Status dikelola lewat aksi "selesai"/"disposisi"; jangan ubah lewat edit
        // agar aset nonaktif (sudah dijual/rusak) tidak kembali aktif.
        $data['status'] = $aset->status;
        $data = $this->terapkanAkunKeuangan($data, $request);

        $aset->update($data);

        return redirect()->route('aset.show', $aset)->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy(Aset $aset)
    {
        if ($aset->penyusutan()->exists()) {
            return back()->with('error', 'Aset sudah memiliki riwayat penyusutan dan tidak dapat dihapus.');
        }

        $punyaJurnal = JurnalUmum::where('ref_type', Aset::class)->where('ref_id', $aset->id)->exists();
        if ($punyaJurnal) {
            return back()->with('error', 'Aset sudah dicatat dalam jurnal pembelian dan tidak dapat dihapus.');
        }

        $aset->delete();

        return redirect()->route('aset.index')->with('success', 'Aset berhasil dihapus.');
    }

    public function selesai(Aset $aset)
    {
        if (in_array($aset->status, ['selesai', 'nonaktif'], true)) {
            return back()->with('info', 'Aset '.$aset->kode.' sudah berstatus selesai / tidak aktif.');
        }

        $aset->update(['status' => 'selesai']);

        return back()->with('success', 'Aset '.$aset->kode.' ditandai selesai. Penyusutan dihentikan.');
    }

    public function disposisi(Request $request, Aset $aset)
    {
        $data = $request->validate([
            'alasan' => 'required|in:'.implode(',', array_keys(AsetDisposisiService::ALASAN)),
            'harga_jual' => 'required_if:alasan,dijual|nullable|numeric|min:0.01',
        ]);

        try {
            $jurnal = AsetDisposisiService::disposisi($aset, $data['alasan'], $data['harga_jual'] ?? null);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return back()->with('success', 'Aset '.$aset->kode.' ditandai '.AsetDisposisiService::ALASAN[$data['alasan']].' (jurnal '.$jurnal->nomor.').');
    }

    private function form(?Aset $aset = null)
    {
        $akunAsetList = AkunPerkiraan::leaf()->where('jenis', 'aset')->orderBy('kode')->get(['id', 'kode', 'nama', 'saldo_normal']);
        $akunBebanList = AkunPerkiraan::leaf()->where('jenis', 'beban')->orderBy('kode')->get(['id', 'kode', 'nama']);
        $templates = AssetTemplate::orderBy('nama_kategori')->get();
        $rekenings = Rekening::aktif()->orderBy('jenis')->orderBy('nama')->get();
        $suppliers = Supplier::aktif()->orderBy('nama')->get();

        $masaOptions = $this->masaOptions($templates);
        $defaults = $this->akunDefaults();
        $templateOptions = $templates->map(fn ($t) => [
            'nama' => $t->nama_kategori,
            'masa' => (int) $t->masa_manfaat_bulan,
            'persen' => (float) $t->persen_residu,
            'akunAset' => $t->akun_aset_id,
            'akunAkumulasi' => $t->akun_akumulasi_id,
            'akunBeban' => $t->akun_beban_id,
        ])->values();

        return view('master.aset.form', [
            'aset' => $aset,
            'akunAsetList' => $akunAsetList,
            'akunBebanList' => $akunBebanList,
            'templates' => $templates,
            'templateOptions' => $templateOptions,
            'rekenings' => $rekenings,
            'suppliers' => $suppliers,
            'masaOptions' => $masaOptions,
            'akunUtangId' => PengaturanSistemService::akunId('utang'),
            'isKeuangan' => true,
            'defaultAkunAset' => $defaults['aset'],
            'defaultAkunAkumulasi' => $defaults['akumulasi'],
            'defaultAkunBeban' => $defaults['beban'],
        ]);
    }

    /**
     * Opsi dropdown "Lama Dipakai" (dalam tahun). Gabungan 1-5 tahun standar
     * dengan nilai masa manfaat dari asset_templates yang bulat per tahun.
     */
    private function masaOptions($templates): array
    {
        $bulanSet = collect([12, 24, 36, 48, 60]);

        foreach ($templates as $template) {
            $bulan = (int) $template->masa_manfaat_bulan;
            if ($bulan > 0 && $bulan % 12 === 0) {
                $bulanSet->push($bulan);
            }
        }

        return $bulanSet
            ->unique()
            ->sort()
            ->values()
            ->map(fn ($bulan) => ['tahun' => (int) ($bulan / 12), 'bulan' => $bulan])
            ->all();
    }

    private function akunDefaults(): array
    {
        return [
            'aset' => PengaturanSistemService::akunId('aset_tetap'),
            'akumulasi' => PengaturanSistemService::akunId('akumulasi_penyusutan'),
            'beban' => PengaturanSistemService::akunId('beban_penyusutan'),
        ];
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'kategori' => 'nullable|string|max:100',
            'lokasi' => 'nullable|string|max:100',
            'tanggal_perolehan' => 'required|date',
            'harga_perolehan' => 'required|numeric|min:1',
            'nilai_residu' => 'nullable|numeric|min:0|lte:harga_perolehan',
            'masa_manfaat_bulan' => 'required|integer|min:1|max:600',
            'akun_aset_id' => 'sometimes|nullable|exists:akun_perkiraan,id',
            'akun_akumulasi_id' => 'sometimes|nullable|exists:akun_perkiraan,id',
            'akun_beban_id' => 'sometimes|nullable|exists:akun_perkiraan,id',
            'sumber_dana' => 'nullable|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'catat_perolehan' => 'sometimes|boolean',
            'status' => 'nullable|in:aktif,selesai',
            'keterangan' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'foto' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data['catat_perolehan'] = $request->boolean('catat_perolehan');
        $data['status'] = $data['status'] ?? 'aktif';

        return $data;
    }

    /**
     * Terapkan setelan keuangan dan pembayaran saat membuat aset baru.
     */
    private function terapkanSetelanKeuangan(array $data, Request $request): array
    {
        $data = $this->terapkanAkunKeuangan($data, $request);

        $sumber = (string) $request->input('sumber_dana');

        if ($data['catat_perolehan']) {
            if (str_starts_with($sumber, 'rekening:')) {
                $rekening = Rekening::find(substr($sumber, strlen('rekening:')));
                if (! $rekening) {
                    throw new RuntimeException('Rekening pembayaran tidak ditemukan.');
                }
                $data['sumber_dana_id'] = $rekening->akun_id;
                $data['rekening_id'] = $rekening->id;
                $data['supplier_id'] = null;
            } else {
                $utang = PengaturanSistemService::akunId('utang');
                if (! $utang) {
                    throw new RuntimeException('Akun utang usaha (211) tidak ditemukan.');
                }
                if (empty($data['supplier_id'])) {
                    throw new RuntimeException('Beli utang wajib memilih nama supplier.');
                }
                $data['sumber_dana_id'] = $utang;
                $data['rekening_id'] = null;
            }
        } else {
            $data['sumber_dana_id'] = null;
            $data['rekening_id'] = null;
            $data['supplier_id'] = null;
        }

        return $data;
    }

    /**
     * Terapkan akun aset/akumulasi/beban (default dari kategori/template bila
     * tidak diisi oleh pemakai) plus simpan foto baru bila diunggah.
     */
    private function terapkanAkunKeuangan(array $data, Request $request): array
    {
        $template = null;
        if (! empty($data['kategori'])) {
            $template = AssetTemplate::where('nama_kategori', $data['kategori'])->first();
        }
        $defaults = $this->akunDefaults();

        $data['akun_aset_id'] = ($data['akun_aset_id'] ?? null) ?: ($template->akun_aset_id ?? $defaults['aset']);
        $data['akun_akumulasi_id'] = ($data['akun_akumulasi_id'] ?? null) ?: ($template->akun_akumulasi_id ?? $defaults['akumulasi']);
        $data['akun_beban_id'] = ($data['akun_beban_id'] ?? null) ?: ($template->akun_beban_id ?? $defaults['beban']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('asets', 'public');
        }

        return $data;
    }

    private function postingPerolehan(Aset $aset): void
    {
        JournalService::post(
            'perolehan_aset',
            $aset->tanggal_perolehan,
            [
                ['akun_id' => $aset->akun_aset_id, 'debit' => $aset->harga_perolehan, 'kredit' => 0, 'keterangan' => 'Pembelian '.$aset->kode.' '.$aset->nama],
                ['akun_id' => $aset->sumber_dana_id, 'debit' => 0, 'kredit' => $aset->harga_perolehan, 'keterangan' => 'Pembelian '.$aset->kode.' '.$aset->nama],
            ],
            'Pembelian Aset '.$aset->kode.' '.$aset->nama,
            $aset
        );
    }
}
