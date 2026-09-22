<?php

namespace App\Events\Transaksi;

use App\Models\Pembelian;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PembelianCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Pembelian $pembelian) {}
}
