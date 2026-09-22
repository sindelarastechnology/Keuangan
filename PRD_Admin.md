# PRD — Aplikasi Admin Pengguna (Flutter, Multi-Device)

Dokumen Kebutuhan Produk ini memandu pembangunan **aplikasi admin** (dibangun
dengan Flutter, berjalan multi-device: Android/iOS/Windows/macOS/Linux) untuk
**mengelola akun pengguna** aplikasi Keuangan. Pengelolaan akun dilakukan
**bukan** lewat UI web lama (sudah dihapus), melainkan lewat **REST API
user-management** yang sudah tersedia di backend Laravel.

Modul yang dicakup dokumen ini (semuanya sudah tersedia di backend):

1. **User management** — CRUD akun, status aktif/suspended, verifikasi email.
2. **Manajemen donasi** — lihat bukti transfer, konfirmasi donasi pengguna.
3. **Manajemen paket (Plan)** — set paket `free` / `pro` per akun.
4. **Chat** — monitoring ruangan & pesan, moderasi, serta **kirim pesan atas
   nama Admin** agar admin dapat berinteraksi dengan pengguna.

---

## 1. Ringkasan

- **Pengguna aplikasi admin:** administrator / tim operasional.
- **Tujuan:** membuat, melihat, mengubah, mengaktifkan/membekukan, menghapus
  akun, mengelola donasi, mengelola paket langganan, serta berinteraksi dengan
  pengguna lewat chat — dari perangkat mana pun.
- **Backend:** aplikasi Laravel yang sama (repo ini), menyajikan API publik
  di `/api/v1/...`.
- **Keamanan API:** dikunci dengan satu **kunci integrasi** yang dikirim
  sebagai header `X-Integration-Key`. Tanpa kunci → `401`.
- **Efek samping penting:**
  - Membuat akun = otomatis **menyiapkan tenant lengkap** (chart of account,
    pajak, gudang default, rekanan UMUM, periode), **email langsung
    terverifikasi**, dan `plan` default **`pro`**.
  - User yang **daftar mandiri di web** langsung muncul di API
    (`GET /api/v1/users`) dengan `email_verified_at` kosong (belum verifikasi);
    admin app mendeteksinya lewat **pola polling** — lihat §12.
  - Menetapkan status `suspended` = akun **langsung kehilangan akses** login
    web dan **sesi aktifnya di-logout** oleh middleware `akun-aktif`.
  - Menghapus akun terakhir ditolak (harus minimal tersisa 1 akun).
  - Mengonfirmasi donasi → pengguna menerima notifikasi in-app
    (`donation.status`).
  - Mengubah paket (`plan`) → pengguna menerima notifikasi in-app
    (`plan.change`); batas fitur/kuota langsung berlaku.
  - Admin mengirim pesan ke ruang DM → kedua anggota ruang menerima
    notifikasi in-app (`admin.message`).

---

## 2. Glosarium

| Istilah | Arti |
| --- | --- |
| **Akun** | Rekaman `users` di backend (nama, email, password, status, plan). |
| **Status** | `aktif` (dapat login) atau `suspended` (diblokir). |
| **Paket / Plan** | `free` atau `pro`; menentukan kuota barang & hak fitur (`plan=pro` default akun baru). |
| **Tenant** | Isolasi data per pengguna (semua baris transaksi ber-label `user_id`). |
| **Donasi** | Kontribusi pengguna dari halaman web; berstatus `pending` → `confirmed`. |
| **Ruang chat** | `global` (komunitas semua pengguna) atau `dm` (dua akun). |
| **Pesan admin** | Pesan bertipe `admin` (tak punya `sender_id`); ditampilkan berlabel **Admin** di sisi pengguna. |
| **Moderasi** | Penghapusan pesan melanggar dari hadapan publik, tercatat moderator & alasan. |
| **Laporan pesan** | Antrean `ChatReport` status `open` yang menunggu ditindak admin. |
| **Kunci integrasi** | `X-Integration-Key`; dibaca backend dari env `USER_MANAGEMENT_API_KEY`. |
| **Provision tenant** | Penyiapan data awal milik satu akun saat akun pertama kali dibuat. |
| **Polling** | Admin app meminta data ke API secara berkala (tanpa push/webhook); dipakai untuk mendeteksi user baru (lihat §12). |
| **Snapshot** | Nilai lokal (`lastSeenMaxId`, `lastSeenTotal`) yang disimpan per device untuk membandingkan ada-tidaknya data baru. |

---

## 3. Arsitektur & Alur Integrasi

```
┌─────────────────────────────┐        HTTPS        ┌───────────────────────────────┐
│  Flutter Admin App          │ ───────────────────► │  Backend Laravel (repo ini)   │
│  (multi-device)             │  header             │  routes/api.php → /api/v1     │
│                             │  X-Integration-Key  │  └─ middleware auth.integration│
│  UI: Kelola User · Donasi   │ ◄─────────────────── │  └─ middleware throttle:60,1  │
│  · Paket · Chat             │        JSON          │  └─ Api\V1\*Controllers        │
└─────────────────────────────┘                      └───────────────────────────────┘
```

- Satu aplikasi Flutter menunjuk ke **satu base URL** backend (berbeda per
  lingkungan: dev/staging/production — lihat §5).
- Semua request memakai **kunci integrasi yang sama** untuk semua device
  (bukan per-user); penyimpanan kunci di device aman (§10).
- Respon selalu JSON. Objek akun selalu dibungkus `data`.
- Data ditarik **pull-based** (app admin bertanya berkala ke API, tanpa
  push/webhook): pola sinkronisasi & deteksi "user baru" dijelaskan di §12.
- Tidak ada autentikasi per-user *di dalam* API ini saat ini; autorisasi admin
  sepenuhnya dipegang oleh keamanan kunci integrasi + throttle.

---

## 4. Persyaratan Backend (sudah terpenuhi)

| Item | Nilai |
| --- | --- |
| Env | `USER_MANAGEMENT_API_KEY=<kunci_rahasia>` di `.env` (lihat `.env.example`) |
| Key dev | `96f679d64553f202d94044f010c9f051cd77597b7f5b7d970d8e911a3b5d0cf1` (sudah diset di `.env` lokal) |
| Middleware | `auth.integration` (validasi `hash_equals` terhadap header) |
| Rate limit | `60` request/menit per IP (`throttle:60,1`) → `429` bila lewat |
| Route prefix | `/api/v1` |
| Kontrak user | `routes/api.php`, `app/Http/Controllers/Api/V1/UserController.php`, `app/Http/Resources/Api/V1/UserResource.php` |
| Kontrak paket | `app/Http/Controllers/Api/V1/Admin/PlanController.php` |
| Kontrak chat | `app/Http/Controllers/Api/V1/Admin/ChatModerationController.php` |
| Kontrak donasi | `app/Http/Controllers/Api/V1/Admin/DonasiController.php` |
| Notifikasi | `app/Http/Controllers/Api/V1/Admin/NotifyController.php`, `app/Services/NotifikasiService.php` |
| Testing | `tests/Feature/Api/UserManagementApiTest.php` (14 skenario hijau), `tests/Feature/Api/V1/DonasiApiTest.php` (6), `tests/Feature/Api/V1/LanggananDanModerasiApiTest.php` (7) |

