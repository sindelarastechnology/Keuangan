---
paths:
  - 'resources/views/transaksi/*/index.blade.php'
---

# Views Transaksi

## Pola index mobile card (tabel hidden md:block + daftar card md:hidden)
Pola index responsif (Fase 2): bungkus tabel dengan 'hidden md:block overflow-x-auto', lalu blok list mobile menjadi 'md:hidden space-y-3 p-4' dengan <x-mobile-card> per baris (opsi: title/subtitle/amount/badge/actions). Awalan: tombol aksi mobile wajib pakai konfirm-dialog dengan nama unik berakhiran '-mobile' (mis. hapus-kas-masuk-<id>-mobile) karena nama dialog tabel sudah dipakai dan modal TANPA x-teleport tidak bisa tampil dari induk display:none.
