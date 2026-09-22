<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\Aset;
use App\Models\JurnalUmum;
use App\Models\Penyusutan;
use App\Models\PeriodeAkuntansi;
use App\Models\Rekening;
use App\Models\Supplier;
use App\Models\User;
use App\Services\ProvisionTenant;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsetPenyusutanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function login(): User
    {
        $user = User::where('email', 'admin@keuangan.test')->firstOrFail();
        $this->actingAs($user);

        return $user;
    }

    private function akun(string $kode): AkunPerkiraan
    {
        return AkunPerkiraan::where('kode', $kode)->firstOrFail();
    }

    private function bulanMulaiPenyusutan(): string
    {
        return now()->format('Y-m');
    }

    private function bulanPerolehan(): string
    {
        return now()->startOfMonth()->subMonth()->format('Y-m');
    }

    private function buatAsetGarisLurus(int $harga, int $masa, string $tanggalPerolehan): Aset
    {
        return Aset::factory()->garisLurus($harga, $masa, $tanggalPerolehan)->aktif()->create();
    }

    public function test_membuat_aset_baru_menghasilkan_kode_dan_mulai_penyusutan_bulan_berikutnya(): void
    {
        $this->login();
        $akunAset = $this->akun('121');
        $tanggal = now()->toDateString();

        $response = $this->post(route('aset.store'), [
            'nama' => 'Mesin Kayu',
            'kategori' => 'Peralatan',
            'tanggal_perolehan' => $tanggal,
            'harga_perolehan' => 12000000,
            'nilai_residu' => 0,
            'masa_manfaat_bulan' => 60,
            'akun_aset_id' => $akunAset->id,
            'akun_akumulasi_id' => $this->akun('121')->id,
            'akun_beban_id' => $this->akun('526')->id,
            'status' => 'aktif',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $aset = Aset::firstOrFail();
        $this->assertMatchesRegularExpression('#^AS-\d{4}$#', $aset->kode);
        $this->assertEquals(Carbon::parse($tanggal)->addMonth()->format('Y-m'), $aset->mulai_periode);
    }

    public function test_perolehan_aset_dicatat_otomatis_ke_jurnal_saat_rekening_dipilih(): void
    {
        $this->login();
        $akunAset = $this->akun('121');
        $rekening = Rekening::factory()->kas()->create(['akun_id' => $this->akun('111')->id]);

        $response = $this->post(route('aset.store'), [
            'nama' => 'Kendaraan Operasional',
            'tanggal_perolehan' => now()->toDateString(),
            'harga_perolehan' => 12000000,
            'nilai_residu' => 1000000,
            'masa_manfaat_bulan' => 60,
            'sumber_dana' => 'rekening:'.$rekening->id,
            'catat_perolehan' => 1,
            'status' => 'aktif',
        ]);

        $response->assertRedirect();

        $aset = Aset::firstOrFail();
        $jurnal = JurnalUmum::where('tipe', 'perolehan_aset')->firstOrFail();

        $this->assertSame(Aset::class, $jurnal->ref_type);
        $this->assertEquals($aset->id, $jurnal->ref_id);
        $this->assertEquals($rekening->id, $aset->rekening_id);
        $this->assertTrue($jurnal->items->contains(fn ($item) => $item->akun_id === $akunAset->id && (float) $item->debit === 12000000.0));
        $this->assertTrue($jurnal->items->contains(fn ($item) => $item->akun_id === $this->akun('111')->id && (float) $item->kredit === 12000000.0));
    }

    public function test_penyusutan_garis_lurus_dimulai_bulan_setelah_perolehan_dan_tidak_dobel(): void
    {
        $this->login();
        $aset = $this->buatAsetGarisLurus(300000, 3, now()->startOfMonth()->subMonth()->format('Y-m-d'));

        $this->post(route('penyusutan.proses'), ['periode' => $this->bulanPerolehan()])
            ->assertSessionHas('info');
        $this->assertSame(0, Penyusutan::count());

        $response = $this->post(route('penyusutan.proses'), ['periode' => $this->bulanMulaiPenyusutan()]);
        $response->assertSessionHas('success');

        $this->assertSame(1, Penyusutan::count());
        $catatan = Penyusutan::firstOrFail();
        $this->assertEquals(100000.0, (float) $catatan->beban);
        $this->assertEquals(100000.0, (float) $catatan->akumulasi_setelah);
        $this->assertEquals(200000.0, (float) $catatan->nilai_buku_setelah);
        $this->assertNotNull($catatan->jurnal_id);

        $jurnal = JurnalUmum::where('tipe', 'penyusutan')->firstOrFail();
        $this->assertTrue($jurnal->items->contains(fn ($item) => $item->akun_id === $aset->akun_beban_id && (float) $item->debit === 100000.0));
        $this->assertTrue($jurnal->items->contains(fn ($item) => $item->akun_id === $aset->akun_akumulasi_id && (float) $item->kredit === 100000.0));

        $this->post(route('penyusutan.proses'), ['periode' => $this->bulanMulaiPenyusutan()])
            ->assertSessionHas('info');
        $this->assertSame(1, Penyusutan::count());
    }

    public function test_penyusutan_berhenti_saat_nilai_aset_tuntas(): void
    {
        $this->login();
        $this->buatAsetGarisLurus(300000, 3, now()->startOfMonth()->subMonth()->format('Y-m-d'));

        foreach ([0, 1, 2] as $bulanKe) {
            $this->post(route('penyusutan.proses'), ['periode' => now()->addMonths($bulanKe)->format('Y-m')])
                ->assertSessionHas('success');
        }

        $this->assertSame(3, Penyusutan::count());
        $this->assertEquals(0.0, (float) Penyusutan::latest('id')->firstOrFail()->nilai_buku_setelah);

        $this->post(route('penyusutan.proses'), ['periode' => now()->addMonths(3)->format('Y-m')])
            ->assertSessionHas('info');
        $this->assertSame(3, Penyusutan::count());
    }

    public function test_membatalkan_penyusutan_membalik_jurnal_dan_menghapus_riwayat(): void
    {
        $this->login();
        $this->buatAsetGarisLurus(300000, 3, now()->startOfMonth()->subMonth()->format('Y-m-d'));
        $periode = $this->bulanMulaiPenyusutan();

        $this->post(route('penyusutan.proses'), ['periode' => $periode])->assertSessionHas('success');
        $this->assertSame(1, Penyusutan::count());

        $this->post(route('penyusutan.batalkan'), ['periode' => $periode])->assertSessionHas('success');

        $this->assertSame(0, Penyusutan::count());
        $jurnalPenyusutan = JurnalUmum::where('tipe', 'penyusutan')->get();
        $this->assertCount(2, $jurnalPenyusutan);
        $this->assertSame(1, $jurnalPenyusutan->filter(fn ($j) => str_starts_with($j->keterangan ?? '', '[DIBATALKAN]'))->count());
        $this->assertTrue($jurnalPenyusutan->contains(fn ($j) => str_contains($j->keterangan ?? '', 'BALIK:') || str_contains($j->keterangan ?? '', 'Batalkan')));

        $this->post(route('penyusutan.proses'), ['periode' => $periode])->assertSessionHas('success');
        $this->assertSame(1, Penyusutan::count());
    }

    public function test_penyusutan_ditolak_saat_periode_dikunci(): void
    {
        $this->login();
        $this->buatAsetGarisLurus(300000, 3, now()->startOfMonth()->subMonth()->format('Y-m-d'));

        $periode = PeriodeAkuntansi::where('is_open', true)->firstOrFail();
        $periode->update(['is_locked' => true]);

        $this->post(route('penyusutan.proses'), ['periode' => $periode->tahun.'-'.$periode->bulan])
            ->assertSessionHas('error');

        $this->assertSame(0, Penyusutan::count());
    }

    public function test_aset_nonaktif_tidak_diproses_penyusutan(): void
    {
        $this->login();
        Aset::factory()->garisLurus(300000, 3, now()->startOfMonth()->subMonth()->format('Y-m-d'))
            ->nonaktif()->create();

        $this->post(route('penyusutan.proses'), ['periode' => $this->bulanMulaiPenyusutan()])
            ->assertSessionHas('info');

        $this->assertSame(0, Penyusutan::count());
    }

    public function test_arus_kas_menyertakan_penyusutan_sebagai_penyesuaian_non_kas(): void
    {
        $this->login();
        $this->buatAsetGarisLurus(300000, 3, now()->startOfMonth()->subMonth()->format('Y-m-d'));

        $this->post(route('penyusutan.proses'), ['periode' => $this->bulanMulaiPenyusutan()])
            ->assertSessionHas('success');

        $data = ReportService::arusKas(now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString());
        $this->assertEquals(100000.0, (float) $data['penyusutan']);
    }

    public function test_laporan_penyusutan_menampilkan_akumulasi_hingga_tanggal_tertentu(): void
    {
        $this->login();
        $aset = $this->buatAsetGarisLurus(300000, 3, now()->startOfMonth()->subMonth()->format('Y-m-d'));

        $this->post(route('penyusutan.proses'), ['periode' => $this->bulanMulaiPenyusutan()]);

        $response = $this->get(route('laporan.penyusutan', ['sampai' => now()->toDateString()]));
        $response->assertOk();
        $response->assertSee($aset->kode);

        $data = ReportService::penyusutan(now()->toDateString());
        $this->assertEquals(100000.0, (float) $data['total_akumulasi']);
        $this->assertEquals(200000.0, (float) $data['total_nilai_buku']);
    }

    public function test_pengguna_terdaftar_dapat_mengelola_aset_dan_penyusutan_penuh(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('aset.create'))->assertOk();
        $this->get(route('aset.index'))->assertOk();
        $this->get(route('penyusutan.index'))->assertOk();
        $this->get(route('laporan.penyusutan'))->assertOk();
    }

    public function test_aset_memakai_akun_default_dari_template_saat_akun_tidak_diisi(): void
    {
        $user = User::factory()->create();
        ProvisionTenant::provision($user);
        $this->actingAs($user);

        $this->post(route('aset.store'), [
            'nama' => 'Laptop Kantor',
            'kategori' => 'Laptop/Komputer',
            'tanggal_perolehan' => now()->toDateString(),
            'harga_perolehan' => 12000000,
            'masa_manfaat_bulan' => 48,
            'status' => 'aktif',
        ])->assertSessionHas('success');

        $aset = Aset::firstOrFail();

        $this->assertEquals($this->akun('121')->id, $aset->akun_aset_id);
        $this->assertEquals($this->akun('122')->id, $aset->akun_akumulasi_id);
        $this->assertEquals($this->akun('526')->id, $aset->akun_beban_id);
    }

    public function test_pembelian_aset_tanpa_pembayaran_tidak_membuat_jurnal(): void
    {
        $this->login();

        $this->post(route('aset.store'), [
            'nama' => 'Meja Rapat',
            'kategori' => 'Peralatan Kantor',
            'tanggal_perolehan' => now()->toDateString(),
            'harga_perolehan' => 5000000,
            'masa_manfaat_bulan' => 60,
            'status' => 'aktif',
        ])->assertSessionHas('success');

        $aset = Aset::firstOrFail();
        $this->assertFalse((bool) $aset->catat_perolehan);
        $this->assertNull($aset->sumber_dana_id);
        $this->assertSame(0, JurnalUmum::count());
    }

    public function test_pembelian_aset_utang_wajib_memilih_supplier_dan_mencatat_utang(): void
    {
        $this->login();

        $this->post(route('aset.store'), [
            'nama' => 'Mesin Produksi',
            'kategori' => 'Mesin Produksi',
            'tanggal_perolehan' => now()->toDateString(),
            'harga_perolehan' => 20000000,
            'masa_manfaat_bulan' => 60,
            'catat_perolehan' => 1,
            'status' => 'aktif',
        ])->assertSessionHas('error');

        $supplier = Supplier::factory()->create();
        $this->post(route('aset.store'), [
            'nama' => 'Mesin Produksi',
            'kategori' => 'Mesin Produksi',
            'tanggal_perolehan' => now()->toDateString(),
            'harga_perolehan' => 20000000,
            'masa_manfaat_bulan' => 60,
            'catat_perolehan' => 1,
            'supplier_id' => $supplier->id,
            'status' => 'aktif',
        ])->assertSessionHas('success');

        $aset = Aset::firstOrFail();
        $this->assertEquals($supplier->id, $aset->supplier_id);
        $this->assertEquals($this->akun('211')->id, $aset->sumber_dana_id);

        $jurnal = JurnalUmum::where('tipe', 'perolehan_aset')->firstOrFail();
        $this->assertTrue($jurnal->items->contains(fn ($item) => $item->akun_id === $this->akun('211')->id && (float) $item->kredit === 20000000.0));
    }

    public function test_pembelian_aset_dengan_rekening_mencatat_jurnal_kas_bank(): void
    {
        $this->login();
        $rekening = Rekening::factory()->kas()->create(['akun_id' => $this->akun('111')->id]);

        $this->post(route('aset.store'), [
            'nama' => 'Kendaraan Operasional',
            'kategori' => 'Kendaraan',
            'tanggal_perolehan' => now()->toDateString(),
            'harga_perolehan' => 15000000,
            'masa_manfaat_bulan' => 96,
            'sumber_dana' => 'rekening:'.$rekening->id,
            'catat_perolehan' => 1,
            'status' => 'aktif',
        ])->assertSessionHas('success');

        $aset = Aset::firstOrFail();
        $this->assertEquals($rekening->id, $aset->rekening_id);
        $this->assertEquals($this->akun('111')->id, $aset->sumber_dana_id);
        $this->assertNull($aset->supplier_id);

        $jurnal = JurnalUmum::where('tipe', 'perolehan_aset')->firstOrFail();
        $this->assertTrue($jurnal->items->contains(fn ($item) => $item->akun_id === $this->akun('111')->id && (float) $item->kredit === 15000000.0));
    }

    public function test_tandai_selesai_menghentikan_penyusutan(): void
    {
        $this->login();
        $aset = $this->buatAsetGarisLurus(300000, 3, now()->startOfMonth()->subMonth()->format('Y-m-d'));

        $this->post(route('aset.selesai', $aset))->assertSessionHas('success');
        $this->assertEquals('selesai', $aset->fresh()->status);

        $this->post(route('penyusutan.proses'), ['periode' => $this->bulanMulaiPenyusutan()])
            ->assertSessionHas('info');

        $this->assertSame(0, Penyusutan::count());
    }

    public function test_penghapusan_aset_dijual_membuat_jurnal_dan_mencatat_laba(): void
    {
        $this->login();
        $aset = $this->buatAsetGarisLurus(300000, 3, now()->startOfMonth()->subMonth()->format('Y-m-d'));
        $this->post(route('penyusutan.proses'), ['periode' => $this->bulanMulaiPenyusutan()])
            ->assertSessionHas('success');

        $this->post(route('aset.disposisi', $aset), [
            'alasan' => 'dijual',
            'harga_jual' => 250000,
        ])->assertSessionHas('success');

        $aset->refresh();
        $this->assertEquals('nonaktif', $aset->status);
        $this->assertEquals('dijual', $aset->disposisi_alasan);
        $this->assertEquals(250000.0, (float) $aset->disposisi_harga_jual);
        $this->assertEquals(50000.0, (float) $aset->disposisi_laba);

        $jurnal = JurnalUmum::where('tipe', 'penghapusan_aset')->firstOrFail();
        $this->assertTrue($jurnal->items->contains(fn ($item) => $item->akun_id === $aset->akun_akumulasi_id && (float) $item->debit === 100000.0));
        $this->assertTrue($jurnal->items->contains(fn ($item) => $item->akun_id === $this->akun('111')->id && (float) $item->debit === 250000.0));
        $this->assertTrue($jurnal->items->contains(fn ($item) => $item->akun_id === $aset->akun_aset_id && (float) $item->kredit === 300000.0));
        $this->assertTrue($jurnal->items->contains(fn ($item) => $item->akun_id === $this->akun('421')->id && (float) $item->kredit === 50000.0));

        $this->post(route('penyusutan.proses'), ['periode' => now()->addMonth()->format('Y-m')])
            ->assertSessionHas('info');
        $this->assertSame(1, Penyusutan::count());
    }

    public function test_penghapusan_aset_dijual_mencatat_hasil_ke_akun_bank_anak_sumber_dana(): void
    {
        $this->login();
        $akunBankAnak = AkunPerkiraan::create([
            'kode' => '1121',
            'nama' => 'Bank Anak',
            'jenis' => 'aset',
            'saldo_normal' => 'debit',
            'is_header' => false,
            'parent_id' => $this->akun('11')->id,
        ]);
        $aset = Aset::factory()->garisLurus(300000, 3, now()->startOfMonth()->subMonth()->format('Y-m-d'))
            ->aktif()
            ->create(['sumber_dana_id' => $akunBankAnak->id]);

        $this->post(route('aset.disposisi', $aset), [
            'alasan' => 'dijual',
            'harga_jual' => 250000,
        ])->assertSessionHas('success');

        $jurnal = JurnalUmum::where('tipe', 'penghapusan_aset')->firstOrFail();
        $this->assertTrue($jurnal->items->contains(fn ($item) => $item->akun_id === $akunBankAnak->id && (float) $item->debit === 250000.0));
        $this->assertFalse($jurnal->items->contains(fn ($item) => $item->akun_id === $this->akun('111')->id));
    }

    public function test_penghapusan_aset_rusak_dicatat_sebagai_rugi(): void
    {
        $this->login();
        $aset = $this->buatAsetGarisLurus(300000, 3, now()->startOfMonth()->subMonth()->format('Y-m-d'));
        $this->post(route('penyusutan.proses'), ['periode' => $this->bulanMulaiPenyusutan()])
            ->assertSessionHas('success');

        $this->post(route('aset.disposisi', $aset), ['alasan' => 'rusak'])
            ->assertSessionHas('success');

        $aset->refresh();
        $this->assertEquals('nonaktif', $aset->status);
        $this->assertEquals('rusak', $aset->disposisi_alasan);

        $jurnal = JurnalUmum::where('tipe', 'penghapusan_aset')->firstOrFail();
        $this->assertTrue($jurnal->items->contains(fn ($item) => $item->akun_id === $this->akun('531')->id && (float) $item->debit === 200000.0));
    }

    public function test_harga_jual_wajib_diisi_saat_aset_dijual(): void
    {
        $this->login();
        $aset = $this->buatAsetGarisLurus(300000, 3, now()->startOfMonth()->subMonth()->format('Y-m-d'));

        $this->post(route('aset.disposisi', $aset), ['alasan' => 'dijual'])
            ->assertSessionHasErrors('harga_jual');

        $this->assertEquals('aktif', $aset->fresh()->status);
        $this->assertSame(0, JurnalUmum::where('tipe', 'penghapusan_aset')->count());
    }
}