> **Catatan kunci:** Jangan mengubah nilai `USER_MANAGEMENT_API_KEY` yang sudah
> ada di `.env` — nilai itulah yang harus dipakai oleh aplikasi admin di
> lingkungan dev. Bila diubah, semua device admin harus diperbarui (§10).

---

## 5. Base URL & Cara Mengakses

- **Base URL (dev):** `http://localhost:8000/api/v1`
- **Header wajib (semua request):** `X-Integration-Key: 96f679d64553f202d94044f010c9f051cd77597b7f5b7d970d8e911a3b5d0cf1`
- **Format body:** `application/json` (untuk `POST`/`PUT`/`PATCH`).
- Versi independen dari sesi/login web: API ini hanya mengandalkan kunci di header.

Contoh cepat (PowerShell/`curl`):

```bash
KEY="96f679d64553f202d94044f010c9f051cd77597b7f5b7d970d8e911a3b5d0cf1"
BASE="http://localhost:8000/api/v1"

# Cek koneksi: daftar akun halaman pertama
curl -H "X-Integration-Key: $KEY" "$BASE/users?per_page=1"

# Daftar paket + informasi paket akun id 42
curl -H "X-Integration-Key: $KEY" "$BASE/plans?user_id=42"

# Daftar ruangan chat (cari id ruang komunitas)
curl -H "X-Integration-Key: $KEY" "$BASE/chat/rooms"

# Kirim pengumuman admin ke ruang komunitas
curl -X POST -H "X-Integration-Key: $KEY" -H "Content-Type: application/json" \
  -d '{"body":"Halo semua pengguna KasPro!"}' \
  "$BASE/chat/rooms/1/messages"

# Konfirmasi donasi id 5
curl -X PATCH -H "X-Integration-Key: $KEY" -H "Content-Type: application/json" \
  -d '{"status":"confirmed"}' \
  "$BASE/donasi/5/status"
```

> **Production:** ganti kunci dengan nilai baru aman (32 byte hex):
> ```bash
> php -r 'echo "USER_MANAGEMENT_API_KEY=" . bin2hex(random_bytes(32)) . PHP_EOL;'
> ```
> Lalu set di `.env` & restart server (`php artisan config:clear`).

---

## 6. Spesifikasi API (Kontrak Lengkap)

### 6.1. Atribut Objek Akun

Semua endpoint akun mengembalikan objek dengan **wrapper `data`**:

```json
{
  "data": {
    "id": 42,
    "name": "Budi Santoso",
    "email": "budi@contoh.id",
    "email_verified_at": "2026-09-13T06:30:00+00:00",
    "status": "aktif",
    "plan": "pro",
    "created_at": "2026-09-13T06:30:00+00:00",
    "updated_at": "2026-09-13T06:30:00+00:00"
  }
}
```

- `id`: int — identitas akun (dipakai untuk semua route berparameter).
- `email_verified_at`, `created_at`, `updated_at`: string ISO-8601 UTC, **nullable**.
- `status`: `"aktif"` | `"suspended"`.
- `plan`: `"free"` | `"pro"`. Akun yang dibuat via API (dan default DB) = `"pro"`;
  paket **efektif** bisa turun otomatis ke `"free"` bila `plan_expires_at` sudah
  lewat (lihat §6.12).
- `password`, `remember_token`, `google_id` **tidak pernah** dikembalikan.
- Informasi lengkap paket (`is_pro`, `plan_expires_at`, `trial_ends_at`) ada di
  `GET /api/v1/plans?user_id=...` dan `PATCH /api/v1/users/{id}/plan` (§6.12).

### 6.2. Autentikasi

```
X-Integration-Key: <nilai USER_MANAGEMENT_API_KEY backend>
```

Selalu kirim header ini. Bila salah/kosong → `401`:

```json
{ "message": "Unauthorized." }
```

### 6.3. Daftar Akun — `GET /api/v1/users`

Query string (semua opsional):

| Param | Tipe | Default | Keterangan |
| --- | --- | --- | --- |
| `q` | string | `""` | Cari sebagian kata pada `name` atau `email` (case-insensitive LIKE). |
| `per_page` | int | `20` | Jumlah item per halaman. |

Respon `200` — daftar ter-paginasi (struktur pagination bawaan Laravel):

```json
{
  "data": [ { "id": 1, "name": "…", "email": "…", "status": "aktif", "plan": "free", "…": "…" } ],
  "links": { "first": "…", "last": "…", "prev": null, "next": "…" },
  "meta": { "current_page": 1, "last_page": 3, "per_page": 20, "total": 45 }
}
```

Contoh (Flutter):
`GET /api/v1/users?q=budi&per_page=20` → halaman 1 berisi akun yang cocok.

### 6.4. Buat Akun — `POST /api/v1/users`

Body (JSON):

| Field | Tipe | Wajib | Aturan |
| --- | --- | --- | --- |
| `name` | string | ya | maks 255 |
| `email` | string | ya | format email, lowercase, **unik**, maks 255 |
| `password` | string | ya | **confirmed** (`password_confirmation` harus dikirim), kebijakan Laravel (min 8, kombinasi huruf & angka) |
| `password_confirmation` | string | ya | harus sama dengan `password` |

Respon `201` — objek akun baru + efek otomatis:

- `status` selalu `"aktif"`, `plan` selalu `"pro"`.
- Email **langsung terverifikasi** (`email_verified_at` terisi) — akun boleh
  langsung login.
- **Tenant disiapkan** (idempoten): chart of account (termasuk kode 526),
  template aset, pajak, pengaturan sistem, periode akuntansi, gudang default
  `GDG-0001`, serta pelanggan & pemasok `UMUM`.

Contoh request:

```json
{
  "name": "Budi Santoso",
  "email": "budi@contoh.id",
  "password": "rahasia-kuat-123",
  "password_confirmation": "rahasia-kuat-123"
}
```

Contoh respon (status kode `201`):

```json
{
  "data": {
    "id": 42,
    "name": "Budi Santoso",
    "email": "budi@contoh.id",
    "email_verified_at": "2026-09-13T06:30:00+00:00",
    "status": "aktif",
    "plan": "pro",
    "created_at": "2026-09-13T06:30:00+00:00",
    "updated_at": "2026-09-13T06:30:00+00:00"
  }
}
```

Gagal validasi → `422`:

```json
{
  "message": "The email has already been taken. (and 1 more error)",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password field is required."]
  }
}
```

### 6.5. Detail Akun — `GET /api/v1/users/{id}`

Respon `200` — satu objek akun. `404` bila id tidak ada.

### 6.6. Ubah Identitas — `PUT /api/v1/users/{id}`

Body (JSON):

| Field | Tipe | Wajib | Aturan |
| --- | --- | --- | --- |
| `name` | string | ya | maks 255 |
| `email` | string | ya | email, lowercase, unik — email milik akun sendiri tetap valid |
| `password` | string | tidak | opsional; hanya diganti bila diisi; `confirmed` |
| `password_confirmation` | string | tidak | wajib bila `password` diisi |

Respon `200` — objek akun terbaru. Perilaku: `status` tidak bisa diubah di
endpoint ini (pakai §6.7), `plan` tidak bisa diubah di sini (pakai §6.12).

### 6.7. Ubah Status — `PATCH /api/v1/users/{id}/status`

Body:

| Field | Tipe | Wajib | Aturan |
| --- | --- | --- | --- |
| `status` | string | ya | `aktif` atau `suspended` |

