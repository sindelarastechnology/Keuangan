<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Pengaturan;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PengaturanController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::pluck('value', 'key')->all();
        $satuanList = Satuan::orderBy('nama')->get();
        $kategoriList = Kategori::orderBy('nama')->get();

        return view('pengaturan.index', compact('pengaturan', 'satuanList', 'kategoriList'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'nama_perusahaan' => 'required|string|max:150',
            'alamat_perusahaan' => 'nullable|string|max:255',
            'telepon_perusahaan' => 'nullable|string|max:30',
            'email_perusahaan' => 'nullable|email|max:150',
            'kota_perusahaan' => 'nullable|string|max:100',
        ]);

        foreach ($data as $key => $value) {
            Pengaturan::atur($key, $value);
        }

        return redirect()->route('pengaturan.index', ['tab' => 'perusahaan'])->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function satuanStore(Request $request)
    {
        $request->validateWithBag('satuan', [
            'nama' => ['required', 'string', 'max:50', Rule::unique('satuan', 'nama')->where('user_id', auth()->id())],
        ]);
        Satuan::create(['nama' => $request->nama]);

        return redirect()->route('pengaturan.index', ['tab' => 'satuan'])->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function satuanUpdate(Request $request, Satuan $satuan)
    {
        $request->validateWithBag('satuan', [
            'nama' => ['required', 'string', 'max:50', Rule::unique('satuan', 'nama')->ignore($satuan->id)->where('user_id', auth()->id())],
        ]);
        $satuan->update(['nama' => $request->nama]);

        return redirect()->route('pengaturan.index', ['tab' => 'satuan'])->with('success', 'Satuan berhasil diperbarui.');
    }

    public function satuanDestroy(Satuan $satuan)
    {
        if (Barang::where('satuan', $satuan->nama)->exists()) {
            return redirect()->route('pengaturan.index', ['tab' => 'satuan'])
                ->with('error', 'Satuan "'.$satuan->nama.'" masih dipakai oleh barang. Ubah satuan barang tersebut terlebih dahulu.');
        }

        $satuan->delete();

        return redirect()->route('pengaturan.index', ['tab' => 'satuan'])->with('success', 'Satuan berhasil dihapus.');
    }

    public function kategoriStore(Request $request)
    {
        $request->validateWithBag('kategori', [
            'nama' => ['required', 'string', 'max:100', Rule::unique('kategori', 'nama')->where('user_id', auth()->id())],
        ]);
        Kategori::create(['nama' => $request->nama]);

        return redirect()->route('pengaturan.index', ['tab' => 'kategori'])->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function kategoriUpdate(Request $request, Kategori $kategori)
    {
        $request->validateWithBag('kategori', [
            'nama' => ['required', 'string', 'max:100', Rule::unique('kategori', 'nama')->ignore($kategori->id)->where('user_id', auth()->id())],
        ]);
        $kategori->update(['nama' => $request->nama]);

        return redirect()->route('pengaturan.index', ['tab' => 'kategori'])->with('success', 'Kategori berhasil diperbarui.');
    }

    public function kategoriDestroy(Kategori $kategori)
    {
        if (Barang::where('kategori', $kategori->nama)->exists()) {
            return redirect()->route('pengaturan.index', ['tab' => 'kategori'])
                ->with('error', 'Kategori "'.$kategori->nama.'" masih dipakai oleh barang. Ubah kategori barang tersebut terlebih dahulu.');
        }

        $kategori->delete();

        return redirect()->route('pengaturan.index', ['tab' => 'kategori'])->with('success', 'Kategori berhasil dihapus.');
    }
}
