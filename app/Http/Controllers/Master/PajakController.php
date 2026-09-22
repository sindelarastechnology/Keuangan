<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Pajak;
use App\Services\KolomTabelService;
use Illuminate\Http\Request;

class PajakController extends Controller
{
    public const KOLOM_OPTIONS = [
        'nama' => 'Nama',
        'rate' => 'Rate',
        'status' => 'Status',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['nama', 'rate', 'status', 'aksi'];

    public function index()
    {
        $pajak = Pajak::orderBy('nama')->get();

        return view('master.pajak.index', [
            'pajak' => $pajak,
            'kolomOptions' => self::KOLOM_OPTIONS,
            'kolomAktif' => self::kolomAktif(),
        ]);
    }

    public function simpanKolom(Request $request)
    {
        KolomTabelService::simpan($request, 'pajak_kolom', self::KOLOM_OPTIONS);

        return back()->with('success', 'Pengaturan kolom berhasil disimpan.');
    }

    public static function kolomAktif(): array
    {
        return KolomTabelService::aktif('pajak_kolom', self::KOLOM_OPTIONS, self::KOLOM_DEFAULT);
    }

    public function create()
    {
        return view('master.pajak.form');
    }

    public function store(Request $request)
    {
        Pajak::create($this->validate($request));

        return redirect()->route('pajak.index')->with('success', 'Pajak berhasil ditambahkan.');
    }

    public function edit(Pajak $pajak)
    {
        return view('master.pajak.form', compact('pajak'));
    }

    public function update(Request $request, Pajak $pajak)
    {
        $pajak->update($this->validate($request));

        return redirect()->route('pajak.index')->with('success', 'Pajak berhasil diperbarui.');
    }

    public function destroy(Pajak $pajak)
    {
        $pajak->delete();

        return redirect()->route('pajak.index')->with('success', 'Pajak berhasil dihapus.');
    }

    private function validate(Request $request): array
    {
        return $request->validate([
            'nama' => 'required|string|max:100',
            'rate' => 'required|numeric|min:0|max:100',
        ]) + ['is_aktif' => $request->boolean('is_aktif', true)];
    }
}