Respon `200` — objek akun terbaru. Nilai lain → `422`.

**Efek status `suspended`** (penting untuk UX aplikasi admin):

- Akun **tidak bisa lagi login** web/email-password — form login menampilkan
  pesan "Akun Anda telah dinonaktifkan. Silakan hubungi administrator."
- Jika akun **sedang login** di web, sesi **langsung di-logout** oleh
  middleware `PastikanAkunAktif` (dipasang di halaman-halaman inti aplikasi).
- Akses **dikembalikan** begitu status dikembalikan ke `aktif` (`isAktif()`
  = `status !== 'suspended'`).

### 6.8. Hapus Akun — `DELETE /api/v1/users/{id}`

Respon `200`:

```json
{ "message": "Akun dihapus." }
```

Aturan:

- **Akun terakhir tidak boleh dihapus** (saat menyisakan 0 akun). Ditolak
  `422`:
  `{ "message": "...", "errors": { "user": ["Akun terakhir tidak dapat dihapus."] } }`
- Data tenant (transaksi, dsb.) ikut terhapus sesuai relasi cascade/hapus
  yang berlaku pada model (verifikasi saat implementasi UI: tampilkan dialog
  konfirmasi yang menyebut destruktif).

### 6.9. Ringkasan Status Kode

| Kode | Arti | Catatan |
| --- | --- | --- |
| `200` | sukses | |
| `201` | akun / pesan dibuat | `POST /users`, `POST /chat/rooms/{room}/messages` |
| `401` | kunci salah/tidak ada | `{"message":"Unauthorized."}` |
| `404` | akun/ruang/donasi tidak ditemukan | id salah |
| `422` | validasi gagal / akun terakhir | `errors.field[]` |
| `429` | melebihi 60 req/menit | backoff klien |
| `500` | error internal | `message` acak |

### 6.10. Contoh cURL — User Management

```bash
# Daftar + cari
curl -H "X-Integration-Key: $KEY" "$BASE/users?q=budi&per_page=20"

# Buat
curl -X POST -H "X-Integration-Key: $KEY" -H "Content-Type: application/json" \
  -d '{"name":"Budi Santoso","email":"budi@contoh.id","password":"rahasia-kuat-123","password_confirmation":"rahasia-kuat-123"}' \
  "$BASE/users"

# Detail
curl -H "X-Integration-Key: $KEY" "$BASE/users/42"

# Ubah nama/email (password opsional)
curl -X PUT -H "X-Integration-Key: $KEY" -H "Content-Type: application/json" \
  -d '{"name":"Budi Baru","email":"budi.baru@contoh.id"}' \
  "$BASE/users/42"

# Bekukan
curl -X PATCH -H "X-Integration-Key: $KEY" -H "Content-Type: application/json" \
  -d '{"status":"suspended"}' \
  "$BASE/users/42/status"

# Hapus
curl -X DELETE -H "X-Integration-Key: $KEY" "$BASE/users/42"
```

---

### 6.11. Manajemen Donasi

Donasi dikirim pengguna dari halaman web KasPro (QRIS screen). Admin app cukup
memverifikasi bukti dan mengonfirmasi status.

#### Atribut objek donasi

```json
{
  "id": 1,
  "user": { "id": 2, "name": "Budi Santoso", "email": "budi@contoh.id" },
  "nominal": 150000.0,
  "keterangan": "Dukungan pengembangan",
  "status": "pending",
  "bukti_url": "http://localhost:8000/storage/bukti-donasi/....jpg",
  "created_at": "2026-09-16T02:00:00+00:00",
  "updated_at": "2026-09-16T02:00:00+00:00"
}
```

- `id`: int — identitas donasi.
- `nominal`: decimal|null — `null` berarti "seikhlasnya" (tanpa nominal).
   Dikirim sebagai `float` dalam JSON.
- `keterangan`: string|null — opsional.
- `status`: `"pending"` | `"confirmed"`.
- `bukti_url`: string|null — URL absolut bukti transfer (bila diunggah);
   `null` bila pengguna tidak mengunggah file. **Download gambar memakai URL
   ini polos (tanpa header kunci).**
- `user`: objek akun penyumbang (id/name/email).

#### Daftar donasi — `GET /api/v1/donasi`

Query string (semua opsional):

| Param | Tipe | Default | Keterangan |
| --- | --- | --- | --- |
| `status` | string | `""` | Filter `pending` atau `confirmed`. Kosong = semua. |
| `q` | string | `""` | Cari sebagian kata pada **nama/email penyumbang**. |
| `per_page` | int | `20` | Jumlah item per halaman. |

Urutan: `id` **turun** (yang terbaru paling atas).

Respon `200`:

```json
{
  "message": "OK",
  "data": [ /* objek donasi */ ],
  "meta": { "current_page": 1, "last_page": 1, "per_page": 20, "total": 3 }
}
```

Contoh (Flutter):
`GET /api/v1/donasi?status=pending&per_page=20` → daftar donasi yang belum diverifikasi.

#### Detail donasi — `GET /api/v1/donasi/{id}`

Respon `200` — objek donasi tunggal (wrapper `data`).

#### Ubah status — `PATCH /api/v1/donasi/{id}/status`

Body (JSON):

| Field | Tipe | Wajib | Aturan |
| --- | --- | --- | --- |
| `status` | string | ya | `pending` \| `confirmed` |

Saat status diubah menjadi `confirmed`, pengguna web menerima notifikasi dalam
aplikasi `donation.status` ("Donasi terkonfirmasi"). Respon `200` — objek donasi
terbaru (wrapper `data`).

#### Alur verifikasi (rekomendasi UI admin)

1. Buka `GET /api/v1/donasi?status=pending` → lihat `bukti_url` & `nominal`.
2. Cocokkan bukti transfer dengan nominal (bila diisi); unduh gambar `bukti_url`.
3. `PATCH /api/v1/donasi/{id}/status` → `"confirmed"` (pengguna langsung dinotifikasi).
4. Bila donasi disertai permintaan aktivasi Pro: lanjutkan dengan
   `PATCH /api/v1/users/{id}/plan` → `{ "plan": "pro" }` (§6.12).

#### cURL

```bash
# Daftar donasi pending
curl -H "X-Integration-Key: $KEY" "$BASE/donasi?status=pending"

# Detail
curl -H "X-Integration-Key: $KEY" "$BASE/donasi/5"

# Konfirmasi
curl -X PATCH -H "X-Integration-Key: $KEY" -H "Content-Type: application/json" \
  -d '{"status":"confirmed"}' \
  "$BASE/donasi/5/status"
```

---

### 6.12. Manajemen Paket (Plan: free / pro)

Mengatur paket langganan tiap akun. Paket menentukan kuota & hak fitur aplikasi
Keuangan (lihat `config/plans.php`):

| Aspek | `free` | `pro` |
| --- | --- | --- |
| `nama` | Gratis | Pro |
| `batas_barang` | 50 | Tak terbatas (server mengembalikan `9223372036854775807` / `PHP_INT_MAX`) |
| Hak fitur (aset, retur, multi-gudang, opname, daftar harga, tutup buku, BB khusus, export) | semua mati | semua aktif |

#### Daftar paket & status user — `GET /api/v1/plans`

Query string:

