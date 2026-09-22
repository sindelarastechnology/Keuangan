---
paths:
  - app/Models/Barang.php
  - app/Models/Penjualan.php
  - 'app/Models/**'
---

# Models

## Kelas warna avatar dinamis harus di-safelist Tailwind
Palet `avatarWarna` (bg-sky-600, bg-violet-600, dll.) hanya berupa string di PHP sehingga tidak di-scan Tailwind (isi content hanya blade). Jika menambah/mengubah warna avatar, perbarui `safelist: [{ pattern: /^bg-(emerald|sky|violet|amber|rose|cyan|indigo|teal)-600$/ }]` di tailwind.config.js lalu `npm run build`, jika tidak warnanya tidak akan tampil di CSS.

## Cast tanggal date pada Penjualan/Pembelian
Penjualan & Pembelian WAJIB cast 'tanggal' => 'date' di model. Data diambil dari header (dataDariHeader*) memakai ->toDateString(); tanpa cast fail di PHP 8.3. formatTanggal() di helpers tetap aman karena Carbon::parse.

## Cross-tenant models bypass BelongsToUser
Exception: cross-tenant models (ChatRoom, ChatMessage, ChatRoomRead, ChatReport, Notification/DatabaseNotification) must NOT use the BelongsToUser trait — their rows belong to multiple users / no single owner. They use morphs (chat_rooms/user_a_id+user_b_id, notifications.notifiable morph) or composite keys instead. Global chat rooms are created by the 'komunitas' user (OrangSistem). Only plain tenant tables (barang, produk, jasa, satuan, ...) use BelongsToUser.

## PlanService fallback free; semua akun baru default Pro
Anyone who can use the app must always get an accessible plan chip/badge in the top navbar regardless of plan — ALWAYS fall back to 'free' in PlanService::plan() so nobody gets null/confusing "undefined". Sejak kebijakan utang dirapikan, DB default (add_plan migration), factory User, registrasi web, login Google, dan API /api/v1/users semuanya 'pro' — hanya akun legacy bernilai NULL yang tampil 'free'. Migrasi jangan menurunkan default ke 'free'; lanjutkan dengan config plans.default='pro' (DefaultPlanProTest menjaga invariant ini).
