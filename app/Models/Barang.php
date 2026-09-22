<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Barang extends Model
{
    use BelongsToUser;

    protected $table = 'barang';

    protected $fillable = [
        'kode', 'nama', 'foto', 'satuan', 'kategori', 'merek', 'ukuran', 'warna',
        'tipe', 'gudang_id', 'stok', 'harga_avg', 'harga_beli', 'harga_jual', 'min_stok',
        'barcode', 'keterangan', 'is_aktif',
    ];

    protected $casts = [
        'stok' => 'decimal:2',
        'harga_avg' => 'decimal:2',
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'min_stok' => 'decimal:2',
        'is_aktif' => 'boolean',
    ];

    public function daftarHarga(): HasMany
    {
        return $this->hasMany(DaftarHarga::class);
    }

    public function bbPersediaan(): HasMany
    {
        return $this->hasMany(BbPersediaan::class);
    }

    public function returPembelianItems(): HasMany
    {
        return $this->hasMany(ReturPembelianItem::class);
    }

    public function returPenjualanItems(): HasMany
    {
        return $this->hasMany(ReturPenjualanItem::class);
    }

    public function perubahanStokItems(): HasMany
    {
        return $this->hasMany(PerubahanStokItem::class);
    }

    public function stokOpnameItems(): HasMany
    {
        return $this->hasMany(StokOpnameItem::class);
    }

    public function transferGudangItems(): HasMany
    {
        return $this->hasMany(TransferGudangItem::class);
    }

    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class, 'gudang_id');
    }

    public function stokGudang(): HasMany
    {
        return $this->hasMany(StokGudang::class);
    }

    protected static function booted(): void
    {
        static::saved(function (Barang $barang) {
            $barang->seimbangkanStokGudang();
        });
    }

    /**
     * Jaga invariant sum(stok_gudang) == barang.stok.
     *
     * Gudang utama barang menjadi baris penyeimbang: qty-nya = total stok
     * dikurangi stok semua gudang lainnya (yang hanya berubah via transfer).
     */
    public function seimbangkanStokGudang(): void
    {
        $gudangId = $this->gudang_id;

        if (! $gudangId) {
            $utama = Gudang::utama();
            if (! $utama) {
                return;
            }
            $this->gudang_id = (int) $utama->id;
            $this->saveQuietly();
            $gudangId = (int) $this->gudang_id;
        }

        StokGudang::firstOrCreate(
            ['barang_id' => $this->id, 'gudang_id' => $gudangId],
            ['qty' => 0]
        );

        $stokLain = (float) StokGudang::where('barang_id', $this->id)
            ->where('gudang_id', '!=', $gudangId)
            ->sum('qty');

        $qtyUtama = max(0, round((float) $this->stok - $stokLain, 2));

        StokGudang::where('barang_id', $this->id)
            ->where('gudang_id', $gudangId)
            ->update(['qty' => $qtyUtama]);
    }

    public function pembelianItems(): HasMany
    {
        return $this->hasMany(PembelianItem::class);
    }

    public function penjualanItems(): HasMany
    {
        return $this->hasMany(PenjualanItem::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    public function scopeBarang($query)
    {
        return $query->where('tipe', 'barang');
    }

    public function scopeJasa($query)
    {
        return $query->where('tipe', 'jasa');
    }

    /**
     * Apakah produk sudah tercatat dalam alur transaksi/stok sehingga tidak aman
     * dihapus permanen (FK nullOnDelete akan mengosongkan referensi transaksi).
     */
    public function terpakai(): bool
    {
        $periksa = [
            fn () => $this->pembelianItems()->exists(),
            fn () => $this->penjualanItems()->exists(),
            fn () => $this->bbPersediaan()->exists(),
            fn () => $this->returPembelianItems()->exists(),
            fn () => $this->returPenjualanItems()->exists(),
            fn () => $this->perubahanStokItems()->exists(),
            fn () => $this->stokOpnameItems()->exists(),
            fn () => $this->transferGudangItems()->exists(),
        ];

        foreach ($periksa as $cek) {
            if ($cek()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Arsipkan produk: nonaktifkan produk dan seluruh daftar harga aktifnya.
     * Riwayat transaksi, jurnal, dan stok tidak diubah.
     */
    public function arsipkan(string $catatan = 'Produk diarsipkan karena memiliki riwayat transaksi/stok'): void
    {
        if ($this->is_aktif) {
            $this->update(['is_aktif' => false]);
        }

        DaftarHarga::nonaktifkanUntukBarang($this->id, $catatan);
    }

    /**
     * Bersihkan referensi pendukung barang sebelum dihapus permanen:
     * daftar harga, riwayat harga, saldo per gudang, dan file foto.
     * Data transaksi/retur/stok TIDAK disentuh karena produk yang memakai
     * tabel-tabel itu harus diarsipkan, bukan dihapus.
     */
    public function bersihkanReferensiPendukung(): void
    {
        DaftarHarga::where('barang_id', $this->id)->delete();
        DaftarHargaRiwayat::where('barang_id', $this->id)->delete();
        StokGudang::where('barang_id', $this->id)->delete();

        if ($this->foto) {
            Storage::disk('public')->delete($this->foto);
        }
    }

    protected function warnaHex(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->warna) {
                    return null;
                }
                $path = base_path('referensi_warna.json');
                if (! file_exists($path)) {
                    return null;
                }
                $data = json_decode(file_get_contents($path), true);
                foreach ($data['kategori'] ?? [] as $kategori) {
                    foreach ($kategori['warna'] ?? [] as $warna) {
                        if ($warna['nama'] === $this->warna) {
                            return $warna['hex'] ?? null;
                        }
                    }
                }

                return null;
            }
        );
    }

    protected function label(): Attribute
    {
        return Attribute::make(
            get: function () {
                $parts = [$this->nama];
                if ($this->merek) {
                    $parts[] = $this->merek;
                }
                if ($this->ukuran) {
                    $parts[] = $this->ukuran;
                }
                if ($this->warna) {
                    $parts[] = $this->warna;
                }

                return implode(' - ', $parts);
            }
        );
    }

    protected function fotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->foto ? asset('storage/'.$this->foto) : null
        );
    }

    protected function inisial(): Attribute
    {
        return Attribute::make(
            get: function () {
                $words = preg_split('/\s+/', trim((string) $this->nama)) ?: [];

                if (count($words) >= 2) {
                    return mb_strtoupper(mb_substr($words[0], 0, 1).mb_substr($words[1], 0, 1));
                }

                $first = $words[0] ?? 'B';

                return mb_strtoupper(mb_substr($first, 0, 2));
            }
        );
    }

    protected function avatarWarna(): Attribute
    {
        return Attribute::make(
            get: function () {
                $palette = ['bg-emerald-600', 'bg-sky-600', 'bg-violet-600', 'bg-amber-600', 'bg-rose-600', 'bg-cyan-600', 'bg-indigo-600', 'bg-teal-600'];
                $key = $this->inisial ? ord($this->inisial[0]) : 0;

                return $palette[$key % count($palette)];
            }
        );
    }

    public static function generateKode(string $prefix = 'BRG'): string
    {
        $max = (int) static::max('id') + 1;

        return $prefix.'-'.str_pad((string) $max, 4, '0', STR_PAD_LEFT);
    }

    public static function getWarnaList(): array
    {
        $path = base_path('referensi_warna.json');
        if (! file_exists($path)) {
            return [];
        }
        $data = json_decode(file_get_contents($path), true);
        $list = [];
        foreach ($data['kategori'] ?? [] as $kategori) {
            foreach ($kategori['warna'] ?? [] as $warna) {
                $list[] = [
                    'nama' => $warna['nama'],
                    'hex' => $warna['hex'] ?? '#000000',
                    'kategori' => $kategori['nama_kategori'],
                ];
            }
        }

        return $list;
    }

    public static function getUkuranOptions(): array
    {
        return ['S', 'M', 'L', 'XL', 'XXL', 'XXXL', '28', '30', '32', '34', '36', '38', '40', '42', '44', '46'];
    }
}