| Param | Tipe | Default | Keterangan |
| --- | --- | --- | --- |
| `user_id` | int | `""` | Bila diisi, `data.user` berisi paket efektif akun tsb. |

Respon `200`:

```json
{
  "message": "OK",
  "data": {
    "paket": {
      "free": { "nama": "Gratis", "batas_barang": 50 },
      "pro":  { "nama": "Pro", "batas_barang": 9223372036854775807 }
    },
    "user": {
      "id": 42,
      "plan": "free",
      "is_pro": false,
      "trial_ends_at": null,
      "plan_expires_at": null
    }
  }
}
```

- `data.user` hanya hadir bila `user_id` dikirim dan akun ditemukan.
- `data.user.plan` = paket **efektif**: bila `plan="pro"` tetapi
  `plan_expires_at` sudah lewat, server mengembalikan `free`.
- `is_pro` = paket efektif `pro`.
- Tips UI: tampilkan `batas_barang` Pro sebagai "Tak terbatas" (nilai besar).

#### Ubah paket — `PATCH /api/v1/users/{id}/plan`

Body (JSON):

| Field | Tipe | Wajib | Aturan |
| --- | --- | --- | --- |
| `plan` | string | ya | `free` \| `pro` |
| `plan_expires_at` | string/date | tidak | ISO-8601; nilai untuk `free` biasanya dihapus (`null`). |
| `trial_ends_at` | string/date | tidak | ISO-8601; opsional untuk status trial. |

Respon `200`:

```json
{
  "message": "Paket akun diperbarui.",
  "data": {
    "id": 42,
    "plan": "pro",
    "is_pro": true,
    "plan_expires_at": "2027-01-01T00:00:00+00:00",
    "trial_ends_at": null
  }
}
```

Efek:

- Paket & tanggal disimpan langsung; pengguna menerima notifikasi in-app
  `plan.change` ("Paket Anda kini: Pro./Gratis.").
- Kuota barang & hak fitur di aplikasi web menyesuaikan seketika (mis. beralih
  ke `free` saat jumlah barang > 50 → menu/layanan terkunci terkait).
- Nilai lain selain `free`/`pro` → `422`.

#### cURL

```bash
# Lihat paket + status akun 42
curl -H "X-Integration-Key: $KEY" "$BASE/plans?user_id=42"

# Naikkan ke Pro (berlaku 1 tahun)
curl -X PATCH -H "X-Integration-Key: $KEY" -H "Content-Type: application/json" \
  -d '{"plan":"pro","plan_expires_at":"2027-01-01T00:00:00+00:00"}' \
  "$BASE/users/42/plan"

# Turunkan ke free
curl -X PATCH -H "X-Integration-Key: $KEY" -H "Content-Type: application/json" \
  -d '{"plan":"free"}' \
  "$BASE/users/42/plan"
```

---

### 6.13. Chat — Monitoring, Moderasi & Interaksi Admin

Admin dapat **melihat semua ruangan & pesan**, **memoderasi** pesan melanggar,
dan **mengirim pesan atas nama Admin** sehingga bisa berinteraksi dengan semua
pengguna (ruang `global` = komunitas) maupun per ruang percakapan pribadi (`dm`).

#### Daftar ruangan — `GET /api/v1/chat/rooms`

Query string:

| Param | Tipe | Default | Keterangan |
| --- | --- | --- | --- |
| `limit` | int | `100` | Jumlah maks ruangan (terbaru dahulu, urutan `updated_at` turun). |

Respon `200`:

```json
{
  "message": "OK",
  "data": [
    {
      "id": 1,
      "tipe": "global",
      "user_a": null,
      "user_b": null,
      "terakhir_pesan": "2026-09-16T02:00:00+00:00"
    },
    {
      "id": 12,
      "tipe": "dm",
      "user_a": { "id": 2, "name": "Budi Santoso" },
      "user_b": { "id": 7, "name": "Siti Aminah" },
      "terakhir_pesan": "2026-09-16T01:30:00+00:00"
    }
  ]
}
```

- Ruang `global` (id biasanya 1) = komunitas semua pengguna — tempat utama admin
  berinteraksi menjangkau banyak pengguna sekaligus.
- Ruang `dm` = percakapan antara dua akun; admin boleh membalas di dalamnya
  lalu kedua anggota diberi tahu lewat notifikasi in-app (`admin.message`).

#### Daftar pesan satu ruangan — `GET /api/v1/chat/rooms/{room}/messages`

Query string:

| Param | Tipe | Default | Keterangan |
| --- | --- | --- | --- |
| `after` | int | `0` | Ambil pesan ber-id **lebih besar** dari nilai ini (pemetaan polling). |

Respon `200` — larik pesan terurut naik (id):

```json
{
  "message": "OK",
  "data": [
    {
      "id": 120,
      "sender_id": 2,
      "sender": "Budi Santoso",
      "type": "user",
      "body": "Halo semuanya",
      "moderated": false,
      "moderated_at": null,
      "created_at": "2026-09-16T02:00:00+00:00"
    },
    {
      "id": 121,
      "sender_id": null,
      "sender": "Admin",
      "type": "admin",
      "body": "Terima kasih atas antusiasme Anda!",
      "moderated": false,
      "moderated_at": null,
      "created_at": "2026-09-16T02:01:00+00:00"
    }
  ]
}
```

- Pesan `type="admin"` punya `sender_id=null` & `sender="Admin"`.
- Pesan yang sudah dimoderasi punya `moderated=true` (+ `moderated_at`);
  sebaiknya **jangan ditampilkan** di UI atau tampilkan sebagai "pesan dihapus".

#### Kirim pesan sebagai Admin — `POST /api/v1/chat/rooms/{room}/messages`

Body (JSON):

| Field | Tipe | Wajib | Aturan |
| --- | --- | --- | --- |
| `body` | string | ya | 1–2000 karakter |

Respon `201`:

```json
{
  "message": "Pesan admin terkirim.",
  "data": {
    "id": 122,
    "room_id": 1,
    "type": "admin",
    "body": "Pengumuman penting!",
    "created_at": "2026-09-16T02:05:00+00:00"
  }
}
```

Perilaku:

- Pesan tersimpan `type="admin"`, tanpa `sender_id`; di aplikasi web tampil
  berlabel **Admin** (avatar ungu, inisial "A") di ruang yang sama.
- Ruang `global` → seluruh pengguna melihat pesan di chat komunitas mereka.
- Ruang `dm` → kedua anggota melihat pesan Admin di percakapan itu dan menerima
  notifikasi in-app `admin.message` ("Pesan dari Admin").
- `body` kosong/null → `422` (`errors.body[]`).
- Pesan Admin **tidak dapat** di-laporkan pengguna.

#### Antrean laporan — `GET /api/v1/chat/reports`

Query string:

| Param | Tipe | Default | Keterangan |
| --- | --- | --- | --- |
| `per_page` | int | `25` | Pagination. |

Respon `200`:

```json
{
  "message": "OK",
  "data": [
    {
      "id": 3,
      "status": "open",
      "reason": "Bahasa tidak pantas",
      "created_at": "2026-09-16T01:50:00+00:00",
      "reporter": { "id": 5, "name": "Dewi Lestari" },
      "message": {
        "id": 118,
        "body": "…pesan yang dilaporkan…",
        "sender": "Budi Santoso",
        "moderated": false
      }
    }
  ],
  "meta": { "total": 3 }
}
```

