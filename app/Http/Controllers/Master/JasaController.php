<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JasaController extends Controller
{
    public function index(Request $request)
    {
        $jasa = Barang::jasa()
            ->orderBy('nama')
            ->when($request->filled('cari'), function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('nama', 'like', '%'.$request->cari.'%')
                        ->orWhere('kode', 'like', '%'.$request->cari.'%');
                });
            })
            ->paginate(15)->withQueryString();

        return view('master.jasa.index', compact('jasa'));
    }

    public function create()
    {
        return $this->form();
    }

    public function edit(Barang $jasa)
    {
        if ($jasa->tipe !== 'jasa') {
            abort(404);
        }

        return $this->form($jasa);
    }

    public function store(Request $request)
    {
        $data = $this->validate($request);
        $data['kode'] = Barang::generateKode('JAS');
        $data['tipe'] = 'jasa';
        $data['stok'] = 0;
        $data['harga_avg'] = 0;
        $data['harga_beli'] = 0;
        $data['min_stok'] = 0;

        if ($request->hasFile('foto')) {
            $data['foto'] = $this->simpanFoto($request);
        }

        Barang::create($data);

        return redirect()->route('jasa.index')->with('success', 'Jasa berhasil ditambahkan.');
    }

    public function update(Request $request, Barang $jasa)
    {
        if ($jasa->tipe !== 'jasa') {
            abort(404);
        }

        $data = $this->validate($request);

        if ($request->boolean('hapus_foto')) {
            $data['foto'] = null;
        } elseif ($request->hasFile('foto')) {
            $data['foto'] = $this->simpanFoto($request);
        }

        $jasa->update($data);

        return redirect()->route('jasa.index')->with('success', 'Jasa berhasil diperbarui.');
    }

    public function destroy(Barang $jasa)
    {
        DB::beginTransaction();
        try {
            if ($jasa->terpakai()) {
                $jasa->arsipkan();

                DB::commit();

                return redirect()->route('jasa.index')
                    ->with('success', "Jasa '{$jasa->nama}' diarsipkan (dinonaktifkan) karena masih terkait riwayat transaksi. "
                        .'Data transaksi dan jurnal tetap aman.');
            }

            $jasa->bersihkanReferensiPendukung();
            $jasa->delete();

            DB::commit();

            return redirect()->route('jasa.index')->with('success', 'Jasa berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menghapus: '.$e->getMessage());
        }
    }

    private function form(?Barang $barang = null)
    {
        $satuanList = Satuan::orderBy('nama')->get(['id', 'nama']);
        $kategoriList = Kategori::orderBy('nama')->get(['id', 'nama']);

        return view('master.jasa.form', compact('barang', 'satuanList', 'kategoriList'));
    }

    private function simpanFoto(Request $request): string
    {
        return $request->file('foto')->store('jasa', 'public');
    }

    private function validate(Request $request): array
    {
        return $request->validate([
            'nama' => 'required|string|max:150',
            'satuan' => 'nullable|string|max:20',
            'kategori' => 'nullable|string|max:100',
            'harga_jual' => 'required|numeric|min:0',
            'barcode' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan' => 'nullable|string',
            'hapus_foto' => 'sometimes|boolean',
        ]) + ['is_aktif' => $request->boolean('is_aktif', true)];
    }
}
