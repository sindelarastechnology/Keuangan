---
paths:
  - 'app/Services/ReportService.php, tests/Feature/AsetPenyusutanTest.php'
---

# Feature

## Pakai whereDate untuk rentang tanggal di model ber-cast date
Suite PHPUnit memakai SQLite :memory:. Model dengan cast 'tanggal' => 'date' tersimpan sebagai 'YYYY-MM-DD 00:00:00', sehingga string compare 'tanggal <= 2026-09-30' gagal dan query mengembalikan baris 0. Untuk penyaringan rentang tanggal (mis. Penyusutan di ReportService::arusKas/penyusutan) selalu gunakan whereDate('tanggal','>=',...)->whereDate('tanggal','<=',...), bukan whereBetween/<= string.
