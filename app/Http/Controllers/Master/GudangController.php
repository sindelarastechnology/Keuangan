<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\StokGudang;
use App\Services\KolomTabelService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    public const KOLOM_OPTIONS = [
        'kode' => 'Kode',
        'nama' => 'Nama',
        'alamat' => 'Alamat',
        'stok_count' => 'Jenis Barang',
        'status' => 'Status',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['kode', 'nama', 'stok_count', 'status', 'aksi'];

    public function index()
    {
        $gudangs = Gudang::withCount('stok')->with('stok.barang')->orderBy('id')->get();

        return view('master.gudang.index', [
            'gudangs' => $gudangs,
            'kolomOptions' => self::KOLOM_OPTIONS,
            'kolomAktif' => self::kolomAktif(),
        ]);
    }

    public function create()
    {
        return view('master.gudang.form');
    }

    public function store(Request $request)
    {
        $data = $this->validate($request);
        $data['kode'] = Gudang::generateKode();
        Gudang::create($data);

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil ditambahkan.');
    }

    public function edit(Gudang $gudang)
    {
        return view('master.gudang.form', compact('gudang'));
    }

    public function update(Request $request, Gudang $gudang)
    {
        $gudang->update($this->validate($request));

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil diperbarui.');
    }

    public function destroy(Gudang $gudang)
    {
        if ($gudang->id === (int) optional(Gudang::utama())->id) {
            return back()->with('error', 'Gudang utama tidak dapat dihapus.');
        }

        if (Barang::where('gudang_id', $gudang->id)->exists()) {
            return back()->with('error', 'Gudang masih menampung data produk dan tidak dapat dihapus.');
        }

        if (StokGudang::where('gudang_id', $gudang->id)->where('qty', '>', 0)->exists()) {
            return back()->with('error', 'Gudang masih memiliki stok dan tidak dapat dihapus.');
        }

        try {
            $gudang->delete();
        } catch (QueryException $e) {
            return back()->with('error', 'Gudang masih direferensikan data lain dan tidak dapat dihapus.');
        }

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil dihapus.');
    }

    public function simpanKolom(Request $request)
    {
        KolomTabelService::simpan($request, 'gudang_kolom', self::KOLOM_OPTIONS);

        return back()->with('success', 'Pengaturan kolom berhasil disimpan.');
    }

    public static function kolomAktif(): array
    {
        return KolomTabelService::aktif('gudang_kolom', self::KOLOM_OPTIONS, self::KOLOM_DEFAULT);
    }

    private function validate(Request $request): array
    {
        return $request->validate([
            'nama' => 'required|string|max:150',
            'alamat' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]) + ['is_aktif' => $request->boolean('is_aktif', true)];
    }
}
