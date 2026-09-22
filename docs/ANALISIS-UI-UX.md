# Analisis UI/UX — Antarmuka Pengguna dan Rekomendasi Perbaikan

> **Proyek:** Sistem Pengelolaan Keuangan Semi-Akuntansi  
> **Stack Frontend:** Blade · Tailwind CSS 3/4 · Alpine.js 3 · Vite 8 · Figtree Font  
> **Tanggal analisis:** 7 September 2026  
> **Total views:** ~98 file Blade (aplikasi)  
> **Catatan:** Dokumen ini bersifat **read-only** — tidak ada kode yang diubah saat analisis.

---

## Daftar Isi

1. [Ringkasan Eksekutif](#1-ringkasan-eksekutif)
2. [Arsitektur Layout](#2-arsitektur-layout)
3. [Design System Saat Ini](#3-design-system-saat-ini)
4. [Inventaris Halaman](#4-inventaris-halaman)
5. [Pola Form](#5-pola-form)
6. [Pola Tabel dan List](#6-pola-tabel-dan-list)
7. [Laporan dan PDF](#7-laporan-dan-pdf)
8. [Responsivitas Mobile](#8-responsivitas-mobile)
9. [User Feedback dan Interaksi](#9-user-feedback-dan-interaksi)
10. [Aksesibilitas (A11y)](#10-aksesibilitas-a11y)
11. [Inkonsistensi Antar Modul](#11-inkonsistensi-antar-modul)
12. [JavaScript dan Alpine.js](#12-javascript-dan-alpinejs)
13. [Print dan PDF](#13-print-dan-pdf)
14. [Bug UI Teridentifikasi](#14-bug-ui-teridentifikasi)
15. [Rekomendasi UI/UX Ideal](#15-rekomendasi-uiux-ideal)
16. [Design System Target](#16-design-system-target)
17. [Wireframe dan Layout Target](#17-wireframe-dan-layout-target)
18. [Roadmap Implementasi UI/UX](#18-roadmap-implementasi-uiux)
19. [Checklist Per Halaman](#19-checklist-per-halaman)
20. [Referensi File UI](#20-referensi-file-ui)

---

## 1. Ringkasan Eksekutif

### Kondisi Saat Ini

Sistem memiliki **fondasi UI yang solid** dengan sidebar navigasi, komponen Blade reusable, dan palet warna emerald/slate yang konsisten di sebagian besar halaman. Namun, terdapat **dua "bahasa desain"** yang berbeda — aplikasi utama (emerald, rounded) vs halaman auth/profile Breeze (indigo, uppercase).

### Skor UX (Estimasi)

| Aspek | Skor | Catatan |
|-------|------|---------|
| Navigasi | 7/10 | Sidebar lengkap, bottom nav terbatas |
| Konsistensi visual | 6/10 | Dua design language |
| Form usability | 7/10 | Alpine.js dynamic forms bagus |
| Mobile experience | 5/10 | Tabel lebar, touch target kecil |
| Feedback pengguna | 5/10 | Flash OK, tidak ada loading state |
| Aksesibilitas | 4/10 | Banyak icon tanpa label |
| Laporan | 6/10 | Web + PDF, tidak ada export Excel |
| **Rata-rata** | **5.7/10** | Perlu polish signifikan |

### Top 8 Prioritas Perbaikan UI/UX

| # | Prioritas | Dampak |
|---|-----------|--------|
| 1 | Fix duplicate `#menu-overlay` | Bug mobile menu |
| 2 | Unifikasi design system (emerald) | Konsistensi visual |
| 3 | Aksesibilitas icon actions | Inklusivitas |
| 4 | Mobile card layout untuk tabel | Usability mobile |
| 5 | Loading state pada submit | Feedback |
| 6 | Modal konfirmasi (ganti `confirm()`) | UX modern |
| 7 | Standardisasi validasi form | Konsistensi error |
| 8 | Profile page alignment | Konsistensi layout |

---

## 2. Arsitektur Layout

### 2.1 Struktur Layout Utama

```
┌─────────────────────────────────────────────────────┐
│  SIDEBAR (fixed, 264px, dark slate-900)             │
│  ┌─────────────┐  ┌──────────────────────────────┐  │
│  │ Logo        │  │ TOPBAR (sticky, white)       │  │
│  │             │  │ [☰] Page Title    Periode 👤 │  │
│  │ Menu groups │  ├──────────────────────────────┤  │
│  │ - Utama     │  │                              │  │
│  │ - Master    │  │ MAIN CONTENT                 │  │
│  │ - Transaksi │  │ [Alert flash messages]       │  │
│  │ - Inventori │  │ {{ $slot }}                  │  │
│  │ - Akuntansi │  │                              │  │
│  │ - Laporan   │  │                              │  │
│  │ - Pengaturan│  │                              │  │
│  │             │  ├──────────────────────────────┤  │
│  │ [Logout]    │  │ BOTTOM NAV (mobile only)     │  │
│  └─────────────┘  └──────────────────────────────┘  │
└─────────────────────────────────────────────────────┘
```

### 2.2 File Layout

| File | Fungsi | Breakpoint |
|------|--------|-----------|
| `resources/views/layouts/app.blade.php` | Shell utama authenticated | All |
| `resources/views/layouts/sidebar.blade.php` | Navigasi kiri | Hidden < lg, slide-in mobile |
| `resources/views/layouts/topbar.blade.php` | Header sticky | All |
| `resources/views/layouts/bottom-nav.blade.php` | Nav bawah mobile | Visible < lg |
| `resources/views/layouts/guest.blade.php` | Auth pages | Centered card |
| `resources/views/layouts/navigation.blade.php` | **Legacy Breeze — TIDAK DIPAKAI** | — |

### 2.3 Navigasi Sidebar

**File:** `resources/views/layouts/sidebar.blade.php`

| Grup | Jumlah Item | Icon |
|------|------------|------|
| Menu Utama | 1 (Dashboard) | Heroicons SVG inline |
| Master Data | 8 | Heroicons SVG inline |
| Transaksi | 5 | Heroicons SVG inline |
| Inventori | 4 | Heroicons SVG inline |
| Akuntansi | 7 | Heroicons SVG inline |
| Laporan | 2 | Heroicons SVG inline |
| Pengaturan | 1 | Heroicons SVG inline |
| **Total** | **28 menu items** | |

**Active state:** `request()->routeIs($route . '*')` → `bg-emerald-600 text-white`

### 2.4 Bottom Navigation (Mobile)

**File:** `resources/views/layouts/bottom-nav.blade.php`

| Tab | Route | Icon |
|-----|-------|------|
| Dashboard | `/dashboard` | Home |
| Kas Masuk | `/kas-masuk/create` | Arrow down |
| **Transaksi (FAB)** | Toggle sidebar | Plus (elevated) |
| Kas Keluar | `/kas-keluar/create` | Arrow up |
| Akun | `/pengaturan` | Settings |

**Masalah:** Hanya 4 dari 28 modul accessible via bottom nav. FAB "Transaksi" hanya buka sidebar, bukan shortcut ke modul transaksi.

### 2.5 Masalah Layout

| # | Masalah | File | Severity |
|---|---------|------|----------|
| L1 | Duplicate `#menu-overlay` ID | `app.blade.php` L34 + `sidebar.blade.php` L118 | 🔴 Bug |
| L2 | Sidebar toggle pakai inline `onclick` | topbar, sidebar, bottom-nav | 🟡 A11y |
| L3 | Sidebar ~30 items tanpa collapse/search | sidebar.blade.php | 🟡 UX |
| L4 | Profile page pakai Breeze layout lama | profile/edit.blade.php | 🟡 Inkonsistensi |
| L5 | Tidak ada breadcrumb | Semua detail pages | 🟢 Enhancement |
| L6 | `navigation.blade.php` dead code | layouts/navigation.blade.php | 🟢 Cleanup |

---

## 3. Design System Saat Ini

### 3.1 Typography

| Elemen | Class | Font |
|--------|-------|------|
| Body | `font-sans text-sm` | Figtree 400 |
| Page title | `text-xl font-bold text-gray-900` | Figtree 700 |
| Subtitle | `text-sm text-gray-500` | Figtree 400 |
| Table header | `text-xs uppercase tracking-wider text-gray-500` | Figtree 500 |
| Form label | `text-sm font-medium text-gray-700` | Figtree 500 |
| Money positive | `text-emerald-600 font-semibold` | — |
| Money negative | `text-red-600 font-semibold` | — |

**Font loading:** Bunny Fonts CDN (`fonts.bunny.net/css?family=figtree:400,500,600,700`)

### 3.2 Color Palette (De Facto)

| Token | Tailwind | Penggunaan |
|-------|----------|-----------|
| **Primary** | `emerald-600/700` | Buttons, active nav, positive values |
| **Sidebar** | `slate-900` | Background sidebar |
| **Neutral** | `gray-50` → `gray-900` | Cards, tables, text |
| **Danger** | `red-500/600` | Errors, void, hutang |
| **Warning** | `amber-500/600` | Stok menipis, pending |
| **Info** | `blue-500/600` | Tunai badge, invoice PDF |
| **Success** | `emerald-500` | Posted, approved badges |

**⚠️ Tidak ada custom tokens di `tailwind.config.js`** — semua hardcoded utility classes.

### 3.3 Komponen Reusable

**Direktori:** `resources/views/components/`

| Komponen | File | Status | Penggunaan |
|----------|------|--------|-----------|
| Card | `card.blade.php` | ✅ Aktif | Wrapper konten |
| Page Header | `page-header.blade.php` | ✅ Aktif | Title + subtitle + actions |
| Stat Card | `stat-card.blade.php` | ✅ Aktif | Dashboard KPI |
| Alert | `alert.blade.php` | ✅ Aktif | Flash messages |
| Datatable | `datatable.blade.php` | ❌ **Tidak pernah dipakai** | Dead component |
| Modal | `modal.blade.php` | ✅ Aktif | Breeze modal (Alpine) |
| Text Input | `text-input.blade.php` | ✅ Aktif | Form fields |
| Select | `select.blade.php` | ✅ Aktif | Dropdown |
| Textarea | `textarea.blade.php` | ✅ Aktif | Multi-line input |
| Input Label | `input-label.blade.php` | ✅ Aktif | Form labels |
| Input Error | `input-error.blade.php` | ⚠️ Sebagian | Validation errors |
| Primary Button | `primary-button.blade.php` | ⚠️ Breeze style | Auth/profile only |
| Secondary Button | `secondary-button.blade.php` | ⚠️ Breeze style | Auth/profile only |
| Danger Button | `danger-button.blade.php` | ⚠️ Breeze style | Auth/profile only |

### 3.4 Dua Bahasa Desain

| Aspek | App Pages (Mayoritas) | Breeze Pages (Auth/Profile) |
|-------|----------------------|---------------------------|
| Primary color | Emerald 600 | Gray 800 / Indigo 600 |
| Button style | `rounded-lg text-sm` | `uppercase tracking-widest text-xs` |
| Focus ring | Emerald (implicit) | Indigo ring |
| Layout | Sidebar + card | Centered max-w-7xl |
| Language | Indonesia | English ("Log in", "Profile") |
| Form error | `<x-input-error>` atau `@error` | `<x-input-error>` |

---

## 4. Inventaris Halaman

### 4.1 Dashboard

**File:** `resources/views/dashboard.blade.php`

| Elemen | Detail |
|--------|--------|
| KPI cards | 5 stat cards (laba/rugi, saldo rekening, kas masuk/keluar, piutang, hutang) |
| Chart | 6-month cash flow bar chart (CSS divs, bukan library) |
| Recent transactions | Tabel transaksi terbaru |
| Quick links | Link ke laporan laba rugi |

### 4.2 Master Data (8 modul × 2 halaman = 16 views)

| Modul | Index | Form | Fitur Khusus |
|-------|-------|------|-------------|
| Akun Perkiraan | ✅ | ✅ | Hierarki parent-child |
| Rekening | ✅ | ✅ | Saldo awal toggle |
| Supplier | ✅ | ✅ | Search filter |
| Customer | ✅ | ✅ | Search filter |
| Barang | ✅ | ✅ | **Foto, barcode scanner, kolom config, modal satuan/kategori** |
| Jasa | ✅ | ✅ | Foto preview |
| Daftar Harga | ✅ | ✅ + riwayat | Accordion grouped, tier pricing |
| Pajak | ✅ | ✅ | Simple CRUD |

### 4.3 Transaksi (5 modul)

| Modul | Index | Create | Show | PDF | Void |
|-------|-------|--------|------|-----|------|
| Kas Masuk | ✅ | ✅ | ✅ | — | — |
| Kas Keluar | ✅ | ✅ | ✅ | — | — |
| Mutasi Bank | ✅ | ✅ | ✅ | — | — |
| Pembelian | ✅ | ✅ | ✅ | ✅ | ✅ |
| Penjualan | ✅ | ✅ | ✅ | ✅ | ✅ |

### 4.4 Inventori (4 modul)

| Modul | Index | Create | Show | Void |
|-------|-------|--------|------|------|
| Perubahan Stok | ✅ | ✅ | ✅ | ✅ |
| Stok Opname | ✅ | ✅ | ✅ | ✅ |
| Retur Penjualan | ✅ | ✅ | ✅ | ✅ |
| Retur Pembelian | ✅ | ✅ | ✅ | ✅ |

### 4.5 Akuntansi (7 modul)

| Modul | Index | Create | Show | Actions |
|-------|-------|--------|------|---------|
| Jurnal Umum | ✅ | ✅ (manual) | ✅ | Void, Approve, Reject |
| Buku Besar | ✅ (dual mode) | — | — | Filter akun + tanggal |
| BB Piutang | ✅ | — | — | Filter customer |
| BB Hutang | ✅ | — | — | Filter supplier |
| BB Persediaan | ✅ | — | — | Filter barang |
| Periode | ✅ | — | — | Buka/Kunci/Buka-kunci |
| Tutup Buku | ✅ (form) | — | — | Submit close |

### 4.6 Laporan (2 modul)

| Modul | Web View | PDF |
|-------|----------|-----|
| Laba Rugi | ✅ | ✅ |
| Neraca | ✅ | ✅ |

### 4.7 Auth & Profile (8 views)

| Halaman | File | Style |
|---------|------|-------|
| Login | auth/login.blade.php | Breeze guest |
| Register | auth/register.blade.php | Breeze guest |
| Forgot Password | auth/forgot-password.blade.php | Breeze guest |
| Reset Password | auth/reset-password.blade.php | Breeze guest |
| Verify Email | auth/verify-email.blade.php | Breeze guest |
| Confirm Password | auth/confirm-password.blade.php | Breeze guest |
| Profile Edit | profile/edit.blade.php | **Breeze app (TIDAK match app layout)** |
| Profile Partials | profile/partials/*.blade.php | Breeze style |

---

## 5. Pola Form

### 5.1 Pola Standar (Master CRUD)

```html
<x-app-layout>
    <x-page-header title="..." subtitle="..." />
    <x-card>
        <form method="POST" action="...">
            @csrf
            @method('PUT') <!-- jika edit -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="field" value="Label" />
                    <x-text-input id="field" name="field" :value="old('field')" />
                    <x-input-error :messages="$errors->get('field')" />
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <a href="..." class="...">Batal</a>
                <button type="submit" class="bg-emerald-600 ...">Simpan</button>
            </div>
        </form>
    </x-card>
</x-app-layout>
```

### 5.2 Pola Dynamic Line Items (Transaksi)

**Digunakan di:** pembelian, penjualan, kas masuk/keluar, jurnal manual, retur, opname

```html
<div x-data="penjualanForm()">
    <form @submit="syncAll()">
        <template x-for="(item, index) in items" :key="index">
            <tr>
                <td><select x-model="item.barang_id">...</select></td>
                <td><input x-model.number="item.qty" @input="hitungSubtotal(index)"></td>
                <td><input x-model.number="item.harga" @input="hitungSubtotal(index)"></td>
                <td x-text="formatRupiah(item.subtotal)"></td>
                <td><button @click="removeRow(index)" :disabled="items.length === 1">✕</button></td>
            </tr>
        </template>
        <button type="button" @click="addRow()">+ Tambah Baris</button>
        <!-- Hidden inputs synced on submit -->
    </form>
</div>
```

**Alpine factories (inline `<script>`):**
- `penjualanForm()` — ~150 lines
- `pembelianForm()` — ~150 lines
- `kasMasukForm()` / `kasKeluarForm()` — ~100 lines each
- `jurnalForm()` — double-entry balancer
- `barangForm()` — foto preview, modals, scanner
- Retur/opname forms — ~100 lines each

### 5.3 Pola Validasi — INKONSISTEN

| Pola | Digunakan Di | Contoh |
|------|-------------|--------|
| **A: Component** | Master forms | `<x-input-error :messages="$errors->get('field')">` |
| **B: Blade directive** | Transaksi create, pengaturan | `@error('field')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror` |

**Rekomendasi:** Standardisasi ke Pola A (`<x-input-error>`) di semua form.

### 5.4 Conditional Fields (Alpine x-show)

| Form | Kondisi | Field yang muncul |
|------|---------|-------------------|
| Pembelian/Penjualan | `metode_bayar === 'tunai'` | Select rekening |
| Pembelian/Penjualan | `metode_bayar === 'kredit'` | Info supplier/customer |
| Kas Masuk | `kategori === 'piutang'` | Select customer |
| Kas Keluar | `kategori === 'hutang'` | Select supplier |
| Daftar Harga | `entitas === 'supplier'` | Select supplier (hide customer) |
| Rekening | `jenis === 'bank'` | Input nomor rekening |

### 5.5 Modal Inline CRUD

**Hanya di:** `master/barang/form.blade.php`

| Modal | Trigger | Action |
|-------|---------|--------|
| Tambah Satuan | Button → fetch POST | Reload select satuan |
| Tambah Kategori | Button → fetch POST | Reload select kategori |
| Barcode Scanner | Button → Html5Qrcode | Fill barcode field |

**Pola bagus** — pertimbangkan untuk modul lain (tambah supplier/customer dari form transaksi).

---

## 6. Pola Tabel dan List

### 6.1 Pola Index Standar

```html
<x-page-header title="..." >
    <x-slot:actions>
        <a href="{{ route('...create') }}" class="bg-emerald-600 ...">+ Tambah</a>
    </x-slot:actions>
</x-page-header>

<x-card>
    <!-- Filter form (GET) -->
    <form method="GET" class="flex flex-wrap gap-3 mb-4">
        <input name="search" placeholder="Cari..." />
        <input type="date" name="dari" />
        <input type="date" name="sampai" />
        <button type="submit" class="bg-slate-600 ...">Filter</button>
    </form>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr><!-- headers --></tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50"><!-- cells + actions --></tr>
                @empty
                    <tr><td colspan="N" class="text-center py-8 text-gray-400">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $items->links() }}
</x-card>
```

### 6.2 Pagination

**16 halaman index** menggunakan `{{ $model->links() }}` (default Laravel Tailwind pagination).

**Tidak ada pagination:**
- Buku Besar, BB Piutang/Hutang/Persediaan (bisa unbounded)
- Daftar Harga (accordion grouped)
- Dashboard widgets

### 6.3 Filter Capabilities per Modul

| Modul | Search | Date Range | Dropdown Filter | Reset Button |
|-------|--------|-----------|----------------|-------------|
| Barang | ✅ Text | — | Kategori, Satuan, Stok level | ✅ |
| Supplier/Customer | ✅ Text | — | — | ✅ |
| Transaksi (5) | — | ✅ Dari-Sampai | — | ⚠️ Sebagian |
| Inventori (4) | — | ✅ Dari-Sampai | — | ⚠️ Sebagian |
| Jurnal | — | ✅ Dari-Sampai | ✅ Tipe | ✅ |
| Buku Besar | — | ✅ Dari-Sampai | ✅ Akun | ✅ |
| BB Sub-ledger | — | ✅ Dari-Sampai | ✅ Entity | ⚠️ |
| Daftar Harga | — | — | ✅ Entitas, Supplier/Customer | ✅ |

### 6.4 Status Badges

| Status | Warna | Modul |
|--------|-------|-------|
| Posted / Approved | `bg-emerald-100 text-emerald-800` | Jurnal, transaksi |
| Draft | `bg-gray-100 text-gray-600` | Transaksi |
| Pending Review | `bg-amber-100 text-amber-800` | Approval |
| Rejected | `bg-red-100 text-red-800` | Approval |
| Tunai | `bg-blue-100 text-blue-800` | Pembelian/Penjualan |
| Kredit | `bg-purple-100 text-purple-800` | Pembelian/Penjualan |
| Stok Habis | `bg-red-100 text-red-800` | Barang |
| Stok Menipis | `bg-amber-100 text-amber-800` | Barang |

### 6.5 Action Buttons (Icon-Only)

| Action | Icon | Warna | Aria-label |
|--------|------|-------|-----------|
| View | Eye SVG | Gray hover | ❌ Tidak ada |
| Edit | Pencil SVG | Blue hover | ❌ Tidak ada |
| Delete | Trash SVG | Red hover | ❌ Tidak ada |
| Void | X-circle SVG | Red hover | ❌ Tidak ada |
| PDF | Document SVG | Slate hover | ❌ Tidak ada |

**⚠️ Semua action buttons icon-only tanpa `aria-label`** — masalah aksesibilitas serius.

### 6.6 Fitur Unik: Barang Column Config

**File:** `master/barang/index.blade.php`

- Modal "Atur Kolom" untuk show/hide kolom tabel
- Preference disimpan via POST ke `barang.simpan-kolom`
- **Pola bagus** — pertimbangkan untuk tabel transaksi lebar

---

## 7. Laporan dan PDF

### 7.1 Halaman Laporan Web

| Laporan | Filter | Layout |
|---------|--------|--------|
| Laba Rugi | Dari-Sampai (date range) | 2 kolom: Pendapatan \| Beban, net result highlighted |
| Neraca | Sampai (as-of date) | 2 kolom: Aset \| Kewajiban+Modal, balanced check |

**Shared pattern:**
```html
<x-card>
    <form method="GET"><!-- date filters + submit + PDF button --></form>
    <div class="mt-6">
        <h3>{{ setting('nama_perusahaan') }}</h3>
        <!-- account lists with formatRupiah() -->
        <div class="border-t-2 font-bold"><!-- total --></div>
    </div>
</x-card>
```

### 7.2 PDF Templates

| Template | Controller | Accent Color | Font |
|----------|-----------|-------------|------|
| `laporan/pdf/laba-rugi.blade.php` | LabaRugiController | Emerald `#10b981` | DejaVu Sans |
| `laporan/pdf/neraca.blade.php` | NeracaController | Emerald | DejaVu Sans |
| `transaksi/penjualan/invoice.blade.php` | PenjualanController | Blue `#2563eb` | DejaVu Sans |
| `transaksi/pembelian/invoice.blade.php` | PembelianController | — | DejaVu Sans |

**Karakteristik PDF:**
- Standalone HTML + inline CSS (bukan Tailwind)
- Tidak ada shared layout partial
- Brand colors hardcoded per template
- Dibuka di tab baru (`target="_blank"`)

### 7.3 Masalah Laporan

| # | Masalah | Rekomendasi |
|---|---------|-------------|
| R1 | Tidak ada export Excel/CSV | Tambah export button |
| R2 | Tidak ada Arus Kas | Modul laporan baru |
| R3 | Tidak ada Neraca Saldo UI | Tab/mode di buku besar |
| R4 | PDF color ≠ web primary | Unifikasi emerald |
| R5 | Tidak ada print CSS di web | `@media print` hide nav |
| R6 | Tidak ada preview in-app | Embed atau modal preview |

---

## 8. Responsivitas Mobile

### 8.1 Breakpoints yang Digunakan

| Breakpoint | Tailwind | Penggunaan |
|-----------|----------|-----------|
| Default | — | Single column, bottom nav visible |
| `sm:` (640px) | 2-col grids, show email | Form grids |
| `md:` (768px) | 3-col dashboard stats | Dashboard |
| `lg:` (1024px) | Sidebar visible, hide bottom nav | Layout switch |
| `xl:` (1280px) | 5-col dashboard stats | Dashboard |

### 8.2 Yang Sudah Responsive

| Elemen | Pattern | Status |
|--------|---------|--------|
| Sidebar | Off-canvas slide | ✅ |
| Content padding | `px-3 sm:px-6 lg:px-8` | ✅ |
| Form grids | `grid-cols-1 sm:grid-cols-2` | ✅ |
| Page header | `flex-col sm:flex-row` | ✅ |
| Dashboard stats | `grid-cols-2 md:grid-cols-3 xl:grid-cols-5` | ✅ |
| Tables | `overflow-x-auto` wrapper | ⚠️ Functional tapi UX buruk |

### 8.3 Gap Mobile

| # | Gap | Impact | Priority |
|---|-----|--------|----------|
| M1 | Tabel transaksi 7+ kolom | Heavy horizontal scroll | 🔴 |
| M2 | Tidak ada card-list fallback | Sulit scan di phone | 🔴 |
| M3 | Touch target icon actions ~24px | Di bawah minimum 44px | 🔴 |
| M4 | Sidebar 30 items tanpa search | Scroll panjang | 🟡 |
| M5 | Topbar email hidden < sm | Hanya avatar | 🟢 |
| M6 | Daftar harga accordion nested | Complex di narrow | 🟡 |
| M7 | Bottom nav hanya 4 shortcuts | 24 modul unreachable | 🟡 |
| M8 | Form line items table di mobile | Sangat sulit digunakan | 🔴 |

### 8.4 Rekomendasi Mobile

```
Desktop (lg+):     [Sidebar] [Table layout]
Tablet (md-lg):    [Sidebar] [Table with fewer columns]
Mobile (< md):     [Bottom nav] [Card list layout]
```

**Card list pattern untuk mobile:**
```html
<!-- Hidden on desktop, visible on mobile -->
<div class="md:hidden space-y-3">
    @foreach($items as $item)
        <div class="bg-white rounded-lg p-4 shadow-sm border">
            <div class="flex justify-between">
                <span class="font-medium">{{ $item->nomor }}</span>
                <span class="badge">{{ $item->status }}</span>
            </div>
            <div class="text-sm text-gray-500 mt-1">{{ $item->tanggal }}</div>
            <div class="text-emerald-600 font-semibold mt-2">{{ formatRupiah($item->total) }}</div>
            <div class="flex gap-2 mt-3">
                <a href="..." class="flex-1 text-center py-2 bg-gray-100 rounded">Detail</a>
            </div>
        </div>
    @endforeach
</div>
```

---

## 9. User Feedback dan Interaksi

### 9.1 Flash Messages

**Global handler** (`layouts/app.blade.php`):
```php
@if(session('success')) <x-alert type="success" :message="session('success')" /> @endif
@if(session('error'))   <x-alert type="error" :message="session('error')" />   @endif
```

**Alert component** (`components/alert.blade.php`):
- Alpine.js `x-show` + dismiss on click
- Types: success (green), error (red), warning (amber), info (blue)
- Icons per type
- **Tidak auto-dismiss** — user harus klik close
- **Tidak ada `role="alert"`** — screen reader tidak announce

### 9.2 Konfirmasi Destructive Actions

**Pattern saat ini:** Browser native `confirm()` — **24 usages** across modules

```html
<form onsubmit="return confirm('Yakin batalkan transaksi ini?')">
```

**Masalah:**
- Tidak bisa di-style
- Tidak accessible (screen reader inconsistent)
- Tidak bisa tambah context/detail
- Blocked di some browsers/settings

### 9.3 Loading States

| State | Status |
|-------|--------|
| Form submit loading | ❌ Tidak ada |
| Button disabled on submit | ❌ Tidak ada |
| Skeleton loaders | ❌ Tidak ada |
| Progress bar (PDF gen) | ❌ Tidak ada |
| AJAX fetch loading (satuan/kategori) | ❌ Tidak ada |
| Infinite scroll | ❌ Tidak ada (pagination only) |

### 9.4 Error Handling

| Sumber Error | Feedback Method |
|-------------|----------------|
| Server validation | Red text under field |
| Business logic (RuntimeException) | Flash error message |
| Void/delete guard | Flash error message |
| AJAX (satuan/kategori) | Alpine `satuanError` inline text |
| Camera/scanner | Browser `alert()` |
| 403/404/500 | Laravel default error pages |

### 9.5 Rekomendasi Feedback

| Priority | Action | Implementation |
|----------|--------|---------------|
| 🔴 High | Submit loading state | `@click="$el.disabled=true; $el.innerHTML='Menyimpan...'"` |
| 🔴 High | Modal confirmation | Reuse `<x-modal>` with confirm/cancel |
| 🟡 Medium | Auto-dismiss success | Alpine `setTimeout(() => show=false, 5000)` |
| 🟡 Medium | `role="alert"` on alerts | Add to alert component |
| 🟢 Low | Toast notification stack | New component for non-blocking notices |

---

## 10. Aksesibilitas (A11y)

### 10.1 Yang Sudah Ada

| Feature | Status | File |
|---------|--------|------|
| `lang` attribute on `<html>` | ✅ | app.blade.php |
| Form labels (`<x-input-label>`) | ✅ Most forms | components/ |
| Modal focus trap + Escape | ✅ | modal.blade.php (Breeze) |
| `[x-cloak]` anti-flash | ✅ | app.css |
| `sr-only` on some inputs | ⚠️ Partial | barang form, periode |
| `aria-label` on color picker | ⚠️ One instance | barang form |
| `title` on some icon buttons | ⚠️ Barang only | barang form |

### 10.2 Yang Missing

| # | Issue | WCAG Level | Impact |
|---|-------|-----------|--------|
| A1 | Icon-only actions tanpa `aria-label` | A | 🔴 Screen reader users |
| A2 | Sidebar toggle tanpa `aria-expanded` | A | 🔴 Keyboard/screen reader |
| A3 | Bottom nav tanpa `aria-current="page"` | A | 🟡 Navigation context |
| A4 | Alert dismiss button tanpa label | A | 🟡 Close button invisible |
| A5 | Dashboard chart div-only | A | 🔴 No data for screen reader |
| A6 | Auth pages in English | — | 🟡 Language inconsistency |
| A7 | No skip-to-content link | A | 🟡 Keyboard navigation |
| A8 | Focus rings missing on app buttons | AA | 🟡 Keyboard visibility |
| A9 | Color-only status (amber "Menipis") | A | 🟡 Color blind users |
| A10 | No heading hierarchy audit | A | 🟡 Document structure |

### 10.3 Rekomendasi A11y

**Quick wins (1-2 hari):**
```html
<!-- Icon action buttons -->
<button aria-label="Lihat detail {{ $item->nomor }}">
    <svg><!-- eye icon --></svg>
</button>

<!-- Sidebar toggle -->
<button aria-expanded="false" aria-controls="sidebar" aria-label="Buka menu navigasi">

<!-- Alert -->
<div role="alert" aria-live="polite">

<!-- Skip link (in app.blade.php head) -->
<a href="#main-content" class="sr-only focus:not-sr-only ...">Skip to content</a>
```

---

## 11. Inkonsistensi Antar Modul

### 11.1 Matriks Inkonsistensi

| Dimensi | Pattern A (Mayoritas) | Pattern B (Minoritas) | Modul Pattern B |
|---------|----------------------|----------------------|-----------------|
| Primary button | `bg-emerald-600 rounded-lg text-sm` | `primary-button` gray-800 uppercase | Auth, Profile |
| Focus color | Emerald (implicit) | Indigo ring | Breeze components |
| Validation error | `<x-input-error>` | `@error` inline `<p>` | Transaksi create, pengaturan |
| Page chrome | `<x-page-header>` + `<x-card>` | Breeze `py-12 max-w-7xl shadow` | profile/edit |
| Table wrapper | Raw `<table>` | `<x-datatable>` (unused) | — |
| Filter reset | Some have Reset link | Others don't | BB sub-ledger |
| PDF button color | `bg-slate-700` | `bg-blue-600` | Penjualan show |
| JS organization | `@push('scripts')` | Inline `<script>` bottom | Mixed |
| Delete confirm | `confirm()` everywhere | — | Universal |
| Status language | Indonesian | English "Posted" | Jurnal show |
| Menu overlay | Two `#menu-overlay` IDs | — | app + sidebar |
| Error display (inventori) | Global flash | Inline session error | stok-opname, perubahan-stok create |

### 11.2 Rekomendasi Unifikasi

1. **Buat `<x-button variant="primary|secondary|danger">`** — ganti semua inline button classes
2. **Migrate auth/profile** ke emerald palette + Indonesian labels
3. **Standardize `<x-input-error>`** di semua form
4. **Adopt atau hapus `<x-datatable>`**
5. **Satu `<x-confirm-modal>`** ganti semua `confirm()`

---

## 12. JavaScript dan Alpine.js

### 12.1 Global Entry Point

**File:** `resources/js/app.js`

```javascript
import Alpine from 'alpinejs';
import { Html5Qrcode } from 'html5-qrcode';
window.Alpine = Alpine;
window.Html5Qrcode = Html5Qrcode;
Alpine.start();
```

**Minimal** — no Axios, no router, no component registration.

### 12.2 Inline Alpine Factories (~20 views)

| Factory Function | File | Lines (est.) | Complexity |
|-----------------|------|-------------|-----------|
| `penjualanForm()` | transaksi/penjualan/create | ~150 | High |
| `pembelianForm()` | transaksi/pembelian/create | ~150 | High |
| `kasMasukForm()` | transaksi/kas-masuk/create | ~100 | Medium |
| `kasKeluarForm()` | transaksi/kas-keluar/create | ~100 | Medium |
| `jurnalForm()` | akuntansi/jurnal/create | ~80 | Medium |
| `barangForm()` | master/barang/form | ~200 | High |
| `returPenjualanForm()` | inventori/retur-penjualan/create | ~120 | High |
| `returPembelianForm()` | inventori/retur-pembelian/create | ~120 | High |
| `stokOpnameForm()` | inventori/stok-opname/create | ~80 | Medium |
| `perubahanStokForm()` | inventori/perubahan-stok/create | ~80 | Medium |
| `daftarHargaForm()` | master/daftar-harga/form | ~100 | Medium |

### 12.3 Duplikasi Kode JS

| Logic | Duplicated In | Lines Duplicated |
|-------|--------------|-----------------|
| `formatRupiah()` | Every transaction form | ~5 × 10 = 50 |
| `addRow()` / `removeRow()` | All line-item forms | ~15 × 8 = 120 |
| `syncAll()` hidden inputs | All line-item forms | ~20 × 8 = 160 |
| Subtotal calculation | Pembelian, Penjualan, Kas | ~30 × 3 = 90 |
| Fetch CSRF helper | Barang form only | Should be shared |

**Total estimasi duplikasi:** ~420 lines yang bisa di-extract.

### 12.4 Rekomendasi Refactor JS

```
resources/js/
├── app.js                    # Entry point
├── alpine/
│   ├── components/
│   │   ├── line-item-form.js # Shared add/remove/sync
│   │   ├── currency.js       # formatRupiah helper
│   │   └── confirm-modal.js  # Destructive action confirm
│   └── forms/
│       ├── penjualan.js
│       ├── pembelian.js
│       ├── kas-masuk.js
│       ├── jurnal.js
│       └── barang.js
└── utils/
    ├── fetch.js              # CSRF-aware fetch wrapper
    └── scanner.js            # Html5Qrcode wrapper
```

---

## 13. Print dan PDF

### 13.1 DomPDF Usage

| Route | Controller Method | Template |
|-------|------------------|----------|
| `penjualan/{id}/pdf` | `PenjualanController@pdf` | `transaksi/penjualan/invoice` |
| `pembelian/{id}/pdf` | `PembelianController@pdf` | `transaksi/pembelian/invoice` |
| `laporan/laba-rugi/pdf` | `LabaRugiController@pdf` | `laporan/pdf/laba-rugi` |
| `laporan/neraca/pdf` | `NeracaController@pdf` | `laporan/pdf/neraca` |

### 13.2 PDF Design Patterns

| Aspek | Current | Recommended |
|-------|---------|-------------|
| Layout | Self-contained per template | Shared `pdf/layout.blade.php` |
| Header | Inline per template | Company block partial |
| Colors | Hardcoded per template | CSS variables |
| Font | DejaVu Sans | Keep (dompdf safe) |
| Footer | None | Page number + date partial |
| Logo | None | From pengaturan |

### 13.3 Print CSS (Tidak Ada)

**Rekomendasi tambahkan di `app.css`:**
```css
@media print {
    nav, .sidebar, .bottom-nav, .topbar, .no-print { display: none !important; }
    main { padding: 0 !important; }
    .print-full-width { width: 100% !important; }
    body { font-size: 12pt; }
}
```

---

## 14. Bug UI Teridentifikasi

| # | Bug | Severity | File | Fix |
|---|-----|----------|------|-----|
| UI-B1 | Duplicate `#menu-overlay` ID | 🔴 | app.blade.php + sidebar.blade.php | Hapus satu, atau rename |
| UI-B2 | `<x-datatable>` never used | 🟢 | components/datatable.blade.php | Adopt or delete |
| UI-B3 | `navigation.blade.php` dead code | 🟢 | layouts/navigation.blade.php | Delete |
| UI-B4 | Profile page layout mismatch | 🟡 | profile/edit.blade.php | Migrate to x-page-header + x-card |
| UI-B5 | Auth pages English, app Indonesian | 🟡 | auth/*.blade.php | Localize |
| UI-B6 | Inventori create inline error bypasses global alert | 🟡 | stok-opname/create, perubahan-stok/create | Use global flash |
| UI-B7 | Barcode scanner button uses emoji | 🟢 | barang/form.blade.php | Use SVG icon |
| UI-B8 | Scanner block after submit button in DOM | 🟡 | barang/form.blade.php | Move near barcode field |

---

## 15. Rekomendasi UI/UX Ideal

### 15.1 Prinsip Desain Target

| Prinsip | Implementasi |
|---------|-------------|
| **Konsistensi** | Satu design system, satu button component, satu error pattern |
| **Kecepatan** | Loading states, optimistic UI where safe, keyboard shortcuts |
| **Kejelasan** | Label jelas, status badges konsisten, format Rupiah uniform |
| **Aksesibilitas** | WCAG 2.1 AA minimum, aria-labels, focus management |
| **Mobile-first** | Card layouts, touch-friendly targets, collapsible filters |
| **Feedback** | Toast notifications, modal confirmations, inline validation |

### 15.2 Navigasi Ideal

```
┌─ SIDEBAR ─────────────────┐
│ 🔍 [Search menu...]       │  ← NEW: menu search
│                           │
│ ▼ Menu Utama              │  ← NEW: collapsible groups
│   Dashboard               │
│ ▼ Master Data             │
│   Akun Perkiraan          │
│   ...                     │
│ ▼ Transaksi               │
│   ...                     │
│                           │
│ ── QUICK ACTIONS ──       │  ← NEW: frequent actions
│ [+ Pembelian] [+ Penjualan│
│ [+ Kas Masuk]             │
│                           │
│ Periode: Sep 2026 🟢      │  ← Enhanced period indicator
│ [Logout]                  │
└───────────────────────────┘
```

### 15.3 Dashboard Ideal

```
┌─────────────────────────────────────────────────────┐
│ Dashboard                    [Periode: Sep 2026 ▼]  │
├──────────┬──────────┬──────────┬──────────┬──────────┤
│ Laba/Rugi│ Saldo Kas│ Kas Masuk│ Kas Keluar│ Piutang │
│ Rp 15.2M │ Rp 45.8M │ Rp 8.3M  │ Rp 5.1M  │ Rp 12M  │
│ ▲ 12%    │          │          │          │         │
├──────────┴──────────┴──────────┴──────────┴──────────┤
│ [Cash Flow Chart — 6 months]  │ [Quick Actions]     │
│                                │ + Pembelian         │
│                                │ + Penjualan         │
│                                │ + Kas Masuk         │
│                                │ + Jurnal Manual     │
├────────────────────────────────┴─────────────────────┤
│ Recent Transactions (last 10)          [Lihat Semua →]│
│ ┌──────┬──────────┬─────────┬──────────┬──────────┐  │
│ │ Tipe │ Nomor    │ Tanggal │ Nominal  │ Status   │  │
│ └──────┴──────────┴─────────┴──────────┴──────────┘  │
├───────────────────────────────────────────────────────┤
│ Alerts & Warnings                                     │
│ ⚠ 3 barang stok menipis  ⚠ 2 piutang jatuh tempo    │
└───────────────────────────────────────────────────────┘
```

### 15.4 Form Transaksi Ideal

```
┌─────────────────────────────────────────────────────┐
│ Buat Penjualan                                      │
├─────────────────────────────────────────────────────┤
│ Tanggal: [2026-09-07]  Customer: [Pilih ▼]         │
│ Metode: (●) Tunai  ( ) Kredit                       │
│ Rekening: [BCA ▼]                                   │
├─────────────────────────────────────────────────────┤
│ ITEM PENJUALAN                    [+ Tambah Baris]  │
│ ┌─────────┬─────┬──────────┬──────────┬─────┬─────┐│
│ │ Barang  │ Qty │ Harga    │ Diskon   │ Sub │  ✕  ││
│ ├─────────┼─────┼──────────┼──────────┼─────┼─────┤│
│ │ [▼]     │ [1] │ [50000]  │ [0]      │ 50K │ [✕] ││
│ │ [▼]     │ [2] │ [75000]  │ [5000]   │145K │ [✕] ││
│ └─────────┴─────┴──────────┴──────────┴─────┴─────┘│
│                                                     │
│ ┌─ RINGKASAN ──────────────────────────────────┐   │
│ │ Subtotal:                          Rp 195.000 │   │
│ │ Diskon Global: [    0    ]       - Rp       0 │   │
│ │ PPN (11%):                       + Rp  21.450 │   │
│ │ ─────────────────────────────────────────────  │   │
│ │ TOTAL:                             Rp 216.450 │   │
│ └───────────────────────────────────────────────┘   │
│                                                     │
│ Keterangan: [                                    ]  │
│                                                     │
│              [Batal]  [💾 Simpan Penjualan]         │
│                       ↑ disabled + spinner on click │
└─────────────────────────────────────────────────────┘
```

### 15.5 Tabel Index Ideal (Desktop + Mobile)

**Desktop:** Table layout (current, with improvements)
**Mobile:** Card list layout (new)

```
Desktop:
┌────────┬──────────┬──────────┬──────────┬────────┬─────────┐
│ Nomor  │ Tanggal  │ Customer │ Total    │ Status │ Aksi    │
├────────┼──────────┼──────────┼──────────┼────────┼─────────┤
│ PJ/... │ 07/09/26 │ PT ABC   │ Rp 216K  │ ✅     │ 👁 ✏ 🗑 │
└────────┴──────────┴──────────┴──────────┴────────┴─────────┘

Mobile:
┌─────────────────────────────────┐
│ PJ/09/2026/0042        ✅ Posted│
│ PT ABC · 07 Sep 2026           │
│ Rp 216.450                      │
│ [Detail]  [PDF]  [Void]        │
└─────────────────────────────────┘
```

---

## 16. Design System Target

### 16.1 Tailwind Config Enhancement

```javascript
// tailwind.config.js (target)
export default {
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0',
                    300: '#6ee7b7', 400: '#34d399', 500: '#10b981',
                    600: '#059669', 700: '#047857', 800: '#065f46',
                    900: '#064e3b',
                },
                sidebar: { DEFAULT: '#0f172a', hover: '#1e293b' },
            },
            fontFamily: { sans: ['Figtree', 'sans-serif'] },
        },
    },
};
```

### 16.2 Component Library Target

| Component | Variants | Priority |
|-----------|----------|----------|
| `<x-button>` | primary, secondary, danger, ghost, sm, lg | 🔴 |
| `<x-badge>` | success, warning, danger, info, neutral | 🔴 |
| `<x-input>` | text, number, date, search | ✅ Exists |
| `<x-select>` | default, searchable | ✅ Exists |
| `<x-modal>` | confirm, form, info | ✅ Exists |
| `<x-alert>` | success, error, warning, info, dismissible | ✅ Exists |
| `<x-card>` | default, stat, interactive | ✅ Exists |
| `<x-table>` | default, striped, compact | 🟡 New |
| `<x-empty-state>` | with icon + action | 🟡 New |
| `<x-confirm-dialog>` | destructive action confirm | 🔴 New |
| `<x-loading-button>` | submit with spinner | 🔴 New |
| `<x-currency-display>` | positive, negative, neutral | 🟡 New |
| `<x-filter-bar>` | collapsible on mobile | 🟡 New |
| `<x-mobile-card>` | list item card for mobile | 🔴 New |
| `<x-breadcrumb>` | navigation path | 🟢 New |

### 16.3 Button Component Target

```html
<!-- Usage examples -->
<x-button variant="primary" type="submit">Simpan</x-button>
<x-button variant="secondary" href="{{ route('...') }}">Batal</x-button>
<x-button variant="danger" size="sm" @click="confirmVoid">Void</x-button>
<x-button variant="ghost" icon="plus">Tambah Baris</x-button>

<!-- Loading state -->
<x-button variant="primary" type="submit" loading>Saving...</x-button>
```

### 16.4 Spacing & Sizing Standards

| Token | Value | Usage |
|-------|-------|-------|
| Page padding | `px-3 sm:px-6 lg:px-8 py-6` | Main content |
| Card padding | `p-4 sm:p-6` | Card body |
| Form gap | `gap-4` | Grid gap |
| Section gap | `space-y-6` | Between sections |
| Button height | `h-10` (40px) minimum | All buttons |
| Touch target | `min-h-[44px] min-w-[44px]` | Mobile actions |
| Table row height | `py-3` minimum | Table rows |
| Icon size | `w-5 h-5` (20px) | Inline icons |
| Action icon button | `p-2` (→ 36px total) | Table actions |

---

## 17. Wireframe dan Layout Target

### 17.1 Color Usage Guide

| Context | Color | Example |
|---------|-------|---------|
| Primary action | `primary-600` | Simpan, Tambah, Filter |
| Secondary action | `gray-200` bg | Batal, Reset |
| Destructive action | `red-600` | Void, Hapus |
| Positive money | `primary-600` | Revenue, saldo positif |
| Negative money | `red-600` | Beban, hutang |
| Neutral money | `gray-900` | Subtotal, total |
| Active nav | `primary-600` bg | Sidebar active item |
| Status success | `primary-100` bg + `primary-800` text | Posted, Approved |
| Status warning | `amber-100` bg + `amber-800` text | Pending, Menipis |
| Status danger | `red-100` bg + `red-800` text | Rejected, Void |

### 17.2 Typography Scale

| Level | Class | Usage |
|-------|-------|-------|
| H1 | `text-2xl font-bold` | Page title (rare) |
| H2 | `text-xl font-bold` | Page header title |
| H3 | `text-lg font-semibold` | Card title, section header |
| H4 | `text-base font-medium` | Sub-section |
| Body | `text-sm` | Default text, table cells |
| Caption | `text-xs text-gray-500` | Helper text, timestamps |
| Money | `text-sm font-semibold tabular-nums` | All currency values |

### 17.3 Icon System

**Saat ini:** Inline Heroicons SVG paths (duplicated across views)

**Target:** Blade icon component atau SVG sprite
```html
<x-icon name="eye" class="w-5 h-5" />
<x-icon name="pencil" class="w-5 h-5" />
<x-icon name="trash" class="w-5 h-5" />
<x-icon name="document" class="w-5 h-5" />
```

---

## 18. Roadmap Implementasi UI/UX

### Fase 1 — Fix & Unify (1-2 minggu)

| # | Task | Effort | Files |
|---|------|--------|-------|
| F1-1 | Fix duplicate `#menu-overlay` | 30 min | app.blade.php, sidebar.blade.php |
| F1-2 | Buat `<x-button>` component | 4 jam | components/button.blade.php |
| F1-3 | Buat `<x-confirm-dialog>` component | 4 jam | components/confirm-dialog.blade.php |
| F1-4 | Buat `<x-loading-button>` component | 2 jam | components/loading-button.blade.php |
| F1-5 | Ganti semua `confirm()` → confirm dialog | 1 hari | 24 form locations |
| F1-6 | Tambah `aria-label` ke icon actions | 4 jam | All index views |
| F1-7 | Standardize validation → `<x-input-error>` | 4 jam | Transaksi create views |
| F1-8 | Localize auth pages → Indonesian | 2 jam | auth/*.blade.php |
| F1-9 | Migrate profile page layout | 2 jam | profile/edit.blade.php |
| F1-10 | Hapus dead code (navigation.blade.php, datatable) | 30 min | layouts/, components/ |

### Fase 2 — Mobile & Components (2-3 minggu)

| # | Task | Effort |
|---|------|--------|
| F2-1 | Buat `<x-mobile-card>` component | 4 jam |
| F2-2 | Implement card layout on top 5 index pages | 2 hari |
| F2-3 | Buat `<x-filter-bar>` collapsible | 4 jam |
| F2-4 | Increase touch targets (min 44px) | 4 jam |
| F2-5 | Sidebar menu search | 4 jam |
| F2-6 | Sidebar collapsible groups | 4 jam |
| F2-7 | Submit loading states on all forms | 1 hari |
| F2-8 | Auto-dismiss success alerts (5s) | 1 jam |
| F2-9 | Add `@media print` CSS | 2 jam |
| F2-10 | Update tailwind.config.js with tokens | 2 jam |

### Fase 3 — Polish & Advanced (2-4 minggu)

| # | Task | Effort |
|---|------|--------|
| F3-1 | Refactor JS to ES modules | 3 hari |
| F3-2 | Dashboard redesign (quick actions, alerts) | 2 hari |
| F3-3 | Breadcrumb component | 1 hari |
| F3-4 | PDF shared layout partial | 1 hari |
| F3-5 | Export Excel/CSV buttons | 2 hari |
| F3-6 | `<x-icon>` component system | 1 hari |
| F3-7 | `<x-badge>` component | 2 jam |
| F3-8 | `<x-empty-state>` component | 2 jam |
| F3-9 | Keyboard shortcuts (N=new, /=search) | 1 hari |
| F3-10 | Dark mode (optional) | 3 hari |

---

## 19. Checklist Per Halaman

Gunakan checklist ini saat mereview/memperbaiki setiap halaman:

### Checklist Universal

- [ ] Menggunakan `<x-page-header>` + `<x-card>`
- [ ] Button menggunakan `<x-button variant="...">`
- [ ] Validasi error menggunakan `<x-input-error>`
- [ ] Icon actions memiliki `aria-label`
- [ ] Destructive actions menggunakan `<x-confirm-dialog>`
- [ ] Submit button menggunakan `<x-loading-button>`
- [ ] Flash messages via global alert (bukan inline)
- [ ] Responsive: table (desktop) + card list (mobile)
- [ ] Filter bar collapsible di mobile
- [ ] Empty state dengan icon + action button
- [ ] Pagination dengan query string preserved
- [ ] Status badges menggunakan `<x-badge>`
- [ ] Currency values menggunakan `formatRupiah()` dengan class konsisten
- [ ] Focus ring visible pada interactive elements

### Checklist Form Transaksi

- [ ] Alpine factory di-extract ke JS module (Fase 3)
- [ ] Line items: add/remove row dengan min 1 guard
- [ ] `syncAll()` on submit untuk hidden inputs
- [ ] Conditional fields dengan `x-show` + transition
- [ ] Summary section (subtotal, diskon, PPN, total)
- [ ] Real-time calculation feedback
- [ ] Client-side validation sebelum submit
- [ ] Loading state on submit
- [ ] Error display per field + global

### Checklist Index/List

- [ ] Filter form dengan reset button
- [ ] Active filter chips/summary
- [ ] Table dengan horizontal scroll wrapper
- [ ] Mobile card list fallback
- [ ] Pagination
- [ ] Empty state
- [ ] Action column dengan labeled buttons
- [ ] Sortable columns (enhancement)

### Checklist Show/Detail

- [ ] Header dengan nomor + status badge
- [ ] Detail fields in organized grid
- [ ] Related jurnal table (if applicable)
- [ ] Action buttons (Void, PDF, Edit) clearly labeled
- [ ] Void uses confirm dialog
- [ ] Breadcrumb navigation
- [ ] Back to index link

---

## 20. Referensi File UI

### Layouts
| File | Fungsi |
|------|--------|
| `resources/views/layouts/app.blade.php` | Main authenticated shell |
| `resources/views/layouts/sidebar.blade.php` | Sidebar navigation |
| `resources/views/layouts/topbar.blade.php` | Top header bar |
| `resources/views/layouts/bottom-nav.blade.php` | Mobile bottom navigation |
| `resources/views/layouts/guest.blade.php` | Auth page wrapper |

### Components
| File | Fungsi |
|------|--------|
| `resources/views/components/card.blade.php` | Content container |
| `resources/views/components/page-header.blade.php` | Page title area |
| `resources/views/components/alert.blade.php` | Flash messages |
| `resources/views/components/stat-card.blade.php` | Dashboard KPI |
| `resources/views/components/modal.blade.php` | Dialog overlay |
| `resources/views/components/text-input.blade.php` | Form input |
| `resources/views/components/select.blade.php` | Form select |
| `resources/views/components/input-error.blade.php` | Validation error |

### CSS & JS
| File | Fungsi |
|------|--------|
| `resources/css/app.css` | Tailwind directives + x-cloak |
| `resources/js/app.js` | Alpine.js + Html5Qrcode entry |
| `tailwind.config.js` | Tailwind configuration |
| `vite.config.js` | Vite build config |

### Key Views (Best Examples)
| File | Why Reference |
|------|--------------|
| `resources/views/dashboard.blade.php` | KPI cards + chart pattern |
| `resources/views/master/barang/form.blade.php` | Richest form (modals, scanner, foto) |
| `resources/views/master/barang/index.blade.php` | Column config, filters, badges |
| `resources/views/transaksi/penjualan/create.blade.php` | Dynamic line items pattern |
| `resources/views/akuntansi/jurnal/create.blade.php` | Double-entry form |
| `resources/views/laporan/laba-rugi.blade.php` | Report layout pattern |

### Key Views (Need Most Work)
| File | Issues |
|------|--------|
| `resources/views/profile/edit.blade.php` | Breeze layout mismatch |
| `resources/views/auth/login.blade.php` | English labels, indigo theme |
| `resources/views/layouts/navigation.blade.php` | Dead code |
| `resources/views/components/datatable.blade.php` | Never used |
| All index pages | Missing mobile card layout, aria-labels |
| All create pages with void | Using confirm() instead of modal |

---

> **Dokumen terkait:** Lihat `docs/ANALISIS-SISTEM-LOGIKA-DAN-ALUR.md` untuk analisis logika bisnis, alur akuntansi, bug backend, dan rekomendasi perbaikan sistem.

---

## Lampiran: Mockup Warna

```
┌─ Primary Palette ─────────────────────────┐
│                                           │
│  ■ emerald-50   #ecfdf5   Backgrounds    │
│  ■ emerald-100  #d1fae5   Badge bg       │
│  ■ emerald-500  #10b981   Icons          │
│  ■ emerald-600  #059669   ★ PRIMARY     │
│  ■ emerald-700  #047857   Hover          │
│  ■ emerald-800  #065f46   Badge text     │
│                                           │
├─ Neutral Palette ─────────────────────────┤
│                                           │
│  ■ slate-900    #0f172a   Sidebar        │
│  ■ gray-50      #f9fafb   Table header   │
│  ■ gray-100     #f3f4f6   Page bg        │
│  ■ gray-200     #e5e7eb   Borders        │
│  ■ gray-500     #6b7280   Captions       │
│  ■ gray-700     #374151   Labels         │
│  ■ gray-900     #111827   Headings       │
│  ■ white        #ffffff   Cards          │
│                                           │
├─ Semantic Palette ────────────────────────┤
│                                           │
│  ■ red-500      #ef4444   Error/Danger   │
│  ■ red-600      #dc2626   Void/Delete    │
│  ■ amber-500    #f59e0b   Warning        │
│  ■ blue-500     #3b82f6   Info/Tunai     │
│  ■ purple-500   #8b5cf6   Kredit badge   │
│                                           │
└───────────────────────────────────────────┘
```
