<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function login(): void
    {
        $this->actingAs(User::factory()->create());
    }

    public function test_semua_pengguna_melihat_semua_kategori(): void
    {
        $this->login();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('kas-masuk.index'))
            ->assertSee(route('aset.index'))
            ->assertSee(route('akun-perkiraan.index'))
            ->assertSee(route('jurnal.index'))
            ->assertSee(route('laporan.neraca'))
            ->assertSee(route('pengaturan.index'))
            ->assertSee(route('profile.edit'));
    }

    public function test_menu_baru_tampil_dan_legacy_disembunyikan(): void
    {
        $this->login();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('data-nama.index'))
            ->assertSee(route('data-produk.index'))
            ->assertSee(route('gudang.index'))
            ->assertSee(route('transfer-gudang.index'))
            ->assertSee(route('tutup-buku-tahunan.index'))
            ->assertSee('Data Nama')
            ->assertSee('Data Produk')
            ->assertSee('Data Gudang')
            ->assertSee('Transfer Barang Antar Gudang')
            ->assertSee('Tutup Buku Tahunan')
            ->assertDontSee(route('customer.index'))
            ->assertDontSee(route('supplier.index'))
            ->assertDontSee(route('jasa.index'));
    }

    public function test_kategori_satu_item_jadi_tautan_langsung(): void
    {
        $this->login();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('<a href="'.route('aset.index').'"', false)
            ->assertSee('<a href="'.route('dashboard').'"', false);
    }

    public function test_buku_besar_dan_variannya_berada_di_menu_laporan(): void
    {
        $this->login();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Buku Besar')
            ->assertSee('Buku Besar Umum')
            ->assertSee(route('buku-besar.index'))
            ->assertSee(route('bb-piutang.index'))
            ->assertSee(route('bb-hutang.index'))
            ->assertSee(route('bb-persediaan.index'))
            ->assertSee('Kalkulator Penyusutan');
    }

    public function test_drawer_mobile_dan_bottom_nav_ada(): void
    {
        $this->login();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('id="mobile-drawer"', false)
            ->assertSee('id="menu-overlay"', false)
            ->assertSee(route('profile.edit'));
    }

    public function test_komunitas_hilang_dan_dukung_kaspro_muncul_di_navbar(): void
    {
        $this->login();

        $response = $this->get(route('dashboard'));

        // Dukung KasPro kini tombol di navbar (href relatif), bukan lagi di sidebar/menu Komunitas.
        $response->assertOk()
            ->assertSee('Dukung KasPro')
            ->assertSee(str_replace(url('/'), '', route('donasi.index')));

        // Chat kini hanya lewat FAB (tepat 1 tautan), bukan lagi dari sidebar/menu Komunitas.
        $this->assertSame(1, substr_count($response->getContent(), '<a href="'.str_replace(url('/'), '', rtrim(route('chat.index'), '/')).'"'));
    }

    public function test_fab_chat_dan_badge_paket_tampil(): void
    {
        $this->login();

        $badgePro = '>Pro</span>';
        $badgeGratis = '>Gratis</span>';

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Chat Komunitas')
            ->assertSee('chatFab')
            ->assertSee($badgePro, false)
            ->assertDontSee($badgeGratis, false);
    }

    public function test_fab_chat_sembunyi_di_halaman_chat(): void
    {
        $this->login();

        $this->get(route('chat.index'))
            ->assertOk()
            ->assertDontSee('chatFab');
    }

    public function test_ticker_percobaan_tampil_di_navbar(): void
    {
        $this->login();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Sistem ini masih dalam masa percobaan.')
            ->assertSee('Mohon jangan memasukkan data penting Anda.')
            ->assertSee('animate-marquee')
            ->assertSee('role="region"', false);
    }

    public function test_ticker_sembunyi_ketika_daftar_pesan_kosong(): void
    {
        config(['pengumuman.pesan' => []]);
        $this->login();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('animate-marquee');
    }

    public function test_notifikasi_menggunakan_dropdown_di_navbar(): void
    {
        $this->login();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('notifBell')
            ->assertSee('Tandai semua dibaca')
            ->assertSee('Lihat semua');
    }
}
