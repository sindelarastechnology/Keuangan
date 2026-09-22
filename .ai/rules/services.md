---
paths:
  - app/Services/JournalService.php
  - app/Services/NomorGenerator.php
  - 'app/Services/**'
  - app/Services/PengaturanSistemService.php
  - app/Services/ReportService.php
---

# Services

## JournalService guna period-lock check & stamp approval
JournalService::post() memanggil PeriodeService::pastikanDapatDiposting($tanggal) di awal — posting akan throw RuntimeException jika periode bulan tsb is_locked atau is_closed. Jurnal auto-posted selalu di-stamp created_by/updated_by/approved_by + approval_status 'approved'; isPosted=false menghasilkan approval_status 'pending_review'.

## NomorGenerator per-bulan dengan seed anti-duplikat
NomorGenerator memakai key seq per prefix+bulan+tahun (seq_{PREFIX}_{MM}_{YYYY}), di-reset tiap bulan. Saat key belum ada, seq di-seed dari nomor terbesar yang sudah ada di bulan berjalan (maxSeqBulan) agar tidak duplikat saat upgrade / rollback transaksi. PENTING: jangan kembali ke satu key global 'seq_{PREFIX}' — itu bug lama (P1-4). Prefix pemanggil di-mapping ke tabel via TABLE_PREFIX (contoh PB→pembelians, prefix jurnal RET/PEM/PEN/..→jurnal_umum).

## Voided journal + reversal must be excluded as a pair
JurnalUmum::void() (and DepresiasiService::batalkan) posts a reversal (debit/kredit swapped), links it via ref to the original, and sets voided_at on the original (add to $fillable!). Net is 0, but both journals must NEVER appear in GL aggregates/ledgers or users see double rows. Use JurnalUmum::tanpaVoid() scope or where(JurnalUmum::kondisiBukanJurnalVoid()) for any new report/ledger query over jurnal_items or jurnal_umum. voided_at is in $fillable — do not remove.

## Arus Kas uses akunKasBank() (includes child kas/bank akuns)
Kas/bank positions (Arus Kas) must include ALL leaf aset akuns under the configured kas/bank kode prefix (111/112 children like 1111/1121/1122), not just the two configured akun ids. Use akunKasBank() for any kas/bank position logic.

## ArusKas: PPN & disposisi aset masuk rekonsiliasi
arusKas menghitung perubahan PPN via debitNetoRentang/kreditNetoRentang (neto debit/kredit, TIDAK bergantung saldo_normal — akun 213 PPN Masukan di-seed saldo_normal 'kredit' padahal berperilaku aset). Kas operasional = laba + penyusutan − labaDisposisi − Δpiutang − Δpersediaan − ΔppnMasukan + Δhutang + ΔppnKeluaran; investasi dikoreksi +(−akumulasiDisposisi + labaDisposisi). Jurnal penghapusan: tipe 'penghapusan_aset'.
