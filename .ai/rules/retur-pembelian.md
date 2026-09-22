---
paths:
  - 'resources/views/inventori/retur-penjualan/create.blade.php,resources/views/inventori/retur-pembelian/create.blade.php'
---

# Retur Pembelian

## Combobox search transaksi + auto-muat item retur
Pemilihan transaksi pada form retur memakai combobox searchable (bukan <select> penuh): ketik q → GET retur-penjualan.search / retur-pembelian.search → daftar client-side yang sudah difilter hanya transaksi yang masih punya sisa diretur. Setelah transaksi terpilih, seluruh item sumber dimuat otomatis sebagai baris edit di panel kanan (siapSubmit membuang baris jumlah kosong). Hidden input penjualan_id/pembelian_id diisi dari pilihan; backend store validasi tidak berubah (items.*.jumlah harus numeric min 0.01).
