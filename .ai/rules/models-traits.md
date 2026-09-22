---
paths:
  - 'app/{Models,Traits}/**'
---

# Models Traits

## BelongsToUser (multi-tenant)
- Trait `App\Traits\BelongsToUser` **wajib di-import di setiap model data** (kecuali `User`).
- Boot: global scope `'belongsToUser'` memfilter `user_id = Auth::id()` **hanya saat `Auth::check()`** — di console/seeder/test tanpa login, scope tidak aktif.
- Event `creating`: auto-fill `user_id = Auth::id()` jika kosong.
- `scopeUntukUser($userId)`: tanpa global scope, filter `user_id = $userId` — dipakai saat provisioning.

## Approval workflow + audit trail lewat trait & model AuditTrail
Approval workflow memakai trait App\Traits\HasApprovalWorkflow (harus di-import di dalam trait!) dengan method approve/reject/requestApproval/revertToDraft yang otomatis menulis AuditTrail::log. Opsional hook postApproval() di model dipanggil setelah approve. AuditTrail::log menerima instance model apa pun (morph). Kolom approval_status enum: draft/pending_review/approved/rejected, default 'approved'.

## Approval/audit field TIDAK mass-assignable
Kolom `approved_by`, `approval_status`, `approval_reason`, `approved_at` sengaja TIDAK ada di `$fillable` semua model header (Penjualan, Pembelian, KasMasuk, KasKeluar, JurnalUmum, PerubahanStok, ReturPembelian, ReturPenjualan, StokOpname). Jalur penulisan sah: (1) HasApprovalWorkflow via `forceFill`+save, (2) JournalService via assign langsung, (3) factory states via hook `configure()->afterMaking(... forceFill ...)` — JANGAN tambahkan field ini ke `$fillable`. Begitu pula `user_id` tidak ada di `$fillable` AuditTrail (diisi event creating BelongsToUser) dan trait BelongsToUser mengunci `user_id` saat updating (dikembalikan ke Auth::id() bila dipaksa berubah).
