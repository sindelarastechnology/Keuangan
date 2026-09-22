<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Aset;
use App\Models\BbHutang;
use App\Models\BbPiutang;
use App\Models\Customer;
use App\Models\DaftarHarga;
use App\Models\KasKeluar;
use App\Models\KasMasuk;
use App\Models\Supplier;
use App\Services\KolomTabelService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class DataNamaController extends Controller
{
    public const ENTITAS = ['customer', 'supplier'];

    public const KOLOM_OPTIONS = [
        'kode' => 'Kode',
        'nama' => 'Nama',
        'telepon' => 'Telepon',
        'email' => 'Email',
        'alamat' => 'Alamat',
        'saldo' => 'Saldo',
        'keterangan' => 'Keterangan',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['kode', 'nama', 'telepon', 'email', 'saldo', 'aksi'];

    public function index(Request $request)
    {
        $entitas = $request->entitas === 'supplier' ? 'supplier' : 'customer';
        $dataNama = $entitas === 'supplier'
            ? Supplier::orderBy('nama')->when($request->filled('cari'), fn ($q) => $q->where('nama', 'like', '%'.$request->cari.'%')
                ->orWhere('kode', 'like', '%'.$request->cari.'%'))->paginate(15)->withQueryString()
            : Customer::orderBy('nama')->when($request->filled('cari'), fn ($q) => $q->where('nama', 'like', '%'.$request->cari.'%')
                ->orWhere('kode', 'like', '%'.$request->cari.'%'))->paginate(15)->withQueryString();

        return view('master.data-nama.index', [
            'dataNama' => $dataNama,
            'entitas' => $entitas,
            'kolomOptions' => self::KOLOM_OPTIONS,
            'kolomAktif' => self::kolomAktif(),
        ]);
    }

    public function simpanKolom(Request $request)
    {
        KolomTabelService::simpan($request, 'data_nama_kolom', self::KOLOM_OPTIONS);

        return back()->with('success', 'Pengaturan kolom berhasil disimpan.');
    }

    public static function kolomAktif(): array
    {
        return KolomTabelService::aktif('data_nama_kolom', self::KOLOM_OPTIONS, self::KOLOM_DEFAULT);
    }

    public function create(Request $request)
    {
        $entitas = $request->entitas === 'supplier' ? 'supplier' : 'customer';

        return view('master.data-nama.form', ['entitas' => $entitas, 'dataNama' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validate($request);
        $entitas = $data['entitas'];
        unset($data['entitas']);

        if ($entitas === 'supplier') {
            $data['kode'] = $this->generateKode(Supplier::class, 'SUP');
            Supplier::create($data);
        } else {
            $data['kode'] = $this->generateKode(Customer::class, 'CUS');
            Customer::create($data);
        }

        return redirect()->route('data-nama.index', ['entitas' => $entitas])
            ->with('success', 'Data nama berhasil ditambahkan.');
    }

    public function edit(Request $request, string $entitas, $dataNama)
    {
        $entitas = in_array($entitas, self::ENTITAS, true) ? $entitas : 'customer';
        $dataNama = $entitas === 'supplier' ? Supplier::findOrFail($dataNama) : Customer::findOrFail($dataNama);

        return view('master.data-nama.form', compact('entitas', 'dataNama'));
    }

    public function update(Request $request, string $entitas, $dataNama)
    {
        $entitas = in_array($entitas, self::ENTITAS, true) ? $entitas : 'customer';
        $dataNama = $entitas === 'supplier' ? Supplier::findOrFail($dataNama) : Customer::findOrFail($dataNama);

        $data = $this->validate($request);
        unset($data['entitas']);
        $dataNama->update($data);

        return redirect()->route('data-nama.index', ['entitas' => $entitas])
            ->with('success', 'Data nama berhasil diperbarui.');
    }

    public function destroy(Request $request, string $entitas, $dataNama)
    {
        $entitas = in_array($entitas, self::ENTITAS, true) ? $entitas : 'customer';

        if ($entitas === 'supplier') {
            $dataNama = Supplier::findOrFail($dataNama);
            $referensi = $dataNama->pembelians()->exists()
                || BbHutang::where('supplier_id', $dataNama->id)->exists()
                || KasKeluar::where('supplier_id', $dataNama->id)->exists()
                || Aset::where('supplier_id', $dataNama->id)->exists()
                || DaftarHarga::where('supplier_id', $dataNama->id)->exists();
        } else {
            $dataNama = Customer::findOrFail($dataNama);
            $referensi = $dataNama->penjualans()->exists()
                || BbPiutang::where('customer_id', $dataNama->id)->exists()
                || KasMasuk::where('customer_id', $dataNama->id)->exists()
                || DaftarHarga::where('customer_id', $dataNama->id)->exists();
        }

        if ($referensi) {
            return back()->with('error', 'Data nama memiliki riwayat transaksi dan tidak dapat dihapus.');
        }

        try {
            $dataNama->delete();
        } catch (QueryException $e) {
            return back()->with('error', 'Data nama masih direferensikan data lain dan tidak dapat dihapus.');
        }

        return redirect()->route('data-nama.index', ['entitas' => $entitas])
            ->with('success', 'Data nama berhasil dihapus.');
    }

    private function validate(Request $request): array
    {
        return $request->validate([
            'entitas' => 'required|in:customer,supplier',
            'nama' => 'required|string|max:150',
            'alamat' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'npwp' => 'nullable|string|max:30',
            'keterangan' => 'nullable|string',
        ]) + ['is_aktif' => $request->boolean('is_aktif', true)];
    }

    private function generateKode(string $model, string $prefix): string
    {
        $max = (int) $model::max('id') + 1;

        return $prefix.'-'.str_pad((string) $max, 4, '0', STR_PAD_LEFT);
    }
}
