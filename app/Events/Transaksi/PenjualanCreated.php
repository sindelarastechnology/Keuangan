<?php

namespace App\Events\Transaksi;

use App\Models\Penjualan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PenjualanCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Penjualan $penjualan) {}
}