- Hanya laporan berstatus `open` (antrean) yang dikembalikan.

#### Moderasi pesan — `PATCH /api/v1/chat/messages/{message}/moderate`

Body (JSON):

| Field | Tipe | Wajib | Aturan |
| --- | --- | --- | --- |
| `reason` | string | ya | min 10, maks 255 |

Respon `200`:

```json
{ "message": "Pesan dimoderasi.", "data": { "id": 118 } }
```

Perilaku:

- Pesan langsung "hilang dari hadapan publik" (di web ditampilkan sebagai
  "Pesan dihapus oleh moderator" untuk pengguna lain).
- Pengirim pesan menerima notifikasi in-app `chat.moderated` berisi alasan.
- Pesan yang sudah dimoderasi ditolak ulang (`422`).

#### Tutup laporan — `PATCH /api/v1/chat/reports/{report}`

Body kosong. Respon `200`:

```json
{ "message": "Laporan ditutup.", "data": { "id": 3 } }
```

Menandai laporan selesai (status `closed`) setelah ditindak.

#### Rekomendasi UX admin (alur "Kotak Masuk")

1. Tab **Laporan** → `GET /chat/reports`; per item lihat `body` & `reason`.
2. Putusan: **Moderasi** (`PATCH .../moderate`, isi alasan) atau **Tutup**
   (`PATCH .../reports/{id}`) bila laporan tidak berdasar.
3. Tab **Ruangan** → `GET /chat/rooms` → pilih ruang → `GET .../messages`.
4. Untuk menjawab/berinteraksi: `POST .../rooms/{room}/messages` dengan `body`.
   → Pengumuman di ruang `global` menjangkau **semua pengguna**; pembobotan
   `unread` masing-masing pengguna langsung naik.

#### cURL

```bash
# Ruangan chat
curl -H "X-Integration-Key: $KEY" "$BASE/chat/rooms"

# Pesan ruang 1 (pemetaan polling after=id terakhir)
curl -H "X-Integration-Key: $KEY" "$BASE/chat/rooms/1/messages?after=0"

# Kirim pengumuman admin ke komunitas
curl -X POST -H "X-Integration-Key: $KEY" -H "Content-Type: application/json" \
  -d '{"body":"Jam maintenance Sabtu 22.00 WIB."}' \
  "$BASE/chat/rooms/1/messages"

# Laporan terbuka
curl -H "X-Integration-Key: $KEY" "$BASE/chat/reports"

# Moderasi pesan 118
curl -X PATCH -H "X-Integration-Key: $KEY" -H "Content-Type: application/json" \
  -d '{"reason":"Berisi kata kasar yang melanggar aturan."}' \
  "$BASE/chat/messages/118/moderate"

# Tutup laporan 3
curl -X PATCH -H "X-Integration-Key: $KEY" -H "Content-Type: application/json" \
  -d '{}' \
  "$BASE/chat/reports/3"
```

---

### 6.14. Notifikasi — (opsional, pelengkap)

Backend juga menyediakan API notifikasi untuk menampilkan riwayat notifikasi
pengguna atau mengirim pengumuman:

| Method | Path | Fungsi |
| --- | --- | --- |
| `GET` | `/api/v1/users/{user}/notifications?limit=` | Riwayat notifikasi in-app suatu akun + jumlah `unread`. |
| `POST` | `/api/v1/users/{user}/notify` | Kirim notifikasi in-app ke satu akun. |

Body `POST .../notify`: `{ "title": "...", "body": "...", "type"?: "...", "user_id"?: int }`.
Bila `user_id` dikirim, dikirim ke akun itu; tanpa `user_id` → broadcast ke
semua akun aktif. Berguna untuk fungsi "pengumuman" bila alur chat belum dipakai.

---

## 7. Model Data Klien (Dart / Flutter)

```dart
class AkunUser {
  final int id;
  final String name;
  final String email;
  final DateTime? emailVerifiedAt;
  final String status; // 'aktif' | 'suspended'
  final String plan;   // 'free' | 'pro'   (default toleran: 'free' bila kosong)
  final DateTime? createdAt;
  final DateTime? updatedAt;

  // factory dari JSON; toJson() untuk keperluan log/edit
}

class InfoPaket {
  final String nama;
  final int batasBarang; // 9223372036854775807 (pro) → tampilkan "Tak terbatas"

  // factory fromJson(Map<String,dynamic>)
}

class PaketAkun {
  final String plan;         // efektif ('free'|'pro')
  final bool isPro;
  final DateTime? trialEndsAt;
  final DateTime? planExpiresAt;

  // factory fromJson(...)
}

class Donasi {
  final int id;
  final MiniAkun user;        // { id, name, email }
  final double? nominal;      // null = "seikhlasnya"
  final String? keterangan;
  final String status;        // 'pending' | 'confirmed'
  final String? buktiUrl;
  final DateTime createdAt;
  final DateTime updatedAt;

  // factory dari JSON
}

class RuangChat {
  final int id;
  final String tipe;          // 'global' | 'dm'
  final MiniAkun? userA;
  final MiniAkun? userB;
  final DateTime? terakhirPesan;

  // factory dari JSON
}

class PesanChat {
  final int id;
  final int? senderId;
  final String? sender;       // 'Admin' untuk type admin
  final String type;          // 'user' | 'admin'
  final String body;
  final bool moderated;
  final DateTime? moderatedAt;
  final DateTime createdAt;

  // factory dari JSON
}
```

Tipe status disarankan enum `StatusAkun { aktif, suspended }`, tipe pesan enum
`TipePesan { user, admin }`, dan tipe room enum `TipeRuang { global, dm }`
dengan mapping parser yang **toleran terhadap nilai tak dikenal** (default
`suspended→aktif`, `admin→user`) agar klien tetap aman.

---

## 8. Spesifikasi UI (Flutter)

### 8.1. Struktur Navigasi

- **Sidebar/navigasi utama:** Kelola Akun · Donasi · Chat · Paket.
- Alur pindah halaman antar-device bersifat stateless (data dari server);
  pastikan tiap halaman **melakukan refresh** saat dibuka (pull-to-refresh
  opsional) agar multi-device sinkron.

### 8.2. Halaman Daftar Akun (`GET /api/v1/users`)

- Input pencarian (debounce ±300 ms) → `q`.
- `ListView`/`GridView` dengan pagination: **infinite scroll** memakai
  `meta.current_page < meta.last_page`.
- Setiap baris/kartu menampilkan:
  - nama + email,
  - badge status (`aktif` = hijau, `suspended` = merah/kuning),
  - badge kecil "belum verifikasi email" bila `email_verified_at` kosong
    (umumnya user yang daftar mandiri web; lihat §12.9),
  - badge paket kecil (`PRO` biru / `FREE` abu) bila perlu,
  - waktu dibuat.
- Aksi per baris: **Detail**, **Ubah**, **Suspend/Aktifkan**, **Hapus**.
- Empty state ("Belum ada akun") dan error state dengan tombol ulangi.

### 8.3. Halaman Buat Akun (`POST /api/v1/users`)

- Field: Nama, Email, Password, Konfirmasi Password.
- Validasi klien mengikuti aturan server (email valid & unik, password
  min-8-karakter + confirmation match).
- Saat `201`: tampilkan snackbar sukses, kembali ke daftar, dan **refresh**.
- Saat `422`: tampilkan pesan error **per field** dari `errors`.

