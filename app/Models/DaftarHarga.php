<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DaftarHarga extends Model
{
    use BelongsToUser;

    protected $table = 'daftar_harga';

    protected $fillable = [
        'entitas', 'supplier_id', 'customer_id', 'barang_id', 'harga', 'is_aktif',
        'min_qty', 'max_qty', 'tanggal_mulai', 'tanggal_selesai', 'keterangan',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'is_aktif' => 'boolean',
        'min_qty' => 'decimal:2',
        'max_qty' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_aktif', true)
            ->where(function ($q) {
                $q->whereNull('tanggal_selesai')
                    ->orWhere('tanggal_selesai', '>=', now()->toDateString());
            });
    }

    public function scopeSupplier(Builder $query): Builder
    {
        return $query->where('entitas', 'supplier');
    }

    public function scopeCustomer(Builder $query): Builder
    {
        return $query->where('entitas', 'customer');
    }

    /**
     * Entitas rekanan yang relevan (supplier untuk beli, customer untuk jual).
     */
    public function rekananLabel(): string
    {
        if ($this->entitas === 'customer') {
            return $this->customer?->nama ?? '-';
        }

        return $this->supplier?->nama ?? '-';
    }

    /**
     * Cari harga aktif berdasarkan supplier, barang, dan jumlah (harga beli).
     * Prefer tier yang paling spesifik (range paling kecil).
     */
    public static function cariBeli(int $supplierId, int $barangId, float $qty = 1): ?self
    {
        return static::cariDalamEntitas('supplier', 'supplier_id', $supplierId, $barangId, $qty);
    }

    /**
     * Cari harga aktif berdasarkan customer, barang, dan jumlah (harga jual).
     * Prefer tier yang paling spesifik (range paling kecil).
     */
    public static function cariJual(int $customerId, int $barangId, float $qty = 1): ?self
    {
        return static::cariDalamEntitas('customer', 'customer_id', $customerId, $barangId, $qty);
    }

    /**
     * Backward-compatible alias untuk harga beli per supplier.
     */
    public static function cariHarga(int $supplierId, int $barangId, float $qty = 1): ?self
    {
        return static::cariBeli($supplierId, $barangId, $qty);
    }

    protected static function cariDalamEntitas(string $entitas, string $kolom, int $rekananId, int $barangId, float $qty): ?self
    {
        $candidates = static::aktif()
            ->where('entitas', $entitas)
            ->where($kolom, $rekananId)
            ->where('barang_id', $barangId)
            ->where('min_qty', '<=', $qty)
            ->where(function ($q) use ($qty) {
                $q->whereNull('max_qty')
                    ->orWhere('max_qty', '>=', $qty);
            })
            ->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        // Prefer tier dengan range paling sempit (paling spesifik)
        return $candidates->sortBy(function ($item) {
            $range = $item->max_qty ? ($item->max_qty - $item->min_qty) : 999999999;

            return $range;
        })->first();
    }

    /**
     * Cari semua harga (aktif + historis) untuk 1 kombinasi entitas + rekanan + barang.
     *
     * @return Collection
     */
    public static function riwayat(string $entitas, int $rekananId, string $kolom, int $barangId)
    {
        return static::where('entitas', $entitas)
            ->where($kolom, $rekananId)
            ->where('barang_id', $barangId)
            ->orderByDesc('tanggal_mulai')
            ->orderByDesc('min_qty')
            ->with(['supplier', 'customer', 'barang'])
            ->get();
    }

    /**
     * Snapshot nilai baris ini (untuk dicatat di riwayat perubahan).
     */
    public function snapshot(): array
    {
        return [
            'harga' => (float) $this->harga,
            'min_qty' => $this->min_qty !== null ? (float) $this->min_qty : null,
            'max_qty' => $this->max_qty !== null ? (float) $this->max_qty : null,
            'tanggal_mulai' => $this->tanggal_mulai ? $this->tanggal_mulai->toDateString() : null,
            'tanggal_selesai' => $this->tanggal_selesai ? $this->tanggal_selesai->toDateString() : null,
        ];
    }

    /**
     * Catat perubahan harga ke tabel riwayat (buat / ubah / nonaktif).
     */
    public static function catatRiwayat(
        string $entitas,
        ?int $supplierId,
        ?int $customerId,
        int $barangId,
        string $tipe,
        ?array $lama = null,
        ?array $baru = null,
        ?string $catatan = null
    ): void {
        DaftarHargaRiwayat::create([
            'entitas' => $entitas,
            'supplier_id' => $supplierId,
            'customer_id' => $customerId,
            'barang_id' => $barangId,
            'tipe' => $tipe,
            'harga_lama' => $lama['harga'] ?? null,
            'harga_baru' => $baru['harga'] ?? null,
            'min_qty_lama' => $lama['min_qty'] ?? null,
            'max_qty_lama' => $lama['max_qty'] ?? null,
            'min_qty_baru' => $baru['min_qty'] ?? null,
            'max_qty_baru' => $baru['max_qty'] ?? null,
            'tanggal_mulai_lama' => $lama['tanggal_mulai'] ?? null,
            'tanggal_mulai_baru' => $baru['tanggal_mulai'] ?? null,
            'catatan' => $catatan,
        ]);
    }

    /**
     * Nonaktifkan SEMUA tier harga aktif untuk sebuah barang (dipakai saat
     * produk diarsipkan/dihapus) sambil tetap mencatat riwayat perubahan.
     */
    public static function nonaktifkanUntukBarang(int $barangId, string $catatan): void
    {
        $rows = static::aktif()
            ->where('barang_id', $barangId)
            ->get();

        if ($rows->isEmpty()) {
            return;
        }

        $snapshots = $rows->map(fn ($r) => $r->snapshot());

        static::whereIn('id', $rows->pluck('id'))
            ->update([
                'is_aktif' => false,
                'tanggal_selesai' => now()->toDateString(),
            ]);

        foreach ($rows as $i => $row) {
            static::catatRiwayat(
                $row->entitas,
                $row->supplier_id,
                $row->customer_id,
                $row->barang_id,
                DaftarHargaRiwayat::TIPE_NONAKTIF,
                $snapshots[$i] ?? null,
                null,
                $catatan
            );
        }
    }

    /**
     * Sinkronkan harga beli dari transaksi pembelian: update baris daftar harga
     * aktif yang cocok dengan qty, atau buat baris baru yang aman (tanpa overlap).
     * Harga lama tetap tersimpan di riwayat. Harga non-positif diabaikan agar
     * daftar harga tidak pernah tertulis nominal 0.
     */
    public static function sinkronBeli(int $supplierId, int $barangId, float $qty, float $hargaBaru): void
    {
        if ((float) $hargaBaru <= 0) {
            return;
        }

        static::sinkronHargaEntitas('supplier', 'supplier_id', $supplierId, $barangId, (float) $qty, (float) $hargaBaru);
    }

    /**
     * Sinkronkan harga jual dari transaksi penjualan (lihat sinkronBeli).
     */
    public static function sinkronJual(int $customerId, int $barangId, float $qty, float $hargaBaru): void
    {
        if ((float) $hargaBaru <= 0) {
            return;
        }

        static::sinkronHargaEntitas('customer', 'customer_id', $customerId, $barangId, (float) $qty, (float) $hargaBaru);
    }

    /**
     * Inti sinkronisasi harga: update tier yang mencakup qty, atau bila tidak ada
     * yang mencakup, update baris tanpa batas atas (harga umum) bila ada, atau
     * buat tier baru yang tidak bertabrakan dengan tier aktif lainnya.
     */
    protected static function sinkronHargaEntitas(string $entitas, string $kolom, int $rekananId, int $barangId, float $qty, float $harga): void
    {
        $buatNote = $entitas === 'supplier' ? 'Dibuat dari transaksi pembelian' : 'Dibuat dari transaksi penjualan';
        $ubahNote = $entitas === 'supplier' ? 'Diperbarui otomatis dari transaksi pembelian' : 'Diperbarui otomatis dari transaksi penjualan';

        $row = static::cariDalamEntitas($entitas, $kolom, $rekananId, $barangId, $qty);

        if ($row) {
            if (abs((float) $row->harga - $harga) < 0.005) {
                return;
            }

            $lama = $row->snapshot();
            $row->update(['harga' => $harga]);
            static::catatRiwayat($entitas, $kolom === 'supplier_id' ? $rekananId : null, $kolom === 'customer_id' ? $rekananId : null, $barangId, DaftarHargaRiwayat::TIPE_UBAH, $lama, $row->snapshot(), $ubahNote);

            return;
        }

        $rowsAktif = static::aktif()
            ->where('entitas', $entitas)
            ->where($kolom, $rekananId)
            ->where('barang_id', $barangId)
            ->orderBy('min_qty')
            ->get();

        // Belum ada baris aktif sama sekali: buat harga umum tanpa batas atas.
        if ($rowsAktif->isEmpty()) {
            $row = static::create([
                'entitas' => $entitas,
                'supplier_id' => $entitas === 'supplier' ? $rekananId : null,
                'customer_id' => $entitas === 'customer' ? $rekananId : null,
                'barang_id' => $barangId,
                'harga' => $harga,
                'is_aktif' => true,
                'min_qty' => 1,
                'max_qty' => null,
                'tanggal_mulai' => now()->toDateString(),
            ]);

            static::catatRiwayat($entitas, $row->supplier_id, $row->customer_id, $barangId, DaftarHargaRiwayat::TIPE_BUAT, null, $row->snapshot(), $buatNote);

            return;
        }

        // Tak ada tier yang mencakup qty tetapi ada baris lain. Karena cariDalamEntitas
        // gagal, semua baris tanpa batas atas pasti ber-min_qty lebih besar dari qty.
        // Buat tier baru hingga batas tier berikutnya agar tidak tumpang tindih.
        $nextMin = $rowsAktif->where('min_qty', '>', $qty)->min('min_qty');
        $row = static::create([
            'entitas' => $entitas,
            'supplier_id' => $entitas === 'supplier' ? $rekananId : null,
            'customer_id' => $entitas === 'customer' ? $rekananId : null,
            'barang_id' => $barangId,
            'harga' => $harga,
            'is_aktif' => true,
            'min_qty' => $qty,
            'max_qty' => $nextMin !== null ? ($nextMin - 0.01) : null,
            'tanggal_mulai' => now()->toDateString(),
        ]);

        static::catatRiwayat($entitas, $row->supplier_id, $row->customer_id, $barangId, DaftarHargaRiwayat::TIPE_BUAT, null, $row->snapshot(), $buatNote);
    }
}
