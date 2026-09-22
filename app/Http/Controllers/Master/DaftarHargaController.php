<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\DaftarHarga;
use App\Models\DaftarHargaRiwayat;
use App\Models\Supplier;
use App\Services\KolomTabelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DaftarHargaController extends Controller
{
    public const KOLOM_OPTIONS = [
        'barang' => 'Barang',
        'tier' => 'Tier Qty',
        'harga' => 'Harga',
        'keterangan' => 'Keterangan',
        'status' => 'Status',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['barang', 'tier', 'harga', 'status', 'aksi'];

    public function index(Request $request)
    {
        $query = DaftarHarga::with(['supplier', 'customer', 'barang']);

        if ($request->filled('entitas')) {
            $query->where('entitas', $request->entitas);
        }

        if ($request->filled('supplier_id')) {
            $query->where('entitas', 'supplier')->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('customer_id')) {
            $query->where('entitas', 'customer')->where('customer_id', $request->customer_id);
        }

        if (! $request->boolean('show_history')) {
            $query->aktif();
        }

        $rows = $query->orderBy('entitas')
            ->orderByRaw('COALESCE(supplier_id, customer_id)')
            ->orderBy('barang_id')
            ->orderBy('min_qty')
            ->get();

        $daftarHarga = $rows->groupBy(fn ($r) => $r->entitas === 'customer' ? 'customer' : 'supplier')
            ->map(function ($group) {
                return $group->groupBy(fn ($r) => $r->entitas === 'customer' ? 'customer_'.$r->customer_id : 'supplier_'.$r->supplier_id);
            });

        $suppliers = Supplier::aktif()->orderBy('nama')->get();
        $customers = Customer::aktif()->orderBy('nama')->get();
        $showHistory = $request->boolean('show_history');

        return view('master.daftar-harga.index', [
            'daftarHarga' => $daftarHarga,
            'suppliers' => $suppliers,
            'customers' => $customers,
            'showHistory' => $showHistory,
            'kolomOptions' => self::KOLOM_OPTIONS,
            'kolomAktif' => self::kolomAktif(),
        ]);
    }

    public function simpanKolom(Request $request)
    {
        KolomTabelService::simpan($request, 'daftar_harga_kolom', self::KOLOM_OPTIONS, ['barang', 'aksi']);

        return back()->with('success', 'Pengaturan kolom berhasil disimpan.');
    }

    public static function kolomAktif(): array
    {
        return KolomTabelService::aktif('daftar_harga_kolom', self::KOLOM_OPTIONS, self::KOLOM_DEFAULT, ['barang', 'aksi']);
    }

    public function create()
    {
        $suppliers = Supplier::aktif()->orderBy('nama')->get();
        $customers = Customer::aktif()->orderBy('nama')->get();
        $barang = Barang::aktif()->barang()->orderBy('nama')->get();

        $formTiers = [[
            'id' => null,
            'min_qty' => 1,
            'max_qty' => null,
            'harga' => 0,
            'keterangan' => '',
        ]];

        $entitas = 'supplier';

        return view('master.daftar-harga.form', compact('suppliers', 'customers', 'barang', 'formTiers', 'entitas'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $entitas = $validated['entitas'];
        $rekananKolom = $entitas === 'customer' ? 'customer_id' : 'supplier_id';
        $rekananId = (int) $validated[$rekananKolom];

        DB::beginTransaction();
        try {
            foreach ($validated['tiers'] as $tier) {
                $overlap = $this->queryOverlap($entitas, $rekananKolom, $rekananId, $validated['barang_id'], $tier, []);

                if ($overlap) {
                    DB::rollBack();

                    return back()->withErrors([
                        'tiers' => 'Range qty overlap dengan harga aktif yang sudah ada untuk barang ini pada '
                            .($entitas === 'customer' ? 'customer yang sama.' : 'supplier yang sama.'),
                    ])->withInput();
                }

                $this->createRow($entitas, $rekananKolom, $rekananId, $validated, $tier);
            }

            DB::commit();

            return redirect()->route('daftar-harga.index', ['entitas' => $entitas])
                ->with('success', 'Daftar harga berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menyimpan: '.$e->getMessage())->withInput();
        }
    }

    public function edit(DaftarHarga $daftarHarga)
    {
        $daftarHarga->load(['supplier', 'customer', 'barang']);

        $suppliers = Supplier::aktif()->orderBy('nama')->get();
        $customers = Customer::aktif()->orderBy('nama')->get();
        $barang = Barang::aktif()->barang()->orderBy('nama')->get();

        $entitas = $daftarHarga->entitas ?: 'supplier';
        $rekananKolom = $entitas === 'customer' ? 'customer_id' : 'supplier_id';

        // Ambil semua tier aktif untuk entitas+rekanan+barang ini (agar bisa diedit bersamaan)
        $tiers = DaftarHarga::aktif()
            ->where('entitas', $entitas)
            ->where($rekananKolom, $entitas === 'customer' ? $daftarHarga->customer_id : $daftarHarga->supplier_id)
            ->where('barang_id', $daftarHarga->barang_id)
            ->orderBy('min_qty')
            ->get();

        $formTiers = $tiers->map(fn ($t) => [
            'id' => $t->id,
            'min_qty' => (float) $t->min_qty,
            'max_qty' => $t->max_qty !== null ? (float) $t->max_qty : null,
            'harga' => (float) $t->harga,
            'keterangan' => $t->keterangan ?? '',
        ])->values()->all();

        $formTiers = $formTiers ?: [[
            'id' => $daftarHarga->id,
            'min_qty' => (float) $daftarHarga->min_qty,
            'max_qty' => $daftarHarga->max_qty !== null ? (float) $daftarHarga->max_qty : null,
            'harga' => (float) $daftarHarga->harga,
            'keterangan' => $daftarHarga->keterangan ?? '',
        ]];

        return view('master.daftar-harga.form', compact('daftarHarga', 'suppliers', 'customers', 'barang', 'formTiers', 'entitas'));
    }

    public function update(Request $request, DaftarHarga $daftarHarga)
    {
        $validated = $this->validateRequest($request);
        $entitas = $validated['entitas'];
        $rekananKolom = $entitas === 'customer' ? 'customer_id' : 'supplier_id';
        $rekananId = (int) $validated[$rekananKolom];
        $barangId = (int) $validated['barang_id'];

        DB::beginTransaction();
        try {
            // Hapus tier aktif lama yang TIDAK ada di array tiers (user menghapus baris)
            $existingIds = collect($validated['tiers'])->pluck('id')->filter()->values()->toArray();
            $archivedRows = DaftarHarga::aktif()
                ->where('entitas', $entitas)
                ->where($rekananKolom, $rekananId)
                ->where('barang_id', $barangId)
                ->whereNotIn('id', $existingIds)
                ->get();

            if ($archivedRows->isNotEmpty()) {
                $archivedSnapshots = $archivedRows->map(fn ($r) => $r->snapshot());
                DaftarHarga::whereIn('id', $archivedRows->pluck('id'))
                    ->update(['is_aktif' => false, 'tanggal_selesai' => now()->toDateString()]);

                foreach ($archivedRows as $i => $row) {
                    DaftarHarga::catatRiwayat(
                        $entitas,
                        $entitas === 'customer' ? null : $rekananId,
                        $entitas === 'customer' ? $rekananId : null,
                        $barangId,
                        DaftarHargaRiwayat::TIPE_NONAKTIF,
                        $archivedSnapshots[$i] ?? null,
                        null,
                        'Baris tier dihapus dari daftar'
                    );
                }
            }

            foreach ($validated['tiers'] as $tier) {
                // Saat update, form = sumber kebenaran baru untuk kombinasi ini.
                // Baris yang TIDAK dikirim sudah diarsipkan di atas, jadi tidak ada
                // cek overlap; mesin pencari harga menangani range beririsan
                // (prefer tier range tersempit, lihat DaftarHarga::cariDalamEntitas).

                if (! empty($tier['id'])) {
                    $oldRow = DaftarHarga::find($tier['id']);
                    if (! $oldRow || $oldRow->entitas !== $entitas
                        || (int) $oldRow->{$rekananKolom} !== $rekananId
                        || (int) $oldRow->barang_id !== $barangId) {
                        throw new \RuntimeException('Tier daftar harga tidak sesuai kombinasi entitas/barang.');
                    }
                    $oldSnapshot = $oldRow->snapshot();

                    DaftarHarga::where('id', $tier['id'])->update([
                        'min_qty' => $tier['min_qty'],
                        'max_qty' => $tier['max_qty'] ?? null,
                        'harga' => $tier['harga'],
                        'keterangan' => $tier['keterangan'] ?? null,
                        'tanggal_mulai' => $validated['tanggal_mulai'] ?? null,
                        'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
                    ]);

                    $newRow = DaftarHarga::find($tier['id']);
                    $newSnapshot = $newRow ? $newRow->snapshot() : null;

                    if ($oldSnapshot !== $newSnapshot) {
                        DaftarHarga::catatRiwayat(
                            $entitas,
                            $entitas === 'customer' ? null : $rekananId,
                            $entitas === 'customer' ? $rekananId : null,
                            $barangId,
                            DaftarHargaRiwayat::TIPE_UBAH,
                            $oldSnapshot,
                            $newSnapshot,
                            $tier['keterangan'] ?? null
                        );
                    }
                } else {
                    $this->createRow($entitas, $rekananKolom, $rekananId, $validated, $tier);
                }
            }

            DB::commit();

            return redirect()->route('daftar-harga.index', ['entitas' => $entitas])
                ->with('success', 'Daftar harga berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal memperbarui: '.$e->getMessage())->withInput();
        }
    }

    public function destroy(DaftarHarga $daftarHarga)
    {
        $entitas = $daftarHarga->entitas ?: 'supplier';
        $rekananKolom = $entitas === 'customer' ? 'customer_id' : 'supplier_id';
        $rekananId = $entitas === 'customer' ? $daftarHarga->customer_id : $daftarHarga->supplier_id;

        // Soft archive: nonaktifkan SEMUA tier aktif untuk entitas+rekanan+barang yang sama
        $rows = DaftarHarga::aktif()
            ->where('entitas', $entitas)
            ->where($rekananKolom, $rekananId)
            ->where('barang_id', $daftarHarga->barang_id)
            ->get();

        $snapshots = $rows->map(fn ($r) => $r->snapshot());

        DaftarHarga::whereIn('id', $rows->pluck('id'))
            ->update([
                'is_aktif' => false,
                'tanggal_selesai' => now()->toDateString(),
            ]);

        foreach ($rows as $i => $row) {
            DaftarHarga::catatRiwayat(
                $entitas,
                $entitas === 'customer' ? null : $rekananId,
                $entitas === 'customer' ? $rekananId : null,
                $daftarHarga->barang_id,
                DaftarHargaRiwayat::TIPE_NONAKTIF,
                $snapshots[$i] ?? null,
                null,
                'Daftar harga dinonaktifkan'
            );
        }

        return redirect()->route('daftar-harga.index', ['entitas' => $entitas])
            ->with('success', 'Daftar harga berhasil dinonaktifkan (riwayat tetap tersimpan).');
    }

    public function riwayat(Request $request)
    {
        $suppliers = Supplier::aktif()->orderBy('nama')->get();
        $customers = Customer::aktif()->orderBy('nama')->get();
        $barangList = Barang::aktif()->barang()->orderBy('nama')->get();
        $riwayat = null;
        $vertical = [];

        $entitas = $request->filled('entitas') ? $request->entitas : 'supplier';
        $perubahan = collect();

        if ($entitas === 'customer' && $request->filled('customer_id') && $request->filled('barang_id')) {
            $riwayat = DaftarHarga::riwayat('customer', (int) $request->customer_id, 'customer_id', (int) $request->barang_id);
            $vertical = ['kunci' => 'customer_id', 'tipe' => 'customer'];
            $perubahan = DaftarHargaRiwayat::with(['supplier', 'customer', 'barang'])
                ->where('entitas', 'customer')
                ->where('customer_id', (int) $request->customer_id)
                ->where('barang_id', (int) $request->barang_id)
                ->orderByDesc('id')
                ->get();
        } elseif ($entitas !== 'customer' && $request->filled('supplier_id') && $request->filled('barang_id')) {
            $riwayat = DaftarHarga::riwayat('supplier', (int) $request->supplier_id, 'supplier_id', (int) $request->barang_id);
            $vertical = ['kunci' => 'supplier_id', 'tipe' => 'supplier'];
            $perubahan = DaftarHargaRiwayat::with(['supplier', 'customer', 'barang'])
                ->where('entitas', 'supplier')
                ->where('supplier_id', (int) $request->supplier_id)
                ->where('barang_id', (int) $request->barang_id)
                ->orderByDesc('id')
                ->get();
        }

        return view('master.daftar-harga.riwayat', compact('suppliers', 'customers', 'barangList', 'riwayat', 'perubahan', 'entitas', 'vertical'));
    }

    // ===== Helpers =====

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'entitas' => 'required|in:supplier,customer',
            'supplier_id' => 'required_if:entitas,supplier|nullable|exists:suppliers,id',
            'customer_id' => 'required_if:entitas,customer|nullable|exists:customers,id',
            'barang_id' => 'required|exists:barang,id',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|'.($request->filled('tanggal_mulai') ? 'after_or_equal:tanggal_mulai' : ''),
            'tiers' => 'required|array|min:1',
            'tiers.*.id' => 'nullable|exists:daftar_harga,id',
            'tiers.*.min_qty' => 'required|numeric|min:1',
            'tiers.*.max_qty' => 'nullable|numeric|gte:tiers.*.min_qty',
            'tiers.*.harga' => 'required|numeric|min:0',
            'tiers.*.keterangan' => 'nullable|string|max:255',
        ]);
    }

    private function createRow(string $entitas, string $rekananKolom, int $rekananId, array $validated, array $tier)
    {
        $data = [
            'entitas' => $entitas,
            'barang_id' => $validated['barang_id'],
            'harga' => $tier['harga'],
            'is_aktif' => true,
            'min_qty' => $tier['min_qty'],
            'max_qty' => $tier['max_qty'] ?? null,
            'tanggal_mulai' => $validated['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'keterangan' => $tier['keterangan'] ?? null,
        ];

        if ($entitas === 'customer') {
            $data['customer_id'] = $rekananId;
            $data['supplier_id'] = null;
        } else {
            $data['supplier_id'] = $rekananId;
            $data['customer_id'] = null;
        }

        $row = DaftarHarga::create($data);

        DaftarHarga::catatRiwayat(
            $entitas,
            $entitas === 'customer' ? null : $rekananId,
            $entitas === 'customer' ? $rekananId : null,
            (int) $data['barang_id'],
            DaftarHargaRiwayat::TIPE_BUAT,
            null,
            $row->snapshot(),
            $tier['keterangan'] ?? null
        );

        return $row;
    }

    private function queryOverlap(string $entitas, string $rekananKolom, int $rekananId, int $barangId, array $tier, array $ignoreIds = [])
    {
        $overlap = DaftarHarga::aktif()
            ->where('entitas', $entitas)
            ->where($rekananKolom, $rekananId)
            ->where('barang_id', $barangId)
            ->where(function ($q) use ($tier) {
                $q->where(function ($q2) use ($tier) {
                    $q2->where('min_qty', '<=', $tier['max_qty'] ?? 999999999)
                        ->where(function ($q3) use ($tier) {
                            $q3->whereNull('max_qty')
                                ->orWhere('max_qty', '>=', $tier['min_qty']);
                        });
                });
            });

        if (! empty($ignoreIds)) {
            $overlap->whereNotIn('id', $ignoreIds);
        }

        return $overlap->exists();
    }
}
