---
paths:
  - app/Http/Controllers/Master/RekeningController.php
---

# Master

## Rekening store/update: akun_id optional (auto-provision child akun)
akun_id is nullable; empty = keep existing (update) or auto-provison a fresh leaf child akun (store, provisiAkunBaru: kode = root kas/bank kode + next free digit, e.g. 1123, parent = kas/bank header, debit aset). Never require akun_id — after rekening akun isolation all configured kas/bank akuns are consumed and the dropdown may be empty. The per-rekening-unique-akun rule still holds via validation.

## Rekening dengan jurnal terkait tidak bisa dihapus
destroy() tidak hanya mengecek 5 tabel transaksi — juga memblokir jika akun rekening dipakai di JurnalItem (termasuk jurnal saldo awal). Ini melindungi Buku Besar dari transaksi yatim.
