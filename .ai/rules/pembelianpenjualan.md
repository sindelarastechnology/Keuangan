---
paths:
  - 'resources/views/transaksi/{pembelian,penjualan}/create.blade.php'
---

# Pembelianpenjualan

## Layout POS kasir untuk form create pembelian & penjualan
Form create PB/PJ memakai layout POS: galeri item lebar (lg:col-span-2, grid 2/3/4 kolom, kartu vertikal menampilkan stok & sumber harga) di kiri, keranjang sempit sticky (lg:col-span-1, baris bertingkat: select barang → select 'Sumber Harga' → stepper qty + harga + diskon item + subtotal). Header kartu atas berisi Tanggal/Supplier-Customer/Metode/Rekening/Pajak/Diskon Global/Ongkir. Helpers Alpine: jumlahQty(), infoBarang(), naikQty() (PJ cap qty barang ≤ stok), turunQty().
