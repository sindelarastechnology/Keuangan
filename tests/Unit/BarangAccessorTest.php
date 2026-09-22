<?php

namespace Tests\Unit;

use App\Models\Barang;
use PHPUnit\Framework\TestCase;

class BarangAccessorTest extends TestCase
{
    public function test_inisial_dua_kata(): void
    {
        $barang = new Barang(['nama' => 'Kaos Polos']);

        $this->assertSame('KP', $barang->inisial);
    }

    public function test_inisial_satu_kata(): void
    {
        $barang = new Barang(['nama' => 'Beras']);

        $this->assertSame('BE', $barang->inisial);
    }

    public function test_foto_url_null_saat_tanpa_foto(): void
    {
        $barang = new Barang(['nama' => 'Beras']);

        $this->assertNull($barang->foto_url);
    }
}
