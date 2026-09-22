---
paths:
  - 'database/migrations/**'
---

# Migrations

## MySQL: drop FK sebelum drop index/kolom (error 1553)
Saat menghapus kolom/unique index yang masih dipakai foreign key di MySQL, harus drop FK (dan FK cadangan terkait) terlebih dahulu, lalu re-add setelahnya. Mengabaikan ini memicu error 1091/1553 dan membuat migrate:fresh macet di tengah.

## Dedupe nama index dari SHOW INDEX sebelum drop
SHOW INDEX MySQL mengembalikan SATU baris per kolom index komposit, jadi nama index muncul berkali-kali. Sebelum drop index unik/named, dedupe (unique) nama index dulu, dan gunakan Schema::hasIndex($table, $stringName) — argumen string diperlakukan sebagai NAMA index, array sebagai kolom.
