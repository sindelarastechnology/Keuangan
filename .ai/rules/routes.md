---
paths:
  - routes/web.php
  - routes/auth.php
---

# Routes

## Multi-tenant & autentikasi
1 user = 1 tenant, isolasi via `App\Traits\BelongsToUser` (global scope `user_id` + auto-fill saat `creating`).

### Registrasi & verifikasi email
- Registrasi publik **aktif**: `GET/POST /register` (guest + throttle:register, 5×/menit/IP).
- `MustVerifyEmail` aktif di `App\Models\User`. Middleware `verified` diterapkan ke seluruh route utama (`Route::middleware(['auth', 'verified'])`).
- Route verifikasi email (`/verify-email`, `/verify-email/{id}/{hash}`, `/email/verification-notification`) tetap di grup `auth` (bukan `verified`) agar user yang belum verifikasi bisa mengakses halaman verifikasi.
- Registrasi mengirim `SendEmailVerificationNotification` otomatis.
- Provision tenant dipanggil via event `Login` dan `Verified` oleh `ProvisionTenantOnAuth` listener (terdaftar di `AppServiceProvider`).

### Google OAuth (manual, tanpa Socialite)
- Route: `GET auth/google` (google.redirect) dan `GET auth/google/callback` (google.callback), keduanya di grup guest.
- Implementasi manual di `App\Http\Controllers\Auth\GoogleController` memakai `Http` facade (Guzzle 8 — Socialite tak kompatibel).
- `GOOGLE_REDIRECT` wajib persis sama karakter-demi-karakter dengan "Authorized redirect URIs" di Google Cloud Console (termasuk http/s & port) — config/services.php memakai nilai ENV tetap, bukan request host (aman di belakang Cloudflare).
- Scope pakai default OAuth: `openid email profile` (nama, email, foto profil saja — TIDAK butuh enable Google People API).
- User baru dari Google dibuat dengan `password = null` (kolom password nullable sejak migrasi `make_password_nullable`) dan `email_verified_at = now()` via `forceFill` (tidak ada di `#[Fillable]` User).
- `google_id` unik; login Google mengikat google_id ke akun dengan email yang sama bila sudah ada.

### Navigasi
Pendaftaran route/menu baru: tambahkan di `resources/views/layouts/nav-data.php` (single source of truth), bukan hardcode di view lain.

### Catatan penting untuk developer
- **Seeder wajib `Auth::login($user)` sebelum membuat data model** — tanpa `Auth::login`, event `creating` di `BelongsToUser` tidak mengisi `user_id` dan global scope tidak aktif.
- `DatabaseSeeder` telah menggunakan `Auth::login($admin)` + `ProvisionTenant::provision()` tanpa `WithoutModelEvents`.
- `DB::table()` query mentah **wajib** filter `user_id` secara eksplisit — global scope Eloquent tidak berlaku.
- `Pengaturan::atur()` (NomorGenerator) dijalankan dalam DB::transaction + `lockForUpdate()` — scope aktif saat login.
- Provisi tenant idempoten: `ProvisionTenant::provision()` hanya jalan bila `akun_perkiraan` untuk user belum ada.