### 8.4. Halaman Detail / Ubah Akun (`GET/PUT` + `PATCH status` + `PATCH plan`)

- Menampilkan seluruh atribut akun (termasuk status verifikasi email
  `email_verified_at`, paket `plan`, dan waktu tayang terakhir).
- **Kartu Paket Langganan** (`GET /plans?user_id={id}`):
  - tampilkan paket efektif (`plan`, `is_pro`, `plan_expires_at`),
  - tombol **Jadikan Pro** / **Turunkan ke Gratis** → dialog konfirmasi →
    `PATCH /users/{id}/plan` (biarkan `plan_expires_at` kosong bila berbayar
    permanen, atau isi tanggal kadaluarsa Pro),
  - setelah sukses: snackbar + refresh kartu (pengguna otomatis dinotifikasi).
- Form ubah: Nama + Email, tombol "Ganti Password" (opsional, munculkan dua
  field password saat dipilih).
- Tombol **Bekukan / Aktifkan** (toggle) dengan dialog konfirmasi yang
  menjelaskan efek: akun yang dibekukan **kehilangan akses login web dan
  keluar dari semua sesi**.
- Tombol **Hapus** (berwarna merah) dengan dialog konfirmasi destruktif;
  tombol hapus dinonaktifkan bila akun yang tersisa = 1 (hanya di sisi
  server, terakhir ditolak `422`).

### 8.5. Perilaku Multi-Device

- Tidak ada data lokal yang diandalkan: daftar/mutu selalu dari server.
- Aksi yang dilakukan device A terlihat di device B setelah **refresh**.
- Deteksi "ada user baru" memakai **pola polling**; algoritma lengkap,
  interval, state lokal, dan penanganan kasus tepi ada di §12.

### 8.6. Halaman Donasi

- Tab filter: **Semua** / **Pending** / **Confirmed** → `GET /api/v1/donasi?status=`.
- Daftar kartu donasi: nama & email penyumbang, nominal (atau "Seikhlasnya"),
  status badge, waktu; tap → **Detail**.
- Detail (`GET /api/v1/donasi/{id}`): tampilkan `bukti_url` (gambar
  didownload polos), `keterangan`, `nominal`.
- Aksi untuk status `pending`: tombol **Konfirmasi** → dialog konfirmasi →
  `PATCH /donasi/{id}/status` → `{ "status": "confirmed" }`; snackbar sukses
  (pengguna otomatis dinotifikasi). Bila donasi sekaligus permintaan Pro,
  lanjutkan ke §8.4/§8.7 untuk menaikkan paket.
- Pencarian `q` berdasarkan nama/email penyumbang.

### 8.7. Halaman Paket (opsional, pengelompokan)

Alternatif bila tidak ingin menggabungkan kartu paket di §8.4: halaman terpisah
"Paket" yang menampilkan matriks paket (`GET /api/v1/plans`) dan memilih akun
untuk diubah paketnya.

### 8.8. Halaman Chat

- **Tab Ruangan** (`GET /api/v1/chat/rooms`):
  - daftar ruang `global` lalu `dm`, menampilkan tipe & lawan bicara;
  - tap → tampilan percakapan (§ di bawah);
  - ruang `global` diberi label "Komunitas Semua Pengguna" agar jelas bahwa
    pesan di sana dilihat semua akun.
- **Tampilan percakapan** (`GET /api/v1/chat/rooms/{id}/messages?after=`):
  - polling ringan untuk memuat pesan baru (sejalan anggaran §12.6),
  - pesan `moderated=true` disembunyikan / diganti placeholder,
  - pesan `type=admin` ditandai sebagai "Admin" (dia yang Anda kirim bila
    berasal dari aplikasi ini).
- **Komposer pesan** (`POST /api/v1/chat/rooms/{id}/messages`):
  - field `body`, kirim via tombol;
  - tampilkan pesan yang baru terkirim di akhir percakapan;
  - di ruang `global` tampilkan peringatan: "Pesan akan terlihat oleh SEMUA
    pengguna aplikasi Keuangan."
- **Tab Laporan** (`GET /api/v1/chat/reports`):
  - daftar laporan `open` dengan isi pesan & alasan;
  - aksi per laporan: **Moderasi** (`PATCH .../moderate` + isi alasan) atau
    **Tutup** (`PATCH .../reports/{id}`).

---

## 9. Penanganan Error di Klien

| Kode | Perilaku UI |
| --- | --- |
| `401` | Tampilkan layar "Kunci integrasi salah" (kemungkinan kunci berubah/perangkat tak sah), jangan ulangi otomatis. |
| `404` | "Akun/donasi/ruang tidak ditemukan" → kembali daftar + refresh. |
| `422` | Tampilkan `errors` per field di form tsb. |
| `429` | Nonaktifkan tombol + tampil "Terlalu banyak permintaan, coba lagi sebentar" (backoff eksponensial); untuk polling, hentikan sementara & lanjut di timer berikutnya (lihat §12.6). |
| `network/timeout` | Pesan "Tidak ada koneksi"; tombol ulangi. |
| `500` | Pesan umum "Terjadi kesalahan server". |

---

## 10. Keamanan

- **Kunci integrasi** adalah rahasia bersama backend⇄app-admin. Simpan di
  *secure storage* device (mis. `flutter_secure_storage` / Keychain-Keystore),
  **bukan** di kode/source control.
- Hanya boleh dikirim lewat **HTTPS** di lingkungan non-dev.
- Berikan kunci berbeda per lingkungan (dev/staging/prod).
- Rotasi kunci = ubah `USER_MANAGEMENT_API_KEY` backend lalu perbarui semua
  device admin.
- **Batasan yang perlu direncanakan:** API ini memakai satu kunci (belum ada
  otorisasi per-admin). Jika nanti butuh pembatasan per-orang/role, PRD lanjutan
  dapat menambahkan daftar "kunci integrasi" milik masing-masing administrator
  (platform backend menyediakan kolom baru di `users` atau tabel khusus).
- **Moderasi chat adalah tindakan destruktif terhadap pengalaman publik** —
  pastikan dialog konfirmasi menyebutkan bahwa pesan yang dimoderasi
  disembunyikan dari semua pengguna dan pengirim dinotifikasi.

---

## 11. Lingkup Luar / Versi Berikutnya

- Login & rekap per "siapa" mengubah akun (audit trail pada akun).
- Ubah pola polling menjadi **push realtime** (webhook/listener event
  `Registered` di backend + FCM) bila notifikasi instan lintas-device
  dibutuhkan; **baseline saat ini tetap polling** (§12).
- **Obrolan dukungan 1-kepada-1 admin↔pengguna** sebagai ruang tersendiri
  (saat ini admin menyapa lewat ruang komunitas `global` atau membalas ruang
  `dm` dua pihak yang sudah ada; ruang khusus "Admin↔User" belum ada).
- Read receipts "sudah dibaca" lintas-device & per-admin (snapshot tetap
  per-device, lihat §12.2).
- Multi-key per admin (lihat §10).
- Sinkronisasi data akun ke sistem eksternal lain.
- Manajemen tenant detail (reset kata sandi dari sisi admin tanpa klik tautan
  email — saat ini reset tetap lewat alur web).
- Karena pembuatan akun sudah meng-verifikasi email, admin tidak dikirimkan
  email konfirmasi; laporkan kredensial awal melalui kanal yang disepakati.

