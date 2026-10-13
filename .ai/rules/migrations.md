---
paths:
  - 'database/migrations/**'
---

# Migrations

## MySQL: drop FK sebelum drop index/kolom (error 1553)
Saat menghapus kolom/unique index yang masih dipakai foreign key di MySQL, harus drop FK (dan FK cadangan terkait) terlebih dahulu, lalu re-add setelahnya. Mengabaikan ini memicu error 1091/1553 dan membuat migrate:fresh macet di tengah.

## Dedupe nama index dari SHOW INDEX sebelum drop
SHOW INDEX MySQL mengembalikan SATU baris per kolom index komposit, jadi nama index muncul berkali-kali. Sebelum drop index unik/named, dedupe (unique) nama index dulu, dan gunakan Schema::hasIndex($table, $stringName) — argumen string diperlakukan sebagai NAMA index, array sebagai kolom.

## Drop tabel tenant dari MySQL shared pakai migrasi ber-guard
MySQL shared hanya untuk manajemen user + tabel framework; tabel tenant (dari TenantTables) dihapus lewat migrasi forward-only. Pada migrasi anonim `getConnection()` selalu null — deteksi konteks pakai `DB::getDefaultConnection() !== 'mysql'` (provision tenant mengarahkan default ke 'tenant') DAN `DB::connection('mysql')->getDriverName() !== 'mysql'` (test sqlite di-skip). Jangan tambah tabel tenant baru ke jalur migrasi shared tanpa guard ini.
