<?php

namespace Tests\Feature;

use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class KolomTabelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function login(): void
    {
        $this->actingAs(User::where('email', 'admin@keuangan.test')->firstOrFail());
    }

    private function kolomHeader(string $url): string
    {
        $html = $this->get($url)->getContent();

        return Str::between($html, '<thead class="bg-gray-50">', '</thead>');
    }

    public function test_data_nama_default_menampilkan_kolom_bawaan(): void
    {
        $this->login();

        $thead = $this->kolomHeader(route('data-nama.index', ['entitas' => 'customer']));

        $this->assertStringContainsString('Kode', $thead);
        $this->assertStringContainsString('Nama', $thead);
        $this->assertStringContainsString('Telepon', $thead);
        $this->assertStringContainsString('Saldo Piutang', $thead);
        $this->assertStringContainsString('Aksi', $thead);
        $this->assertStringNotContainsString('Alamat', $thead);

        $this->get(route('data-nama.index', ['entitas' => 'customer']))
            ->assertSee("detail: 'konfig-kolom'", false);
    }

    public function test_data_nama_simpan_kolom_mendapat_aksi_wajib(): void
    {
        $this->login();

        $this->post(route('data-nama.simpan-kolom'), ['kolom' => ['kode', 'nama']])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $prefs = json_decode((string) Pengaturan::tampil('data_nama_kolom'), true);
        $this->assertEqualsCanonicalizing(['kode', 'nama', 'aksi'], $prefs);

        $thead = $this->kolomHeader(route('data-nama.index', ['entitas' => 'customer']));
        $this->assertStringContainsString('Kode', $thead);
        $this->assertStringContainsString('Nama', $thead);
        $this->assertStringContainsString('Aksi', $thead);
        $this->assertStringNotContainsString('Telepon', $thead);
        $this->assertStringNotContainsString('Saldo Piutang', $thead);
    }

    public function test_gudang_simpan_kolom_mengabaikan_kunci_tak_dikenal(): void
    {
        $this->login();

        $this->post(route('gudang.simpan-kolom'), ['kolom' => ['kode', 'nama', 'hacker']])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $prefs = json_decode((string) Pengaturan::tampil('gudang_kolom'), true);
        $this->assertEqualsCanonicalizing(['kode', 'nama', 'aksi'], $prefs);

        $thead = $this->kolomHeader(route('gudang.index'));
        $this->assertStringContainsString('Kode', $thead);
        $this->assertStringContainsString('Nama', $thead);
        $this->assertStringContainsString('Aksi', $thead);
        $this->assertStringNotContainsString('Jenis Barang', $thead);
        $this->assertStringNotContainsString('Status', $thead);
        $this->assertStringNotContainsString('Alamat', $thead);
    }

    public function test_rekening_simpan_kolom_tersimpan(): void
    {
        $this->login();

        $this->post(route('rekening.simpan-kolom'), ['kolom' => ['jenis', 'nama', 'saldo']])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $thead = $this->kolomHeader(route('rekening.index'));
        $this->assertStringContainsString('Jenis', $thead);
        $this->assertStringContainsString('Nama', $thead);
        $this->assertStringContainsString('Saldo', $thead);
        $this->assertStringNotContainsString('No. Rekening / Pemilik', $thead);
    }

    public function test_pajak_simpan_kolom_tersimpan(): void
    {
        $this->login();

        $this->post(route('pajak.simpan-kolom'), ['kolom' => ['nama', 'status']])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $prefs = json_decode((string) Pengaturan::tampil('pajak_kolom'), true);
        $this->assertEqualsCanonicalizing(['nama', 'status', 'aksi'], $prefs);

        $thead = $this->kolomHeader(route('pajak.index'));
        $this->assertStringContainsString('Nama', $thead);
        $this->assertStringContainsString('Status', $thead);
        $this->assertStringNotContainsString('Rate', $thead);
    }

    public function test_daftar_harga_selalu_memaksa_kolom_barang_dan_aksi(): void
    {
        $this->login();

        $this->post(route('daftar-harga.simpan-kolom'), ['kolom' => ['tier', 'harga']])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $prefs = json_decode((string) Pengaturan::tampil('daftar_harga_kolom'), true);
        $this->assertContains('barang', $prefs);
        $this->assertContains('aksi', $prefs);
        $this->assertNotContains('status', $prefs);
    }

    public function test_daftar_harga_tidak_menerima_kolom_bukan_opsi(): void
    {
        $this->login();

        $this->post(route('daftar-harga.simpan-kolom'), ['kolom' => ['tier', 'bukan_ada', 'harga']])
            ->assertSessionHasNoErrors();

        $prefs = json_decode((string) Pengaturan::tampil('daftar_harga_kolom'), true);
        $this->assertNotContains('bukan_ada', $prefs);
        $this->assertEqualsCanonicalizing(['tier', 'harga', 'barang', 'aksi'], $prefs);
    }

    public function test_simpan_kolom_tanpa_pilihan_mendapat_wajib_saja(): void
    {
        $this->login();

        $this->post(route('gudang.simpan-kolom'), ['kolom' => []])
            ->assertSessionHasNoErrors();

        $prefs = json_decode((string) Pengaturan::tampil('gudang_kolom'), true);
        $this->assertEqualsCanonicalizing(['aksi'], $prefs);
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<int, string>, 4: array<int, string>, 5: string, 6: string}>
     */
    public static function kunciBaru(): array
    {
        return [
            'kas-masuk' => ['kas-masuk.index', 'kas-masuk.simpan-kolom', 'kas_masuk_kolom', ['nomor', 'keterangan'], ['nomor', 'keterangan', 'aksi'], 'Keterangan', 'Total'],
            'kas-keluar' => ['kas-keluar.index', 'kas-keluar.simpan-kolom', 'kas_keluar_kolom', ['nomor', 'keterangan'], ['nomor', 'keterangan', 'aksi'], 'Keterangan', 'Total'],
            'mutasi-bank' => ['mutasi-bank.index', 'mutasi-bank.simpan-kolom', 'mutasi_bank_kolom', ['nomor', 'nominal'], ['nomor', 'nominal', 'aksi'], 'Nominal', 'Transfer Dari'],
            'penjualan' => ['penjualan.index', 'penjualan.simpan-kolom', 'penjualan_kolom', ['nomor', 'total'], ['nomor', 'total', 'aksi'], 'Total', 'Bayar'],
            'pembelian' => ['pembelian.index', 'pembelian.simpan-kolom', 'pembelian_kolom', ['nomor', 'total'], ['nomor', 'total', 'aksi'], 'Total', 'Bayar'],
            'retur-penjualan' => ['retur-penjualan.index', 'retur-penjualan.simpan-kolom', 'retur_penjualan_kolom', ['nomor', 'nilai'], ['nomor', 'nilai', 'aksi'], 'Nilai', 'Pelanggan'],
            'retur-pembelian' => ['retur-pembelian.index', 'retur-pembelian.simpan-kolom', 'retur_pembelian_kolom', ['nomor', 'nilai'], ['nomor', 'nilai', 'aksi'], 'Nilai', 'Supplier'],
            'transfer-gudang' => ['transfer-gudang.index', 'transfer-gudang.simpan-kolom', 'transfer_gudang_kolom', ['nomor', 'qty'], ['nomor', 'qty', 'aksi'], 'Total Qty', 'Asal'],
            'perubahan-stok' => ['perubahan-stok.index', 'perubahan-stok.simpan-kolom', 'perubahan_stok_kolom', ['nomor', 'jenis'], ['nomor', 'jenis', 'aksi'], 'Jenis', 'Status'],
            'stok-opname' => ['stok-opname.index', 'stok-opname.simpan-kolom', 'stok_opname_kolom', ['nomor', 'selisih'], ['nomor', 'selisih', 'aksi'], 'Selisih Qty', 'Nilai'],
            'akun-perkiraan' => ['akun-perkiraan.index', 'akun-perkiraan.simpan-kolom', 'akun_perkiraan_kolom', ['kode', 'nama'], ['kode', 'nama', 'aksi'], 'Nama Akun', 'Saldo Normal'],
            'aset' => ['aset.index', 'aset.simpan-kolom', 'aset_kolom', ['kode', 'nama'], ['kode', 'nama', 'aksi'], 'Nama Aset', 'Harga Beli'],
            'jurnal' => ['jurnal.index', 'jurnal.simpan-kolom', 'jurnal_kolom', ['nomor', 'debit'], ['nomor', 'debit', 'aksi'], 'Debit', 'Kredit'],
            'buku-besar' => ['buku-besar.index', 'buku-besar.simpan-kolom', 'buku_besar_kolom', ['kode', 'saldo'], ['kode', 'saldo', 'detail'], 'Saldo', 'Nama Akun'],
            'bb-piutang' => ['bb-piutang.index', 'bb-piutang.simpan-kolom', 'bb_piutang_kolom', ['nama', 'saldo'], ['nama', 'saldo', 'detail'], 'Saldo', 'Debit'],
            'bb-hutang' => ['bb-hutang.index', 'bb-hutang.simpan-kolom', 'bb_hutang_kolom', ['nama', 'saldo'], ['nama', 'saldo', 'detail'], 'Saldo', 'Debit'],
            'bb-persediaan' => ['bb-persediaan.index', 'bb-persediaan.simpan-kolom', 'bb_persediaan_kolom', ['nama', 'saldo_qty'], ['nama', 'saldo_qty', 'detail'], 'Saldo Qty', 'Kode'],
            'periode' => ['periode.index', 'periode.simpan-kolom', 'periode_kolom', ['label', 'status'], ['label', 'status', 'aksi'], 'Periode', 'Dibuka'],
            'penyusutan' => ['penyusutan.index', 'penyusutan.simpan-kolom', 'penyusutan_kolom', ['kode', 'nama'], ['kode', 'nama'], 'Nama Aset', 'Beban Periode'],
            'tagihan-piutang' => ['tagihan.piutang', 'tagihan.piutang.simpan-kolom', 'tagihan_piutang_kolom', ['kode', 'sisa'], ['kode', 'sisa', 'aksi'], 'Sisa', 'Faktur Terbuka'],
            'tagihan-hutang' => ['tagihan.hutang', 'tagihan.hutang.simpan-kolom', 'tagihan_hutang_kolom', ['kode', 'sisa'], ['kode', 'sisa', 'aksi'], 'Sisa', 'Faktur Terbuka'],
        ];
    }

    /**
     * @param  array<int, string>  $subset
     * @param  array<int, string>  $final
     */
    #[DataProvider('kunciBaru')]
    public function test_kunci_baru_simpan_kolom_dan_menampilkan_subset(string $indexRoute, string $simpanRoute, string $kunci, array $subset, array $final, string $hadir, string $hilang): void
    {
        $this->login();

        $this->post(route($simpanRoute), ['kolom' => $subset])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $prefs = json_decode((string) Pengaturan::tampil($kunci), true);
        $this->assertEqualsCanonicalizing($final, $prefs);

        $thead = $this->kolomHeader(route($indexRoute));
        $this->assertStringContainsString($hadir, $thead);
        $this->assertStringNotContainsString($hilang, $thead);
    }

    #[DataProvider('kunciBaru')]
    public function test_kunci_baru_default_menampilkan_kolom_bawaan(string $indexRoute, string $simpanRoute, string $kunci, array $subset): void
    {
        $this->login();

        $this->get(route($indexRoute))->assertSee("detail: 'konfig-kolom'", false);
    }
}
