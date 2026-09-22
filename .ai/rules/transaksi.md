---
paths:
  - 'app/Http/Controllers/Transaksi/Retur*Controller.php'
  - 'app/Http/Controllers/Transaksi/*Controller.php'
  - app/Http/Controllers/Transaksi/MutasiBankController.php
  - 'app/Http/Controllers/Transaksi/**'
  - 'app/Http/Controllers/Transaksi/{Pembelian,Penjualan}Controller.php'
  - app/Http/Controllers/Transaksi/KasMasukController.php
  - app/Http/Controllers/Transaksi/KasKeluarController.php
  - app/Http/Controllers/Transaksi/PenjualanController.php
---

# Transaksi

## Retur nilai neto + PPN proporsional
Retur penjualan wajib memakai harga neto per unit = (subtotal_item/jumlah) * ((subtotal - diskon_nominal)/subtotal), bukan harga_satuan — ini proporsi nilai 411 yang benar. Jurnal retur juga (debit) membalik PPN Keluaran (212) proporsional dan piutang/rekening dikredit sebesar neto+pajak; retur pembelian membalik Cr 115 neto, Cr PPN Masukan (213), Dr utang/rekening neto+pajak. BB piutang/hutang ikut nilai total termasuk pajak. Void harus membalik nilai yang sama (neto+pajak).

## Syarat returabilitas + badge retur pada transaksi
Returabilitas transaksi: hanya status 'posted' dan masih ada barang (tipe=barang) yang sisanya belum diretur (>0). Retur dihitung per barang (jumlah - sisaQtyRetur); transaksi boleh diretur bertahap. Endpoint search pada form retur otomatis menyembunyikan transaksi yang SEMUA barangnya sudah habis diretur. Transaksi asli tidak berubah status (tetap posted); daftar index penjualan/pembelian menampilkan badge 'Diretur ×<jumlah retur posted>' via relasi hasMany retur() yang di-eager-load dengan filter status posted.

## Void transaksi diblokir saat ada retur aktif
Penjualan/Pembelian void() diblokir bila transaksi masih memiliki retur berstatus 'posted' (mencegah double-count stok & retur jurnal yg menggantung). ReturPenjualan/ReturPembelian store() menolak transaksi sumber yg berstatus 'draft' (sudah dibatalkan). Urutan batal yg benar: void retur terlebih dahulu, baru void transaksi induk.

## Cek saldo sebelum DB::beginTransaction di Mutasi Bank
Cek saldo (rekening asal) di MutasiBankController::store DILAKUKAN SEBELUM DB::beginTransaction(). Jangan begin transaksi lalu return back() lebih awal tanpa commit/rollback — itu hanya menaikkan level transaksi Laravel dan lama-kelamaan memicu 'There is already an active transaction' (dan membocorkan transaksi).

## Ongkir: total termasuk ongkir, jurnal ke akun 524
Kolom `ongkir` (decimal, default 0) di pembelians & penjualans sudah ada. `total` = dasarPajak + pajak + ongkir. Jurnal: Pembelian debit Beban Transportasi (524); Penjualan kredit 524 (mengurangi beban kirim). Void/BbHutang/BbPiutang otomatis konsisten karena memakai `total`. Ongkir tidak habis di-retur.

## Satu rekanan per transaksi + rekanan default UMUM
Form pembelian/penjualan: keranjang wajib kosong di awal, item hanya dari galeri (satu baris per barang_id, merge by barang_id saja, bukan barang+sumber). Supplier/customer default di-preselect ke rekanan kode 'UMUM' (migrasi 2026_09_08_041508; dapat diubah). Jika rekanan diganti, seluruh harga item di-derive ulang dari daftar harga rekanan baru (gantiSupplier/gantiPembeli), tidak menyisakan 'fixed' sumber. BarangController menyinkronkan harga_beli → DaftarHarga supplier UMUM dan harga_jual → customer UMUM via DaftarHarga::sinkronBeli/sinkronJual(qty=1).

