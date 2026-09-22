---
paths:
  - app/Http/Controllers/DashboardController.php
  - app/Http/Controllers/ChatController.php
  - app/Http/Controllers/NotifikasiController.php
---

# Controllers

## Dashboard: optimasi piutang/hutang via subquery
Total piutang dan hutang dihitung via DB::table subquery MAX(id) per entitas, bukan loop Customer/Supplier (N+1). Jangan kembali ke pola loop. Laba/rugi, pending approval, dan jurnal draft SELALU dikirim ke view untuk semua user (tidak ada lagi variabel `$isAdmin`/`$isAkuntan`).

## Dashboard: kartu piutang jatuh tempo
Kartu "Kredit Jatuh Tempo" memakai subquery `MAX(id)` per customer/supplier pada tabel bb_piutang/bb_hutang (baris saldo terakhir), bukan loop entitas.

## Kelola Pengguna: TIDAK ADA — dikelola via API eksternal
UI "Kelola Pengguna" sudah dihapus total (UserController & views di resources/views/pengaturan/user/ tidak ada; rute pengaturan.user.* tidak ada). Pengelolaan user dilakukan aplikasi luar melalui routes/api.php prefix `v1` dengan middleware `auth.integration` (header `X-Integration-Key`, key dari env `USER_MANAGEMENT_API_KEY`):
- `GET/POST /api/v1/users` (index paginate + search `q`, store → verifikasi email langsung + ProvisionTenant::provision), `GET/PUT/PATCH /api/v1/users/{user}` (update, PATCH status aktif|suspended), `DELETE /api/v1/users/{user}` (guard terakhir: `User::count() <= 1`).
- Kendalanya adalah `App\Http\Controllers\Api\V1\UserController` + `App\Http\Resources\Api\V1\UserResource`. status user dipertahankan via `User::isAktif()` + middleware `akun-aktif` + blokir di LoginRequest/GoogleController.

## chat cari result filter lives in controller
'chat.cari' route returns only users with status='aktif' (hard filter inside ChatController::cari query, NOT in ChatService::cariUser), and DM target must be aktif. Don't move the status filter into ChatService or the komunitas room's 'komunitas' pseudo-user will leak into search results.

## Read-notification uuid binding requires ownership scope
Notifications use Laravel's built-in `notifications` table (uuid pk via notification `via` database). Marking read must guard with `whereKey($notif->id)` + `where('notifiable_type','App\Models\User')->where('notifiable_id',$user->id)` because `DatabaseNotification::findOrFail` binds by uuid only. Never broadcast raw structured('morphs notifiable') via uuid route; always scope ownership.
