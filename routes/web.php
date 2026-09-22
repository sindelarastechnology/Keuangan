<?php

use App\Http\Controllers\Akuntansi\BbHutangController;
use App\Http\Controllers\Akuntansi\BbPersediaanController;
use App\Http\Controllers\Akuntansi\BbPiutangController;
use App\Http\Controllers\Akuntansi\BukuBesarController;
use App\Http\Controllers\Akuntansi\JurnalController;
use App\Http\Controllers\Akuntansi\PenyusutanController as AkuntansiPenyusutanController;
use App\Http\Controllers\Akuntansi\PeriodeController;
use App\Http\Controllers\Akuntansi\TutupBukuController;
use App\Http\Controllers\Akuntansi\TutupBukuTahunanController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\Laporan\ArusKasController;
use App\Http\Controllers\Laporan\LabaRugiController;
use App\Http\Controllers\Laporan\NeracaController;
use App\Http\Controllers\Laporan\NeracaSaldoController;
use App\Http\Controllers\Laporan\PenyusutanController as LaporanPenyusutanController;
use App\Http\Controllers\Master\AkunPerkiraanController;
use App\Http\Controllers\Master\AsetController;
use App\Http\Controllers\Master\BarangController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\DaftarHargaController;
use App\Http\Controllers\Master\DataNamaController;
use App\Http\Controllers\Master\DataProdukController;
use App\Http\Controllers\Master\GudangController;
use App\Http\Controllers\Master\JasaController;
use App\Http\Controllers\Master\PajakController;
use App\Http\Controllers\Master\RekeningController;
use App\Http\Controllers\Master\SupplierController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PengaturanSistemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Transaksi\KasKeluarController;
use App\Http\Controllers\Transaksi\KasMasukController;
use App\Http\Controllers\Transaksi\MutasiBankController;
use App\Http\Controllers\Transaksi\PembelianController;
use App\Http\Controllers\Transaksi\PenjualanController;
use App\Http\Controllers\Transaksi\PerubahanStokController;
use App\Http\Controllers\Transaksi\ReturPembelianController;
use App\Http\Controllers\Transaksi\ReturPenjualanController;
use App\Http\Controllers\Transaksi\StokOpnameController;
use App\Http\Controllers\Transaksi\TagihanController;
use App\Http\Controllers\Transaksi\TransferGudangController;
use App\Http\Controllers\UpgradeController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'akun-aktif'])->name('dashboard');

