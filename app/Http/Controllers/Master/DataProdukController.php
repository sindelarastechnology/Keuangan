<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BbPersediaan;
use App\Models\Customer;
use App\Models\DaftarHarga;
use App\Models\Gudang;
use App\Models\Kategori;
use App\Models\Pengaturan;
use App\Models\Satuan;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataProdukController extends Controller
{
    public const KOLOM_OPTIONS = BarangController::KOLOM_OPTIONS;

    public const KOLOM_DEFAULT = BarangController::KOLOM_DEFAULT;

    public function index(Request $request)
    {
        $tipe = in_array($request->tipe, ['barang', 'jasa'], true) ? $request->tipe : null;

        $produk = Barang::when($tipe, fn ($q) => $q->where('tipe', $tipe))
            ->with(['stokGudang.gudang'])
            ->orderBy('nama')
            ->when($request->filled('cari'), function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('nama', 'like', '%'.$request->cari.'%')
                        ->orWhere('kode', 'like', '%'.$request->cari.'%')
                        ->orWhere('barcode', 'like', '%'.$request->cari.'%');
                });
            })
            ->when($request->filled('kategori'), fn ($q) => $q->where('kategori', $request->kategori))
            ->when($request->filled('satuan'), fn ($q) => $q->where('satuan', $request->satuan))
            ->when($request->filled('stok'), function ($q) use ($request) {
                if ($request->stok === 'rendah') {
                    $q->whereColumn('stok', '<=', 'min_stok');
                } elseif ($request->stok === 'habis') {
                    $q->where('stok', '<=', 0);
                }
            })
            ->paginate(15)->withQueryString();

        return view('master.data-produk.index', [
            'produk' => $produk,
            'tipe' => $tipe,
            'satuanList' => Satuan::orderBy('nama')->pluck('nama'),
            'kategoriList' => Kategori::orderBy('nama')->pluck('nama'),
            'kolomOptions' => self::KOLOM_OPTIONS,
            'kolomAktif' => $this->kolomAktif(),
        ]);
    }

    public function create()
    {
        return $this->form();
    }

    public function edit(Barang $dataProduk)
    {
        return $this->form($dataProduk);
    }

    public function store(Request $request)
    {
        $data = $this->validate($request);

        $tipe = $data['tipe'];
        $data['kode'] = Barang::generateKode($tipe === 'jasa' ? 'JAS' : 'BRG');
        $data['tipe'] = $tipe;

        if ($tipe === 'jasa') {
            $data['stok'] = 0;
            $data['harga_beli'] = 0;
            $data['harga_avg'] = 0;
            $data['min_stok'] = 0;
        } else {
            // HPP awal barang baru = harga beli; selanjutnya hanya dipengaruhi transaksi/stok.
            $data['harga_avg'] = (float) $data['harga_beli'];
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $this->simpanFoto($request);
        }

        if (! $request->filled('gudang_id')) {
            $data['gudang_id'] = Gudang::utama()?->id;
        }

        $barang = Barang::create($data);

        if ($tipe === 'barang') {
            $this->sinkronHargaUmum($barang, (float) $data['harga_beli'], (float) $data['harga_jual']);

            // Catat saldo awal ke BB Persediaan jika memiliki stok awal
            if ((float) $barang->stok > 0) {
                BbPersediaan::create([
                    'barang_id' => $barang->id,
                    'ref_type' => null,
                    'ref_id' => null,
                    'tanggal' => now()->toDateString(),
                    'keterangan' => 'Saldo Awal '.$barang->nama,
                    'masuk_qty' => (float) $barang->stok,
                    'masuk_harga' => (float) $barang->harga_avg,
                    'keluar_qty' => 0,
                    'keluar_harga' => 0,
                    'saldo_qty' => (float) $barang->stok,
                    'saldo_harga' => round((float) $barang->stok * (float) $barang->harga_avg, 2),
                    'ratt' => (float) $barang->harga_avg,
                ]);
            }
        }

        return redirect()->route('data-produk.index', ['tipe' => $tipe])
            ->with('success', ucfirst($tipe).' berhasil ditambahkan.');
    }

    public function update(Request $request, Barang $dataProduk)
    {
        // Tipe terkunci setelah dibuat: abaikan nilai tipe dari form.
        $data = $this->validate($request, $dataProduk);
        unset($data['tipe'], $data['harga_avg']);

        if ($request->boolean('hapus_foto')) {
            $data['foto'] = null;
        } elseif ($request->hasFile('foto')) {
            $data['foto'] = $this->simpanFoto($request);
        }

        $dataProduk->update($data);

        if ($dataProduk->tipe === 'barang') {
            $this->sinkronHargaUmum($dataProduk, (float) $data['harga_beli'], (float) $data['harga_jual']);
        }

        return redirect()->route('data-produk.index', ['tipe' => $dataProduk->tipe])
            ->with('success', ucfirst($dataProduk->tipe).' berhasil diperbarui.');
    }

    public function destroy(Barang $dataProduk)
    {
        $tipe = $dataProduk->tipe;

        DB::beginTransaction();
        try {
            if ($dataProduk->terpakai()) {
                $dataProduk->arsipkan();

                DB::commit();

                return redirect()->route('data-produk.index', ['tipe' => $tipe])
                    ->with('success', ucfirst($tipe)." '{$dataProduk->nama}' diarsipkan (dinonaktifkan) karena masih terkait "
                        .'riwayat transaksi/stok. Data transaksi, jurnal, dan stok tetap aman.');
            }

            $dataProduk->bersihkanReferensiPendukung();
            $dataProduk->delete();

            DB::commit();

            return redirect()->route('data-produk.index', ['tipe' => $tipe])
                ->with('success', ucfirst($tipe).' berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menghapus: '.$e->getMessage());
        }
    }

    public function tambahSatuan(Request $request)
    {
        return app(BarangController::class)->tambahSatuan($request);
    }

    public function tambahKategori(Request $request)
    {
        return app(BarangController::class)->tambahKategori($request);
    }

    public function simpanKolom(Request $request)
    {
        return app(BarangController::class)->simpanKolom($request);
    }

    public function kolomAktif(): array
    {
        $tersimpan = Pengaturan::tampil('barang_kolom');
        if (! $tersimpan) {
            return self::KOLOM_DEFAULT;
        }
        $decoded = json_decode($tersimpan, true);

        return is_array($decoded) ? array_values(array_intersect($decoded, array_keys(self::KOLOM_OPTIONS))) : self::KOLOM_DEFAULT;
    }

    private function form(?Barang $barang = null)
    {
        $warnaList = Barang::getWarnaList();
        $ukuranOptions = Barang::getUkuranOptions();
        $satuanList = Satuan::orderBy('nama')->get(['id', 'nama']);
        $kategoriList = Kategori::orderBy('nama')->get(['id', 'nama']);
        $gudangList = Gudang::aktif()->orderBy('nama')->get();
        $gudangUtamaId = Gudang::utama()?->id;
        $produkTipe = $barang
            ? $barang->tipe
            : (in_array(request('tipe'), ['barang', 'jasa'], true) ? request('tipe') : old('tipe', 'barang'));

        return view('master.data-produk.form', [
            'produkTipe' => $produkTipe,
            'barang' => $barang,
            'warnaList' => $warnaList,
            'ukuranOptions' => $ukuranOptions,
            'satuanList' => $satuanList,
            'kategoriList' => $kategoriList,
            'gudangList' => $gudangList,
            'gudangUtamaId' => $gudangUtamaId,
        ]);
    }

    private function simpanFoto(Request $request): string
    {
        return $request->file('foto')->store('barang', 'public');
    }

    /**
     * Sinkronkan harga standar produk ke daftar harga rekanan "UMUM":
     * harga_beli -> supplier UMUM, harga_jual -> customer UMUM.
     */
    private function sinkronHargaUmum(Barang $barang, float $hargaBeli, float $hargaJual): void
    {
        // Produk nonaktif/arsip tidak boleh memicu daftar harga aktif baru.
        if (! $barang->is_aktif) {
            return;
        }

        try {
            $supplierUmum = Supplier::umum();
            if ($supplierUmum) {
                DaftarHarga::sinkronBeli($supplierUmum->id, $barang->id, 1, $hargaBeli);
            }

            $customerUmum = Customer::umum();
            if ($customerUmum) {
                DaftarHarga::sinkronJual($customerUmum->id, $barang->id, 1, $hargaJual);
            }
        } catch (\Throwable $e) {
            Log::warning('Sinkron harga UMUM gagal', ['barang_id' => $barang->id, 'error' => $e->getMessage()]);
        }
    }

    private function validate(Request $request, ?Barang $barang = null): array
    {
        // Pada update, tipe mengikuti record yang ada (terkunci) dan tidak dikirim dari form.
        $tipe = $barang ? $barang->tipe : $request->input('tipe');

        $rules = [
            'tipe' => $barang ? 'nullable|in:barang,jasa' : 'required|in:barang,jasa',
            'nama' => 'required|string|max:150',
            'satuan' => 'nullable|string|max:20',
            'kategori' => 'nullable|string|max:100',
            'merek' => 'nullable|string|max:100',
            'ukuran' => 'nullable|string|max:50',
            'warna' => 'nullable|string|max:50',
            'barcode' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'harga_jual' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'hapus_foto' => 'sometimes|boolean',
            'gudang_id' => 'nullable|exists:gudangs,id',
        ];

        if ($tipe === 'jasa') {
            $rules['harga_jual'] = 'required|numeric|min:0';
        } else {
            $rules['stok'] = 'required|numeric|min:0';
            $rules['harga_beli'] = 'required|numeric|min:0';
            $rules['harga_jual'] = 'required|numeric|min:0';
            $rules['min_stok'] = 'required|numeric|min:0';
        }

        return $request->validate($rules) + ['is_aktif' => $request->boolean('is_aktif', true)];
    }
}