---

## 12. Sinkronisasi & Deteksi User Baru (Pola Polling)

Pola yang diadopsi: **tidak ada push/webhook**. Admin app aktif "bertanya" ke
API secara berkala. Konsekuensi: **nol perubahan di backend** — API yang sudah
ada di §6 cukup untuk seluruh kebutuhan ini.

### 12.1. Prinsip kerja

1. Akun baru selalu mendapat `id` yang **bertambah naik** (auto-increment).
   Jadi "user baru" = akun dengan `id` lebih besar dari nilai terakhir yang
   sudah diketahui perangkat ini.
2. Daftar `GET /api/v1/users` diurutkan `id` **naik (tertua dulu)** dan **tidak
   bisa diminta urutan terbalik** dari API. Konsekuensi penting: user terbaru
   selalu berada di **halaman terakhir**, bukan halaman pertama.
3. Karena itu deteksi memakai **`meta.total`** (panggilan sangat murah), dan
   pengambilan entri baru memakai teknik **mundur per halaman** (§12.4).

### 12.2. State lokal per device

Simpan per perangkat admin (mis. `SharedPreferences` atau
`flutter_secure_storage`):

| Kunci | Tipe | Makna |
| --- | --- | --- |
| `lastSeenMaxId` | `int` (awal `0`) | `id` tertinggi yang sudah pernah ditampilkan perangkat ini |
| `lastSeenTotal` | `int` | `meta.total` pada sinkronisasi terakhir |
| `lastSyncAt` | `DateTime?` | waktu sinkronisasi terakhir |

> **Penting:** state ini **per device**. Dua device admin tidak saling tahu
> badge yang sudah dibaca device lain — setiap device punya badge sendiri.
> Sinkronisasi "sudah dibaca" lintas-device membutuhkan backend dan masuk
> lingkup luar (§11).

### 12.3. Deteksi cepat (badge) — dipanggil berkala

1. Panggil `GET /api/v1/users?per_page=1` — payload seminimal mungkin; hanya
   `meta` yang dibutuhkan.
2. Bandingkan `meta.total` dengan `lastSeenTotal`:
   - `total > lastSeenTotal` → tampilkan badge **"N akun baru"**, dengan
     `N = max(0, total − lastSeenTotal)`.
   - `total ≤ lastSeenTotal` → tanpa badge.
3. Snapshot **belum** diperbarui pada langkah ini; baru diperbarui setelah
   admin benar-benar melihat daftar baru (§12.4).

> Catatan: perbandingan total adalah indikasi cepat. Ia sedikit "kotor" bila
> ada akun yang dihapus (total turun). Verifikasi final yang akurat memakai
> perbandingan `id` pada §12.4.

```http
GET /api/v1/users?per_page=1
X-Integration-Key: <kunci>

# → gunakan meta.total, abaikan sisanya
```

### 12.4. Membuka "Lihat akun baru" — mengambil entri yang benar-benar baru

Karena daftar naik (tertua dulu), lakukan **mundur per halaman** sampai tidak
ada lagi akun yang lebih baru dari `lastSeenMaxId`:

```
B   = ukuran halaman (mis. 20)
all = GET /users?per_page=B            # baca meta.total & meta.last_page
baru = []

untuk page berpindah dari lastPage ke 1:
    res = GET /users?per_page=B&page=page
    untuk u di urutan res.data DARI PALING AKHIR:
        jika u.id > lastSeenMaxId: baru.tambahkan(u)
        lainnya: hentikan loop-data
    jika res.data[0].id <= lastSeenMaxId: hentikan loop-halaman
    # ^ seluruh sisa halaman lebih lama; berhenti

tampilkan "baru" (sudah otomatis berurut naik id)

# Setelah admin menutup / me-refresh:
lastSeenMaxId = baru.isEmpty ? lastSeenMaxId : baru.last.id
lastSeenTotal = all.meta.total
```

Simplifikasi untuk timbul halaman yang minim: bisa mulai dari
`per_page=1&page=last_page` (= 1 payload 1 item) untuk mencicipi id tertinggi,
lalu hitung berapa entri yang lebih baru dari `lastSeenMaxId` sebelum menarik
seluruh blok.

### 12.5. Kapan polling berjalan

| Momen | Aksi |
| --- | --- |
| App dibuka & masuk foreground (`AppLifecycleState.resumed`) | Jalankan deteksi langsung, lalu mulai Timer berkala |
| App di foreground | Timer **setiap ≥ 60 detik** |
| App ke background/paused | **Hentikan Timer** (hemat baterai & kuota rate limit) |
| Pull-to-refresh pada halaman daftar | Jalankan deteksi + refresh daftar |
| Setelah aksi tulis sukses (buat/ubah/suspend/hapus) | Perbarui snapshot sesuai §12.7 supaya badge tidak menyesatkan |

### 12.6. Anggaran rate limit (wajib dipahami)

- Limit API: **60 request/menit per IP** (`throttle:60,1`) — lebih dari itu
  dikembalikan `429`.
- Polling 60 detik = **1 request/menit** + beberapa halaman saat membuka
  daftar. Sangat aman untuk satu perangkat.
- Fitur chat (polling pesan `after=`) dan notifikasi memakai anggaran yang sama
  — gabungan semua polling per menit harus dihitung bersama.
- **Risiko bersama:** beberapa perangkat admin pada satu IP publik (satu
  kantor/NAT) ikut berbagi kuota 60/mnt. Contoh: 10 device × 1 req/mnt +
  browsing = nyaman; 30 device × 1 req/mnt + browsing bisa mendekati batas.
- **Aturan praktis:** jangan polling lebih cepat dari 60 detik, dan jangan
  memaksa dua tab chat di-poll bersamaan. Bila `429` muncul saat polling:
  jangan ulangi otomatis, tampilkan pesan sesuai §9 dan tunda beberapa menit
  (backoff), lalu lanjut di timer berikutnya.

### 12.7. Pemutakhiran snapshot setelah aksi tulis

| Aksi | Istirahat snapshot setelah sukses |
| --- | --- |
| Buat akun (`201`) | `lastSeenTotal += 1`; `lastSeenMaxId = max(lastSeenMaxId, id)` |
| Ubah identitas / ubah status / ubah paket (`200`) | tidak berubah (total & id tetap) |
| Hapus akun (`200`) | `lastSeenTotal = max(0, lastSeenTotal − 1)` |

Ini membuat badge konsisten: akun yang baru saja dibuat sendiri tidak akan
dianggap "user baru" oleh device yang melakukan pembuatannya.

### 12.8. Kasus tepi

| Kasus | Penanganan |
| --- | --- |
| Total turun karena akun dihapus device lain | `N` bisa negatif → **clamp ke 0**; badge hanya muncul saat positif; akun baru tetap tertangkap lewat perbandingan `id` di §12.4 |
| Beberapa akun baru sekaligus dalam 1 interval | Tertangkap oleh diff `total` (badge N) dan loop mundur (daftar lengkap) |
| `id` melompat (buat via API, rollback DB) | Tidak masalah — memakai `>` bukan asumsi kontiguitas angka |
| Daftar sedang difilter `q` | Badge dihitung dari `total` **tanpa filter**; `q` hanya untuk pencarian manual |
| Polling gagal (offline/network error) | Tampilkan state error / jangan tampilkan badge; snapshot **hanya** diubah saat panggilan sukses; coba lagi pada timer berikutnya |
| Halaman di luar `last_page` (akun dihapus saat ambil) | Loop mundur berhenti alami karena `res.data` kosong / kondisi `id` |
| Satu akun baru beberapa device | Tiap device punya snapshot sendiri; badge independen (non-nego, konsekuensi pola pull) |