Route::middleware(['auth', 'verified', 'akun-aktif'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ===== Notifikasi (semua paket) =====
    Route::get('notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::get('notifikasi/unread-count', [NotifikasiController::class, 'unreadCount'])->name('notifikasi.unread-count');
    Route::get('notifikasi/recent', [NotifikasiController::class, 'recent'])->name('notifikasi.recent');
    Route::post('notifikasi/{notification}/baca', [NotifikasiController::class, 'baca'])->name('notifikasi.baca');
    Route::post('notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');

    // ===== Chat Komunitas & DM (semua paket) =====
    Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('chat/cari-user', [ChatController::class, 'cari'])->name('chat.cari');
    Route::get('chat/unread-count', [ChatController::class, 'unreadCount'])->name('chat.unread-count');
    Route::get('chat/{room}/pesan', [ChatController::class, 'pesan'])->name('chat.pesan');
    Route::post('chat/{room}/kirim', [ChatController::class, 'kirim'])->name('chat.kirim');
    Route::post('chat/dm/{user}', [ChatController::class, 'bukaDm'])->name('chat.dm.buka');
    Route::post('chat/pesan/{message}/laporkan', [ChatController::class, 'laporkan'])->name('chat.laporkan');

    // ===== Langganan =====
    Route::get('upgrade', [UpgradeController::class, 'index'])->name('upgrade.index');
    Route::post('upgrade/permintaan', [UpgradeController::class, 'kirimPermintaan'])->name('upgrade.request');

    // ===== Donasi (semua paket) =====
    Route::get('donasi', [DonasiController::class, 'index'])->name('donasi.index');
    Route::post('donasi', [DonasiController::class, 'store'])->name('donasi.store');

    // ===== Master =====
    Route::resource('akun-perkiraan', AkunPerkiraanController::class)->except('show')->names('akun-perkiraan');
    Route::post('akun-perkiraan/simpan-kolom', [AkunPerkiraanController::class, 'simpanKolom'])->name('akun-perkiraan.simpan-kolom');
    Route::resource('rekening', RekeningController::class)->except('show')->names('rekening');
    Route::post('rekening/simpan-kolom', [RekeningController::class, 'simpanKolom'])->name('rekening.simpan-kolom');
    Route::resource('supplier', SupplierController::class)->except('show')->names('supplier');
    Route::resource('customer', CustomerController::class)->except('show')->names('customer');
    Route::resource('barang', BarangController::class)->except('show')->names('barang');
    Route::post('barang/tambah-satuan', [BarangController::class, 'tambahSatuan'])->name('barang.tambah-satuan');
    Route::post('barang/tambah-kategori', [BarangController::class, 'tambahKategori'])->name('barang.tambah-kategori');
    Route::post('barang/simpan-kolom', [BarangController::class, 'simpanKolom'])->name('barang.simpan-kolom');
    Route::resource('jasa', JasaController::class)->except('show')->names('jasa');
    Route::middleware('plan.feature:daftar_harga')->group(function (): void {
        Route::resource('daftar-harga', DaftarHargaController::class)->except('show')->names('daftar-harga');
        Route::get('daftar-harga-riwayat', [DaftarHargaController::class, 'riwayat'])->name('daftar-harga.riwayat');
        Route::post('daftar-harga/simpan-kolom', [DaftarHargaController::class, 'simpanKolom'])->name('daftar-harga.simpan-kolom');
    });
    Route::resource('pajak', PajakController::class)->except('show')->names('pajak');
    Route::post('pajak/simpan-kolom', [PajakController::class, 'simpanKolom'])->name('pajak.simpan-kolom');
    Route::resource('gudang', GudangController::class)->except('show')->names('gudang');
    Route::post('gudang/simpan-kolom', [GudangController::class, 'simpanKolom'])->name('gudang.simpan-kolom');

    // ===== Data gabungan (menu baru) =====
    Route::resource('data-produk', DataProdukController::class)->except('show')->names('data-produk');
    Route::post('data-produk/tambah-satuan', [DataProdukController::class, 'tambahSatuan'])->name('data-produk.tambah-satuan');
    Route::post('data-produk/tambah-kategori', [DataProdukController::class, 'tambahKategori'])->name('data-produk.tambah-kategori');
    Route::post('data-produk/simpan-kolom', [DataProdukController::class, 'simpanKolom'])->name('data-produk.simpan-kolom');
    Route::get('data-nama', [DataNamaController::class, 'index'])->name('data-nama.index');
    Route::get('data-nama/create', [DataNamaController::class, 'create'])->name('data-nama.create');
    Route::post('data-nama', [DataNamaController::class, 'store'])->name('data-nama.store');
    Route::get('data-nama/{entitas}/{dataNama}/edit', [DataNamaController::class, 'edit'])->name('data-nama.edit');
    Route::put('data-nama/{entitas}/{dataNama}', [DataNamaController::class, 'update'])->name('data-nama.update');
    Route::delete('data-nama/{entitas}/{dataNama}', [DataNamaController::class, 'destroy'])->name('data-nama.destroy');
    Route::post('data-nama/simpan-kolom', [DataNamaController::class, 'simpanKolom'])->name('data-nama.simpan-kolom');

    // ===== Transaksi =====
    Route::resource('kas-masuk', KasMasukController::class)->except(['edit', 'update'])->names('kas-masuk');
    Route::post('kas-masuk/simpan-kolom', [KasMasukController::class, 'simpanKolom'])->name('kas-masuk.simpan-kolom');
    Route::resource('kas-keluar', KasKeluarController::class)->except(['edit', 'update'])->names('kas-keluar');
    Route::post('kas-keluar/simpan-kolom', [KasKeluarController::class, 'simpanKolom'])->name('kas-keluar.simpan-kolom');
    Route::resource('mutasi-bank', MutasiBankController::class)->except(['edit', 'update'])->names('mutasi-bank');
    Route::post('mutasi-bank/simpan-kolom', [MutasiBankController::class, 'simpanKolom'])->name('mutasi-bank.simpan-kolom');
    Route::resource('pembelian', PembelianController::class)->except(['edit', 'update', 'destroy'])->names('pembelian');
    Route::post('pembelian/simpan-kolom', [PembelianController::class, 'simpanKolom'])->name('pembelian.simpan-kolom');
    Route::resource('penjualan', PenjualanController::class)->except(['edit', 'update', 'destroy'])->names('penjualan');
    Route::post('penjualan/simpan-kolom', [PenjualanController::class, 'simpanKolom'])->name('penjualan.simpan-kolom');
    Route::get('pembelian/{pembelian}/edit', [PembelianController::class, 'edit'])->name('pembelian.edit');
    Route::put('pembelian/{pembelian}', [PembelianController::class, 'update'])->name('pembelian.update');
    Route::get('penjualan/{penjualan}/edit', [PenjualanController::class, 'edit'])->name('penjualan.edit');
    Route::put('penjualan/{penjualan}', [PenjualanController::class, 'update'])->name('penjualan.update');
    Route::get('pembelian/{pembelian}/pdf', [PembelianController::class, 'pdf'])->name('pembelian.pdf');
    Route::get('penjualan/{penjualan}/pdf', [PenjualanController::class, 'pdf'])->name('penjualan.pdf');
    Route::post('pembelian/{pembelian}/void', [PembelianController::class, 'void'])->name('pembelian.void');
    Route::post('pembelian/{pembelian}/post', [PembelianController::class, 'post'])->name('pembelian.post');
    Route::post('penjualan/{penjualan}/void', [PenjualanController::class, 'void'])->name('penjualan.void');
    Route::post('penjualan/{penjualan}/post', [PenjualanController::class, 'post'])->name('penjualan.post');
    Route::post('penjualan/{penjualan}/pelunasan', [PenjualanController::class, 'pelunasan'])->name('penjualan.pelunasan');
    Route::post('pembelian/{pembelian}/pelunasan', [PembelianController::class, 'pelunasan'])->name('pembelian.pelunasan');

    // ===== Tagihan (hub piutang/hutang) =====
    Route::get('tagihan/piutang', [TagihanController::class, 'piutang'])->name('tagihan.piutang');
    Route::get('tagihan/hutang', [TagihanController::class, 'hutang'])->name('tagihan.hutang');
    Route::get('tagihan/piutang/{customer}/bayar', [TagihanController::class, 'formBayarPiutang'])->name('tagihan.piutang.bayar');
    Route::post('tagihan/piutang/{customer}/bayar', [TagihanController::class, 'bayarPiutang'])->name('tagihan.piutang.bayar.submit');
    Route::get('tagihan/hutang/{supplier}/bayar', [TagihanController::class, 'formBayarHutang'])->name('tagihan.hutang.bayar');
    Route::post('tagihan/hutang/{supplier}/bayar', [TagihanController::class, 'bayarHutang'])->name('tagihan.hutang.bayar.submit');
    Route::post('tagihan/piutang/simpan-kolom', [TagihanController::class, 'simpanKolomPiutang'])->name('tagihan.piutang.simpan-kolom');
    Route::post('tagihan/hutang/simpan-kolom', [TagihanController::class, 'simpanKolomHutang'])->name('tagihan.hutang.simpan-kolom');

    // ===== Aset Tetap (Pro) =====
    Route::middleware('plan.feature:aset')->group(function (): void {
        Route::resource('aset', AsetController::class)->names('aset');
        Route::post('aset/simpan-kolom', [AsetController::class, 'simpanKolom'])->name('aset.simpan-kolom');
        Route::post('aset/{aset}/selesai', [AsetController::class, 'selesai'])->name('aset.selesai');
        Route::post('aset/{aset}/disposisi', [AsetController::class, 'disposisi'])->name('aset.disposisi');
    });

    // ===== Inventori (Pro untuk modul lanjutan) =====
    Route::middleware('plan.feature:opname')->group(function (): void {
        Route::resource('perubahan-stok', PerubahanStokController::class)->only(['index', 'create', 'store', 'show'])->names('perubahan-stok');
        Route::post('perubahan-stok/simpan-kolom', [PerubahanStokController::class, 'simpanKolom'])->name('perubahan-stok.simpan-kolom');
        Route::post('perubahan-stok/{perubahanStok}/void', [PerubahanStokController::class, 'void'])->name('perubahan-stok.void');
        Route::resource('stok-opname', StokOpnameController::class)->only(['index', 'create', 'store', 'show'])->names('stok-opname');
        Route::post('stok-opname/simpan-kolom', [StokOpnameController::class, 'simpanKolom'])->name('stok-opname.simpan-kolom');
        Route::post('stok-opname/{stokOpname}/void', [StokOpnameController::class, 'void'])->name('stok-opname.void');
    });
    Route::middleware('plan.feature:multi_gudang')->group(function (): void {
        Route::resource('transfer-gudang', TransferGudangController::class)->only(['index', 'create', 'store', 'show'])->names('transfer-gudang');
        Route::post('transfer-gudang/simpan-kolom', [TransferGudangController::class, 'simpanKolom'])->name('transfer-gudang.simpan-kolom');
    });
    Route::middleware('plan.feature:retur')->group(function (): void {
        Route::get('retur-penjualan/sumber/{penjualan}/items', [ReturPenjualanController::class, 'sumberItems'])->name('retur-penjualan.sumber-items');
        Route::get('retur-penjualan/cari', [ReturPenjualanController::class, 'search'])->name('retur-penjualan.search');
        Route::resource('retur-penjualan', ReturPenjualanController::class)->only(['index', 'create', 'store', 'show'])->names('retur-penjualan');
        Route::post('retur-penjualan/simpan-kolom', [ReturPenjualanController::class, 'simpanKolom'])->name('retur-penjualan.simpan-kolom');
        Route::post('retur-penjualan/{returPenjualan}/void', [ReturPenjualanController::class, 'void'])->name('retur-penjualan.void');
        Route::get('retur-pembelian/sumber/{pembelian}/items', [ReturPembelianController::class, 'sumberItems'])->name('retur-pembelian.sumber-items');
        Route::get('retur-pembelian/cari', [ReturPembelianController::class, 'search'])->name('retur-pembelian.search');
        Route::resource('retur-pembelian', ReturPembelianController::class)->only(['index', 'create', 'store', 'show'])->names('retur-pembelian');
        Route::post('retur-pembelian/simpan-kolom', [ReturPembelianController::class, 'simpanKolom'])->name('retur-pembelian.simpan-kolom');
        Route::post('retur-pembelian/{returPembelian}/void', [ReturPembelianController::class, 'void'])->name('retur-pembelian.void');
    });

    // ===== Akuntansi =====
    Route::get('jurnal', [JurnalController::class, 'index'])->name('jurnal.index');
    Route::post('jurnal/simpan-kolom', [JurnalController::class, 'simpanKolom'])->name('jurnal.simpan-kolom');
    Route::get('jurnal/{jurnal}', [JurnalController::class, 'show'])->name('jurnal.show');
    Route::get('jurnal-manual/create', [JurnalController::class, 'create'])->name('jurnal.create');
    Route::post('jurnal-manual', [JurnalController::class, 'store'])->name('jurnal.store');
    Route::post('jurnal/{jurnal}/void', [JurnalController::class, 'void'])->name('jurnal.void');
    Route::post('jurnal/{jurnal}/minta-approval', [JurnalController::class, 'requestApproval'])->name('jurnal.request-approval');
    Route::post('jurnal/{jurnal}/approve', [JurnalController::class, 'approve'])->name('jurnal.approve');
    Route::post('jurnal/{jurnal}/reject', [JurnalController::class, 'reject'])->name('jurnal.reject');
    Route::get('buku-besar', [BukuBesarController::class, 'index'])->name('buku-besar.index');
    Route::post('buku-besar/simpan-kolom', [BukuBesarController::class, 'simpanKolom'])->name('buku-besar.simpan-kolom');
    Route::middleware('plan.feature:bb_khusus')->group(function (): void {
        Route::get('bb-piutang', [BbPiutangController::class, 'index'])->name('bb-piutang.index');
        Route::post('bb-piutang/simpan-kolom', [BbPiutangController::class, 'simpanKolom'])->name('bb-piutang.simpan-kolom');
        Route::get('bb-hutang', [BbHutangController::class, 'index'])->name('bb-hutang.index');
        Route::post('bb-hutang/simpan-kolom', [BbHutangController::class, 'simpanKolom'])->name('bb-hutang.simpan-kolom');
        Route::get('bb-persediaan', [BbPersediaanController::class, 'index'])->name('bb-persediaan.index');
        Route::post('bb-persediaan/simpan-kolom', [BbPersediaanController::class, 'simpanKolom'])->name('bb-persediaan.simpan-kolom');
    });
    Route::get('periode', [PeriodeController::class, 'index'])->name('periode.index');
    Route::post('periode/simpan-kolom', [PeriodeController::class, 'simpanKolom'])->name('periode.simpan-kolom');
    Route::post('periode/{periode}/buka', [PeriodeController::class, 'buka'])->name('periode.buka');
    Route::post('periode/{periode}/kunci', [PeriodeController::class, 'kunci'])->name('periode.kunci');
    Route::post('periode/{periode}/buka-kunci', [PeriodeController::class, 'bukaKunci'])->name('periode.buka-kunci');
    Route::middleware('plan.feature:tutup_buku')->group(function (): void {
        Route::get('tutup-buku', [TutupBukuController::class, 'index'])->name('tutup-buku.index');
        Route::post('tutup-buku', [TutupBukuController::class, 'store'])->name('tutup-buku.store');
        Route::get('tutup-buku/tahunan', [TutupBukuTahunanController::class, 'index'])->name('tutup-buku-tahunan.index');
        Route::post('tutup-buku/tahunan', [TutupBukuTahunanController::class, 'store'])->name('tutup-buku-tahunan.store');
    });
    Route::middleware('plan.feature:aset')->group(function (): void {
        Route::get('penyusutan', [AkuntansiPenyusutanController::class, 'index'])->name('penyusutan.index');
        Route::post('penyusutan/simpan-kolom', [AkuntansiPenyusutanController::class, 'simpanKolom'])->name('penyusutan.simpan-kolom');
        Route::get('penyusutan/kalkulator', [AkuntansiPenyusutanController::class, 'kalkulator'])->name('penyusutan.kalkulator');
        Route::post('penyusutan/proses', [AkuntansiPenyusutanController::class, 'proses'])->name('penyusutan.proses');
        Route::post('penyusutan/batalkan', [AkuntansiPenyusutanController::class, 'batalkan'])->name('penyusutan.batalkan');
    });

    // ===== Laporan =====
    Route::get('laporan/laba-rugi', [LabaRugiController::class, 'index'])->name('laporan.laba-rugi');
    Route::get('laporan/neraca', [NeracaController::class, 'index'])->name('laporan.neraca');
    Route::get('laporan/arus-kas', [ArusKasController::class, 'index'])->name('laporan.arus-kas');
    Route::get('laporan/neraca-saldo', [NeracaSaldoController::class, 'index'])->name('laporan.neraca-saldo');
    Route::middleware('plan.feature:export')->group(function (): void {
        Route::get('laporan/laba-rugi/pdf', [LabaRugiController::class, 'pdf'])->name('laporan.laba-rugi.pdf');
        Route::get('laporan/laba-rugi/excel', [LabaRugiController::class, 'excel'])->name('laporan.laba-rugi.excel');
        Route::get('laporan/laba-rugi/csv', [LabaRugiController::class, 'csv'])->name('laporan.laba-rugi.csv');
        Route::get('laporan/neraca/pdf', [NeracaController::class, 'pdf'])->name('laporan.neraca.pdf');
        Route::get('laporan/neraca/excel', [NeracaController::class, 'excel'])->name('laporan.neraca.excel');
        Route::get('laporan/neraca/csv', [NeracaController::class, 'csv'])->name('laporan.neraca.csv');
        Route::get('laporan/arus-kas/pdf', [ArusKasController::class, 'pdf'])->name('laporan.arus-kas.pdf');
        Route::get('laporan/arus-kas/excel', [ArusKasController::class, 'excel'])->name('laporan.arus-kas.excel');
        Route::get('laporan/arus-kas/csv', [ArusKasController::class, 'csv'])->name('laporan.arus-kas.csv');
        Route::get('laporan/neraca-saldo/pdf', [NeracaSaldoController::class, 'pdf'])->name('laporan.neraca-saldo.pdf');
        Route::get('laporan/neraca-saldo/excel', [NeracaSaldoController::class, 'excel'])->name('laporan.neraca-saldo.excel');
        Route::get('laporan/neraca-saldo/csv', [NeracaSaldoController::class, 'csv'])->name('laporan.neraca-saldo.csv');
    });
    Route::middleware('plan.feature:aset')->group(function (): void {
        Route::get('laporan/penyusutan', [LaporanPenyusutanController::class, 'index'])->name('laporan.penyusutan');
        Route::get('laporan/penyusutan/pdf', [LaporanPenyusutanController::class, 'pdf'])->name('laporan.penyusutan.pdf');
        Route::get('laporan/penyusutan/excel', [LaporanPenyusutanController::class, 'excel'])->name('laporan.penyusutan.excel');
        Route::get('laporan/penyusutan/csv', [LaporanPenyusutanController::class, 'csv'])->name('laporan.penyusutan.csv');
    });

    // ===== Pengaturan =====
    Route::get('pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
    Route::get('pengaturan/sistem', [PengaturanSistemController::class, 'index'])->name('pengaturan.sistem.index');
    Route::post('pengaturan/sistem', [PengaturanSistemController::class, 'update'])->name('pengaturan.sistem.update');
    Route::post('pengaturan/satuan', [PengaturanController::class, 'satuanStore'])->name('pengaturan.satuan.store');
    Route::put('pengaturan/satuan/{satuan}', [PengaturanController::class, 'satuanUpdate'])->name('pengaturan.satuan.update');
    Route::delete('pengaturan/satuan/{satuan}', [PengaturanController::class, 'satuanDestroy'])->name('pengaturan.satuan.destroy');
    Route::post('pengaturan/kategori', [PengaturanController::class, 'kategoriStore'])->name('pengaturan.kategori.store');
    Route::put('pengaturan/kategori/{kategori}', [PengaturanController::class, 'kategoriUpdate'])->name('pengaturan.kategori.update');
    Route::delete('pengaturan/kategori/{kategori}', [PengaturanController::class, 'kategoriDestroy'])->name('pengaturan.kategori.destroy');
});

require __DIR__.'/auth.php';
