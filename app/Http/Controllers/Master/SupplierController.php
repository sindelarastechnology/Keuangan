<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Aset;
use App\Models\BbHutang;
use App\Models\DaftarHarga;
use App\Models\KasKeluar;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::orderBy('nama')
            ->when($request->filled('cari'), fn ($q) => $q->where('nama', 'like', '%'.$request->cari.'%')
                ->orWhere('kode', 'like', '%'.$request->cari.'%'))
            ->paginate(15)->withQueryString();

        return view('master.supplier.index', compact('suppliers'));
    }

    public function create()
    {
        return view('master.supplier.form');
    }

    public function store(Request $request)
    {
        $data = $this->validate($request);
        $data['kode'] = $this->generateKode();
        Supplier::create($data);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('master.supplier.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($this->validate($request));

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $referensi = $supplier->pembelians()->exists()
            || BbHutang::where('supplier_id', $supplier->id)->exists()
            || KasKeluar::where('supplier_id', $supplier->id)->exists()
            || Aset::where('supplier_id', $supplier->id)->exists()
            || DaftarHarga::where('supplier_id', $supplier->id)->exists();

        if ($referensi) {
            return back()->with('error', 'Supplier memiliki riwayat transaksi / utang dan tidak dapat dihapus.');
        }

        try {
            $supplier->delete();
        } catch (QueryException $e) {
            return back()->with('error', 'Supplier masih direferensikan data lain dan tidak dapat dihapus.');
        }

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }

    private function validate(Request $request): array
    {
        return $request->validate([
            'nama' => 'required|string|max:150',
            'alamat' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'npwp' => 'nullable|string|max:30',
            'keterangan' => 'nullable|string',
        ]) + ['is_aktif' => $request->boolean('is_aktif', true)];
    }

    private function generateKode(): string
    {
        $max = (int) Supplier::max('id') + 1;

        return 'SUP-'.str_pad((string) $max, 4, '0', STR_PAD_LEFT);
    }
}
