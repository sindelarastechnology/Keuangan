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

class BarangController extends Controller
{
    public const KOLOM_OPTIONS = [
        'foto' => 'Foto',
        'kode' => 'Kode',
        'nama' => 'Nama',
        'satuan' => 'Satuan',
        'kategori' => 'Kategori',
        'merek' => 'Merek',
        'ukuran' => 'Ukuran',
        'warna' => 'Warna',
        'barcode' => 'Barcode',
        'stok' => 'Stok',
        'min_stok' => 'Stok Min.',
        'harga_beli' => 'Harga Beli',
        'harga_jual' => 'Harga Jual',
        'gudang' => 'Gudang',
        'keterangan' => 'Keterangan',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = [
        'foto', 'kode', 'nama', 'kategori', 'stok', 'harga_jual', 'harga_beli', 'aksi',
    ];

    public function index(Request $request)
    {
        $barang = Barang::barang()
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

        return view('master.barang.index', [
            'barang' => $barang,
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

    public function edit(Barang $barang)
    {
        if ($barang->tipe === 'jasa') {
            abort(404);
        }

        return $this->form($barang);
    }

    public function store(Request $request)
    {
        $data = $this->validate($request);
        $data['kode'] = Barang::generateKode();
        $data['tipe'] = 'barang';
        // HPP awal barang baru = harga beli; selanjutnya hanya dipengaruhi transaksi/stok.
        $data['harga_avg'] = (float) $data['harga_beli'];

        if ($request->hasFile('foto')) {
            $data['foto'] = $this->simpanFoto($request);
        }

        if (! $request->filled('gudang_id')) {
            $data['gudang_id'] = Gudang::utama()?->id;
        }

        $barang = Barang::create($data);
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

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update(Request $request, Barang $barang)
    {
        if ($barang->tipe === 'jasa') {
            abort(404);
        }

        $data = $this->validate($request);
        // HPP tidak boleh diubah manual; mengikuti harga beli & mutasi stok.
        unset($data['harga_avg']);

        if ($request->boolean('hapus_foto')) {
            $data['foto'] = null;
        } elseif ($request->hasFile('foto')) {
            $data['foto'] = $this->simpanFoto($request);
        }

        $barang->update($data);
        $this->sinkronHargaUmum($barang, (float) $data['harga_beli'], (float) $data['harga_jual']);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        DB::beginTransaction();
        try {
            // Produk yang sudah tercatat di transaksi/jurnal/stok tidak bisa
            // dihapus permanen (akan merusak riwayat). Arsipkan saja.
            if ($barang->terpakai()) {
                $barang->arsipkan();

                DB::commit();

                return redirect()->route('barang.index')
                    ->with('success', "Barang '{$barang->nama}' diarsipkan (dinonaktifkan) karena masih terkait riwayat transaksi/stok. "
                        .'Data transaksi, jurnal, dan stok tetap aman.');
            }

            $barang->bersihkanReferensiPendukung();
            $barang->delete();

            DB::commit();

            return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menghapus: '.$e->getMessage());
        }
    }

    public function tambahSatuan(Request $request)
    {
        $nama = $request->validate(['nama' => 'required|string|max:50'])['nama'];
        $satuan = Satuan::firstOrCreate(['nama' => $nama]);

        return response()->json(['id' => $satuan->id, 'nama' => $satuan->nama]);
    }

    public function tambahKategori(Request $request)
    {
        $nama = $request->validate(['nama' => 'required|string|max:100'])['nama'];
        $kategori = Kategori::firstOrCreate(['nama' => $nama]);

        return response()->json(['id' => $kategori->id, 'nama' => $kategori->nama]);
    }

    public function simpanKolom(Request $request)
    {
        $kolom = $request->validate([
            'kolom' => 'sometimes|array',
            'kolom.*' => 'string',
        ])['kolom'] ?? [];

        $kolom = array_values(array_intersect($kolom, array_keys(self::KOLOM_OPTIONS)));
        if (! in_array('aksi', $kolom, true)) {
            $kolom[] = 'aksi';
        }

        Pengaturan::atur('barang_kolom', json_encode($kolom));

        return back()->with('success', 'Pengaturan kolom berhasil disimpan.');
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

        return view('master.barang.form', compact('barang', 'warnaList', 'ukuranOptions', 'satuanList', 'kategoriList', 'gudangList', 'gudangUtamaId'));
    }

    private function simpanFoto(Request $request): string
    {
        return $request->file('foto')->store('barang', 'public');
    }

    /**
     * Sinkronkan harga standar barang ke daftar harga rekanan "UMUM":
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

    private function validate(Request $request): array
    {
        return $request->validate([
            'nama' => 'required|string|max:150',
            'satuan' => 'nullable|string|max:20',
            'kategori' => 'nullable|string|max:100',
            'merek' => 'nullable|string|max:100',
            'ukuran' => 'nullable|string|max:50',
            'warna' => 'nullable|string|max:50',
            'barcode' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'stok' => 'required|numeric|min:0',
            'harga_avg' => 'nullable|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'min_stok' => 'required|numeric|min:0',
            'gudang_id' => 'nullable|exists:gudang,id',
            'keterangan' => 'nullable|string',
            'hapus_foto' => 'sometimes|boolean',
        ]) + ['is_aktif' => $request->boolean('is_aktif', true)];
    }
}
