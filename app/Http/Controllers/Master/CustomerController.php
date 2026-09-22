<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\BbPiutang;
use App\Models\Customer;
use App\Models\DaftarHarga;
use App\Models\KasMasuk;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::orderBy('nama')
            ->when($request->filled('cari'), fn ($q) => $q->where('nama', 'like', '%'.$request->cari.'%')
                ->orWhere('kode', 'like', '%'.$request->cari.'%'))
            ->paginate(15)->withQueryString();

        return view('master.customer.index', compact('customers'));
    }

    public function create()
    {
        return view('master.customer.form');
    }

    public function store(Request $request)
    {
        $data = $this->validate($request);
        $data['kode'] = $this->generateKode();
        Customer::create($data);

        return redirect()->route('customer.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        return view('master.customer.form', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($this->validate($request));

        return redirect()->route('customer.index')->with('success', 'Customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $referensi = $customer->penjualans()->exists()
            || BbPiutang::where('customer_id', $customer->id)->exists()
            || KasMasuk::where('customer_id', $customer->id)->exists()
            || DaftarHarga::where('customer_id', $customer->id)->exists();

        if ($referensi) {
            return back()->with('error', 'Customer memiliki riwayat transaksi / piutang dan tidak dapat dihapus.');
        }

        try {
            $customer->delete();
        } catch (QueryException $e) {
            return back()->with('error', 'Customer masih direferensikan data lain dan tidak dapat dihapus.');
        }

        return redirect()->route('customer.index')->with('success', 'Customer berhasil dihapus.');
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
        $max = (int) Customer::max('id') + 1;

        return 'CUS-'.str_pad((string) $max, 4, '0', STR_PAD_LEFT);
    }
}
