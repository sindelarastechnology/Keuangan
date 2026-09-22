<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\Aset;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\DaftarHarga;
use App\Models\Gudang;
use App\Models\JurnalItem;
use App\Models\JurnalUmum;
use App\Models\KasKeluar;
use App\Models\KasKeluarItem;
use App\Models\KasMasuk;
use App\Models\KasMasukItem;
use App\Models\Kategori;
use App\Models\MutasiBank;
use App\Models\Pajak;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\PerubahanStok;
use App\Models\PerubahanStokItem;
use App\Models\Rekening;
use App\Models\ReturPembelian;
use App\Models\ReturPembelianItem;
use App\Models\ReturPenjualan;
use App\Models\ReturPenjualanItem;
use App\Models\Satuan;
use App\Models\StokOpname;
use App\Models\StokOpnameItem;
use App\Models\Supplier;
use App\Models\TransferGudang;
use App\Models\TransferGudangItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HalamanRenderBerparameterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(User::where('email', 'admin@keuangan.test')->firstOrFail());
    }

    private function akun(string $kode): int
    {
        return AkunPerkiraan::where('kode', $kode)->firstOrFail()->id;
    }

    private function buatMaster(): array
    {
        $unik = substr((string) uniqid(), -4);

        $gudangCabang = Gudang::create(['kode' => 'GDG-'.$unik, 'nama' => 'Gudang Cabang', 'is_aktif' => true]);
        $satuan = Satuan::create(['nama' => 'pcs']);
        $kategori = Kategori::create(['nama' => 'Umum']);
        $supplier = Supplier::create(['kode' => 'SUP-'.$unik, 'nama' => 'Supplier Tes', 'is_aktif' => true]);
        $customer = Customer::create(['kode' => 'CUS-'.$unik, 'nama' => 'Customer Tes', 'is_aktif' => true]);
        $kas = Rekening::create(['jenis' => 'kas', 'nama' => 'Kas Kecil', 'akun_id' => $this->akun('111'), 'saldo_awal' => 0, 'is_aktif' => true]);
        $bank = Rekening::create(['jenis' => 'bank', 'nama' => 'BCA Tes', 'nomor_rekening' => '12345', 'nama_pemilik' => 'PT Tes', 'akun_id' => $this->akun('111'), 'saldo_awal' => 0, 'is_aktif' => true]);
        $hargaJual = 9000 + rand(0, 1000);

        return compact('unik', 'gudangCabang', 'satuan', 'kategori', 'supplier', 'customer', 'kas', 'bank', 'hargaJual');
    }

    public function test_semua_halaman_berparameter_merender_ok(): void
    {
        $m = $this->buatMaster();
        $tanggal = now()->toDateString();
        $admin = User::where('email', 'admin@keuangan.test')->firstOrFail();

        $barang = Barang::create([
            'kode' => 'BRG-'.$m['unik'], 'nama' => 'Barang Tes', 'tipe' => 'barang',
            'stok' => 100, 'harga_beli' => 3000, 'harga_jual' => $m['hargaJual'], 'is_aktif' => true,
            'satuan_id' => $m['satuan']->id, 'kategori_id' => $m['kategori']->id,
        ]);

        $jasa = Barang::create([
            'kode' => 'JAS-'.$m['unik'], 'nama' => 'Jasa Tes', 'tipe' => 'jasa',
            'stok' => 0, 'harga_beli' => 0, 'harga_jual' => 250000, 'is_aktif' => true,
            'satuan_id' => $m['satuan']->id, 'kategori_id' => $m['kategori']->id,
        ]);

        $pajak = Pajak::where('rate', 11)->firstOrFail();

        $dh = DaftarHarga::create([
            'entitas' => 'supplier', 'supplier_id' => $m['supplier']->id, 'barang_id' => $barang->id,
            'harga' => 7800, 'is_aktif' => true, 'min_qty' => 1, 'max_qty' => null,
            'tanggal_mulai' => $tanggal,
        ]);

        $km = KasMasuk::create([
            'nomor' => 'KM-'.$m['unik'], 'tanggal' => $tanggal, 'rekening_id' => $m['kas']->id,
            'keterangan' => 'Penjualan tunai', 'total' => 500000, 'pajak_id' => null,
            'pajak_nominal' => 0, 'grand_total' => 500000, 'created_by' => $admin->id,
        ]);
        $km->forceFill(['approval_status' => 'approved'])->save();
        KasMasukItem::create(['kas_masuk_id' => $km->id, 'akun_id' => $this->akun('411'), 'keterangan' => 'Penjualan tunai', 'nominal' => 500000]);

        $kk = KasKeluar::create([
            'nomor' => 'KK-'.$m['unik'], 'tanggal' => $tanggal, 'rekening_id' => $m['kas']->id,
            'supplier_id' => $m['supplier']->id, 'keterangan' => 'Bayar gaji', 'total' => 200000,
            'pajak_id' => null, 'pajak_nominal' => 0, 'grand_total' => 200000, 'created_by' => $admin->id,
        ]);
        $kk->forceFill(['approval_status' => 'approved'])->save();
        KasKeluarItem::create(['kas_keluar_id' => $kk->id, 'akun_id' => $this->akun('521'), 'keterangan' => 'Gaji', 'nominal' => 200000]);

        $mb = MutasiBank::create([
            'nomor' => 'MB-'.$m['unik'], 'tanggal' => $tanggal,
            'rekening_asal_id' => $m['kas']->id, 'rekening_tujuan_id' => $m['bank']->id,
            'nominal' => 100000, 'keterangan' => 'Setor tunai',
        ]);

        $aset = Aset::create([
            'kode' => 'AST-'.$m['unik'], 'nama' => 'Komputer Tes', 'kategori' => 'Perangkat Komputer',
            'tanggal_perolehan' => now()->subMonths(2)->toDateString(), 'harga_perolehan' => 5000000,
            'nilai_residu' => 500000, 'masa_manfaat_bulan' => 36,
            'akun_aset_id' => $this->akun('121'), 'akun_akumulasi_id' => $this->akun('122'),
            'akun_beban_id' => $this->akun('521'), 'sumber_dana_id' => $m['kas']->id,
            'rekening_id' => $m['kas']->id, 'supplier_id' => $m['supplier']->id,
            'catat_perolehan' => false, 'status' => 'aktif',
        ]);

        $ps = PerubahanStok::create([
            'nomor' => 'PS-'.$m['unik'], 'tanggal' => $tanggal, 'jenis' => 'rusak',
            'keterangan' => 'Rusak', 'status' => 'posted', 'created_by' => $admin->id,
        ]);
        $ps->forceFill(['approval_status' => 'approved'])->save();
        PerubahanStokItem::create(['perubahan_stok_id' => $ps->id, 'barang_id' => $barang->id, 'arah' => 'keluar', 'jumlah' => 2, 'harga' => 3000, 'subtotal' => 6000]);

        $so = StokOpname::create([
            'nomor' => 'SO-'.$m['unik'], 'tanggal' => $tanggal, 'keterangan' => 'Opname',  'status' => 'posted',
            'created_by' => $admin->id,
        ]);
        $so->forceFill(['approval_status' => 'approved'])->save();
        StokOpnameItem::create(['stok_opname_id' => $so->id, 'barang_id' => $barang->id, 'stok_sistem' => 10, 'stok_fisik' => 12, 'selisih' => 2, 'harga' => 3000, 'subtotal' => 6000]);

        $gudangUtama = Gudang::utama();
        $tg = TransferGudang::create([
            'nomor' => 'TG-'.$m['unik'], 'tanggal' => $tanggal,
            'gudang_asal' => $gudangUtama->id, 'gudang_tujuan' => $m['gudangCabang']->id,
            'keterangan' => 'Pindah',  'status' => 'posted', 'created_by' => $admin->id,
        ]);
        TransferGudangItem::create(['transfer_gudang_id' => $tg->id, 'barang_id' => $barang->id, 'jumlah' => 3, 'keterangan' => null]);

        $jurnal = JurnalUmum::create([
            'nomor' => 'J-'.$m['unik'], 'tanggal' => $tanggal, 'keterangan' => 'Jurnal manual',
            'tipe' => 'manual', 'is_posted' => true, 'created_by' => $admin->id,
        ]);
        $jurnal->forceFill(['approval_status' => 'approved'])->save();
        JurnalItem::create(['jurnal_id' => $jurnal->id, 'akun_id' => $this->akun('111'), 'debit' => 100000, 'kredit' => 0, 'keterangan' => null]);
        JurnalItem::create(['jurnal_id' => $jurnal->id, 'akun_id' => $this->akun('411'), 'debit' => 0, 'kredit' => 100000, 'keterangan' => null]);

        // Pembelian kredit via aplikasi (sinkron Buku Besar & stok) agar halaman tagihan bisa diuji.
        $this->post(route('pembelian.store'), [
            'tanggal' => $tanggal, 'supplier_id' => $m['supplier']->id, 'metode_bayar' => 'kredit',
            'diskon' => 0, 'diskon_tipe' => 'nominal', 'sync_harga' => null, 'pajak_id' => null,
            'items' => [['barang_id' => $barang->id, 'jumlah' => 20, 'harga_satuan' => 3000, 'diskon' => 0]],
        ])->assertSessionHasNoErrors();
        $pembelian = Pembelian::orderByDesc('id')->firstOrFail();

        $this->post(route('penjualan.store'), [
            'tanggal' => $tanggal, 'customer_id' => $m['customer']->id, 'metode_bayar' => 'kredit',
            'diskon' => 0, 'diskon_tipe' => 'nominal', 'sync_harga' => null, 'pajak_id' => null,
            'items' => [['barang_id' => $barang->id, 'jumlah' => 6, 'harga_satuan' => $m['hargaJual'], 'diskon' => 0]],
        ])->assertSessionHasNoErrors();
        $penjualan = Penjualan::orderByDesc('id')->firstOrFail();

        $returBeli = ReturPembelian::create([
            'nomor' => 'RPB-'.$m['unik'], 'tanggal' => $tanggal, 'pembelian_id' => $pembelian->id,
            'keterangan' => 'Retur', 'status' => 'posted', 'created_by' => $admin->id,
        ]);
        $returBeli->forceFill(['approval_status' => 'approved'])->save();
        ReturPembelianItem::create(['retur_pembelian_id' => $returBeli->id, 'barang_id' => $barang->id, 'jumlah' => 1, 'harga_satuan' => 3000, 'subtotal' => 3000]);

        $returJual = ReturPenjualan::create([
            'nomor' => 'RPJ-'.$m['unik'], 'tanggal' => $tanggal, 'penjualan_id' => $penjualan->id,
            'keterangan' => 'Retur', 'status' => 'posted', 'created_by' => $admin->id,
        ]);
        $returJual->forceFill(['approval_status' => 'approved'])->save();
        ReturPenjualanItem::create(['retur_penjualan_id' => $returJual->id, 'barang_id' => $barang->id, 'jumlah' => 1, 'harga_satuan' => $m['hargaJual'], 'subtotal' => $m['hargaJual'], 'hpp' => 3000, 'hpp_total' => 3000]);

        $this->get(route('akun-perkiraan.edit', AkunPerkiraan::first()))->assertOk();
        $this->get(route('rekening.edit', $m['kas']))->assertOk();
        $this->get(route('supplier.edit', $m['supplier']))->assertOk();
        $this->get(route('customer.edit', $m['customer']))->assertOk();
        $this->get(route('barang.edit', $barang))->assertOk();
        $this->get(route('jasa.edit', $jasa))->assertOk();
        $this->get(route('pajak.edit', $pajak))->assertOk();
        $this->get(route('gudang.edit', $m['gudangCabang']))->assertOk();
        $this->get(route('daftar-harga.edit', $dh))->assertOk();
        $this->get(route('data-produk.edit', $barang))->assertOk();
        $this->get(route('data-nama.edit', ['customer', $m['customer']]))->assertOk();
        $this->get(route('data-nama.edit', ['supplier', $m['supplier']]))->assertOk();
        $this->get(route('aset.show', $aset))->assertOk();
        $this->get(route('aset.edit', $aset))->assertOk();

        $this->get(route('kas-masuk.show', $km))->assertOk();
        $this->get(route('kas-keluar.show', $kk))->assertOk();
        $this->get(route('mutasi-bank.show', $mb))->assertOk();
        $this->get(route('pembelian.show', $pembelian))->assertOk();
        $this->get(route('penjualan.show', $penjualan))->assertOk();
        $this->get(route('jurnal.show', $jurnal))->assertOk();
        $this->get(route('perubahan-stok.show', $ps))->assertOk();
        $this->get(route('stok-opname.show', $so))->assertOk();
        $this->get(route('transfer-gudang.show', $tg))->assertOk();
        $this->get(route('retur-pembelian.show', $returBeli))->assertOk();
        $this->get(route('retur-penjualan.show', $returJual))->assertOk();
        $this->get(route('tagihan.piutang.bayar', $m['customer']))->assertOk();
        $this->get(route('tagihan.hutang.bayar', $m['supplier']))->assertOk();

        $this->get(route('pembelian.pdf', $pembelian))->assertOk();
        $this->get(route('penjualan.pdf', $penjualan))->assertOk();
    }
}