### 12.9. Status verifikasi email — data tambahan yang berguna bagi admin

- Akun **daftar mandiri web** → `email_verified_at` **null** sampai user
  mengklik tautan verifikasi email.
- Akun **dibuat via API** (`POST /api/v1/users`) → `email_verified_at`
  **terisi** sejak awal (auto-verified).
- Gunakan di UI: badge kecil "belum verifikasi email" di baris daftar dan di
  halaman detail — membantu admin melakukan follow-up, misalnya menghubungi
  user yang belum mengaktifkan emailnya.

### 12.10. Pseudocode implementasi (Dart)

```dart
// Asumsi: api.users({perPage, page, q}) → UsersResult { List<AkunUser> data, MetaData meta }
class UserSyncService {
  final ApiClient _api;
  final Future<SharedPreferences> _prefs;

  // Deteksi cepat: berapa user baru sejak terakhir dilihat.
  Future<int> jumlahBaru() async {
    final res = await _api.users(perPage: 1);
    final total = res.meta.total;
    final lama = (await _prefs).getInt('lastSeenTotal') ?? 0;
    return total > lama ? total - lama : 0; // clamp non-negatif
  }

  // Ambil entri yang benar-benar baru, lalu perbarui snapshot.
  Future<List<AkunUser>> ambilBaru() async {
    const b = 20;
    final prefs = await _prefs;
    final lastId = prefs.getInt('lastSeenMaxId') ?? 0;
    final meta = (await _api.users(perPage: b)).meta;

    final baru = <AkunUser>[];
    for (var page = meta.lastPage; page >= 1; page--) {
      final res = await _api.users(perPage: b, page: page);
      if (res.data.isEmpty) break;
      for (final u in res.data.reversed) {
        if (u.id > lastId) {
          baru.add(u);
        } else {
          break;
        }
      }
      if (res.data.first.id <= lastId) break; // sisa halaman semuanya lama
    }

    if (baru.isNotEmpty) {
      await prefs.setInt('lastSeenMaxId', baru.last.id);
      await prefs.setInt('lastSeenTotal', meta.total);
    }
    return baru;
  }

  // Panggil dari Timer 60 dtk saat foreground:
  //   if (await jumlahBaru() > 0) -> tampilkan badge tanpa sinkron penuh.
}
```

> **Catatan chat:** gunakan pola polling yang sama untuk daftar pesan
> `GET /chat/rooms/{id}/messages?after=<idTerakhir>` — simpan `idTerakhir` per
> ruang, dan perhitungan anggaran gabungan lihat §12.6.

---

## 13. Kriteria Penerimaan (Acceptance Criteria)

1. Aplikasi Flutter build & jalan di ≥ 2 platform (mis. Android + Windows).
2. Login device admin sekadar menyimpan/menganbil kunci integrasi (secure
   storage) dan seluruh flow memakai header `X-Integration-Key`.
3. Daftar akun menampilkan pagination `meta` dan pencarian `q`; sinkron antar
   device setelah refresh.
4. Membuat akun berhasil (201) ⇒ akun muncul di daftar, `status=aktif`,
   `plan=pro`, dan langsung dapat login di aplikasi web.
5. Email duplikat & password tak memenuhi aturan ⇒ `422` dengan pesan per field.
6. Suspend sebuah akun yang sedang login di web ⇒ ia keluar (redirect login)
   dan dilarang login ulang; aktifkan kembali ⇒ bisa login lagi.
7. Akun terakhir tidak dapat dihapus (server mengembalikan 422 dan klien
   menunjukkan pesan).
8. Rate limit: melebihi 60 req/menit ⇒ klien menampilkan pesan & backoff.
9. Deteksi polling: saat ada akun baru (daftar mandiri web **atau** dibuat
   via API), admin app menampilkan badge jumlah akun baru pada halaman daftar.
10. Menekan "Lihat akun baru" menampilkan hanya akun dengan `id` lebih besar
    dari yang terakhir dilihat perangkat; setelah dibuka, snapshot perangkat
    diperbarui dan badge hilang.
11. Pembuatan akun sendiri oleh device admin tidak membuat badge "user baru"
    di device yang sama (snapshot diperbarui seketika, §12.7).
12. **Donasi:** daftar donasi `pending` menampilkan bukti & nominal; setelah
    admin mengonfirmasi (`PATCH`, `201`), status berubah `confirmed` dan
    pengguna menerima notifikasi `donation.status`.
13. **Paket:** halaman detail akun menampilkan paket efektif; mengubah ke
    `pro`/`free` (`PATCH /users/{id}/plan`) langsung tercermin di respon
    (`is_pro`) dan pengguna menerima notifikasi `plan.change`; kuota barang
    aplikasi web menyesuaikan.
14. **Chat:** admin melihat daftar ruang (`global`+`dm`) dan isi pesan; mengirim
    pesan ke ruang komunitas ⇒ terlihat berlabel "Admin" di aplikasi web
    seluruh pengguna; moderasi pesan ⇒ tersembunyi dari publik & pengirim
    dinotifikasi; laporan tertutup setelah ditindak.

---

## 14. Matriks Endpoint (Cepat)

| Modul | Metode | Path | Fungsi |
| --- | --- | --- | --- |
| Akun | `GET` | `/users` | Daftar + cari (`q`, `per_page`) |
| Akun | `POST` | `/users` | Buat (verifikasi + tenant + `plan=pro`) |
| Akun | `GET` | `/users/{id}` | Detail |
| Akun | `PUT` | `/users/{id}` | Ubah nama/email (password opsional) |
| Akun | `PATCH` | `/users/{id}/status` | `aktif` / `suspended` |
| Akun | `DELETE` | `/users/{id}` | Hapus (terakhir ditolak) |
| Paket | `GET` | `/plans` | Matriks paket + status akun (`?user_id=`) |
| Paket | `PATCH` | `/users/{id}/plan` | Set `free`/`pro` (+ tanggal) |
| Donasi | `GET` | `/donasi` | Daftar (`?status=`, `?q=`, `?per_page=`) |
| Donasi | `GET` | `/donasi/{id}` | Detail |
| Donasi | `PATCH` | `/donasi/{id}/status` | `pending` / `confirmed` |
| Chat | `GET` | `/chat/rooms` | Daftar ruangan (`?limit=`) |
| Chat | `GET` | `/chat/rooms/{room}/messages` | Pesan (`?after=`) |
| Chat | `POST` | `/chat/rooms/{room}/messages` | **Kirim pesan Admin** |
| Chat | `GET` | `/chat/reports` | Antrean laporan |
| Chat | `PATCH` | `/chat/messages/{message}/moderate` | Moderasi pesan |
| Chat | `PATCH` | `/chat/reports/{report}` | Tutup laporan |
| Notif | `GET` | `/users/{user}/notifications` | Riwayat notifikasi |
| Notif | `POST` | `/users/{user}/notify` | Kirim notifikasi (1 akun / broadcast) |