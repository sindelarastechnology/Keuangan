<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class DaftarHargaRiwayat extends Model
{
    use BelongsToUser;

    protected $table = 'daftar_harga_riwayat';

    const TIPE_BUAT = 'buat';

    const TIPE_UBAH = 'ubah';

    const TIPE_NONAKTIF = 'nonaktif';

    protected $fillable = [
        'entitas', 'supplier_id', 'customer_id', 'barang_id', 'tipe',
        'harga_lama', 'harga_baru',
        'min_qty_lama', 'max_qty_lama', 'min_qty_baru', 'max_qty_baru',
        'tanggal_mulai_lama', 'tanggal_mulai_baru', 'catatan',
    ];

    protected $casts = [
        'harga_lama' => 'decimal:2',
        'harga_baru' => 'decimal:2',
        'min_qty_lama' => 'decimal:2',
        'max_qty_lama' => 'decimal:2',
        'min_qty_baru' => 'decimal:2',
        'max_qty_baru' => 'decimal:2',
        'tanggal_mulai_lama' => 'date',
        'tanggal_mulai_baru' => 'date',
    ];

    private const MAX_HARGA = 999_999_999_999.99;

    protected static function booted(): void
    {
        static::creating(function (self $riwayat) {
            foreach (['harga_lama', 'harga_baru'] as $kolom) {
                if (abs((float) $riwayat->getAttribute($kolom)) > static::MAX_HARGA) {
                    throw new \DomainException("Nilai {$kolom} melebihi kapasitas kolom riwayat (decimal 15,2).");
                }
            }
        });
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function rekananLabel(): string
    {
        if ($this->entitas === 'customer') {
            return $this->customer?->nama ?? '-';
        }

        return $this->supplier?->nama ?? '-';
    }
}
