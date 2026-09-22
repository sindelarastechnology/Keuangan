---
paths:
  - 'resources/views/inventori/retur-*/create.blade.php'
---

# Inventori

## Combobox retur filter client-side dari daftar preloaded
Combobox pemilihan transaksi PADA form retur mem-filter client-side (bukan AJAX per keystroke): controller create() merender seluruh daftar returable via daftarReturable() (status posted, ada barang, sisa>0; parameter limit/tebalkan, create pakai PHP_INT_MAX agar semua muncul) ke @json, lalu Alpine hasilCari() memfilter lokal atas daftar tsb (nomor/nama/label). Klik field selalu buka dropdown penuh; ketik mempersempit. Search endpoint tetap ada (dipakai API/test). Pilih → auto-muat item lewat sumber-items, hidden id diisi, siapSubmit() buang baris kosong.

## Pemilihan transaksi retur pakai native select
Form retur memilih transaksi lewat native <select> (bukan combobox input custom) — kontrol aksesibilitas browser, pastikan daftar selalu muncul saat diklik. Options di-render server-side dari daftarReturable() (status posted, ada barang, sisa>0; create pakai PHP_INT_MAX agar semua muncul). select memakai x-model="transaksiId" + @change="pilihTransaksi()" -> fetch sumber-items utk auto-muat item. Search endpoint tetap ada (dipakai API/test).

## fetch sumber-items pakai URL relatif (same-origin)
fetch() ke endpoint JSON (sumber items) harus pakai URL RELATIF, bukan route() absolut — APP_URL bisa http://localhost:8000 sedangkan user browsing http://127.0.0.1:8000 => cross-origin, browser memblok respon, item tidak pernah muncul. Bangun dengan @php str_replace(url('/'), '', route(...)) lalu @json. Trap yang sama berlaku utk semua fetch di app ini (Alpine/JS).