## Status draft penuh transaksi = pending
Transaksi punya 3 status: 'posted' (aktif/selesai), 'pending' (draft penuh — disimpan tanpa stok/jurnal, bisa di-post ulang via route {transaksi}/post), 'draft' (dibatalkan/void). Form create kirim aksi=posted|pending. Bila disimpan pending, header sync_harga ikut disimpan agar posting ulang tahu perlu sinkron DaftarHarga. Void cuma untuk posted; pending dibatalkan dengan void() (→ draft).

## Atribusi pelunasan piutang per faktur via PiutangAttributionService
Kas Masuk pelunasan (akun 113) harus memakai PiutangAttributionService::alokasikan (FIFO ke faktur pelunasan belum lunas; sisa tak teralokasi → baris penjualan_id null). Void di kas-masuk.destroy memakai ::batalkan berbasis jurnal agar sisa faktur kembali penuh — jangan tulis bb_piutang manual di controller ini.

## Atribusi pelunasan hutang per faktur via HutangAttributionService
Pembayaran hutang (kas keluar akun 211) wajib memakai HutangAttributionService::alokasikan (FIFO ke faktur belum lunas; sisa tak teralokasi → baris pembelian_id null). Void di kas-keluar.destroy memakai ::batalkan berbasis jurnal agar sisa faktur kembali penuh — jangan tulis bb_hutang manual. Sisa per faktur = Σ(kredit-debit) baris bb_hutang yang ter-atribusi (Pembelian->sisaHutang).

## Semua pelunasan lewat PelunasanService
Pelunasan piutang/hutang (tombol pada detail transaksi, menu Tagihan, shortcut baris di daftar penjualan/pembelian) WAJIB memakai PelunasanService::bayarPiutang/bayarHutang agar akun (113/211), jurnal, dan alokasi FIFO konsisten. Cap nominal (tidak melebihi sisa rekanan) di controller pemanggil; service mencatat apa adanya. Jangan menulis KasMasuk/KasKeluar + bb_piutang/bb_hutang manual untuk pelunasan.

## Kategori Kas Keluar berbasis role akun penting
Daftar kategori `kategoriList()` di KasKeluarController memakai `role` dari PengaturanSistemService::katalogAkunPenting(), bukan kode akun hardcoded. Kategori gaji→`beban_gaji` (521, fallback 52/531), operasional→`beban_operasional` (523, fallback 52/531), sewa→`beban_sewa` (522, fallback 52/531). Tanpa role=kode hardcoded! Akun debit di-resolve via PengaturanSistemService::akunId saat create, lalu akun_id dikirim per item ke store.

## Void kredit: reversal per-baris bb piutang/hutang
Void penjualan/pembelian kredit: reversal bb_piutang/bb_hutang HARUS per baris untuk SEMUA baris yang terhubung via penjualan_id/pembelian_id (baris awal + alokasi pembayaran), jangan satu baris total. Saldo berjalan: piutang = lastSaldo + kredit - debit; hutang = lastSaldo + debit - kredit.

## KasMasuk pelunasan piutang: wajib customer + cap sisa
Item dengan akun piutang diwajibkan memilih customer_id (RuntimeException 'Item pelunasan piutang memerlukan customer') dan totalnya diblokir bila > Customer::saldoPiutang + 0.005 (pesan menyertakan formatRupiah sisa). Validasi di dalam DB::beginTransaction/try agar gagal → back()->with('error') dan rollback.

## KasKeluar pembayaran hutang: wajib supplier + cap sisa
Simetris dengan KasMasuk: item akun utang wajib supplier_id dan total > Supplier::saldoHutang + 0.005 diblokir. Validasi WAJIB berada dalam DB::beginTransaction/try (bukan sebelum try) agar error ter-flash ke back() alih-alih jadi uncaught RuntimeException/500.

## Void penjualan kredit berbayar diblokir
Void penjualan kredit ditolak bila ada BbPiutang::where('penjualan_id')->where('kredit','>',0.005) (baris pembayaran) dengan pesan 'Penjualan tidak dapat dibatalkan karena sudah ada pelunasan (Kas Masuk)'. Pembatalan penuh dilakukan via void kas masuk pelunasan dulu. Hanya berlaku di cabang posted (bukan draft/pending).
