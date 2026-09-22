---
paths:
  - 'resources/views/layouts/**'
  - resources/views/layouts/nav-data.php
---

# Layouts

## Single source of truth for navigation menu
All menu items (group label, route, icon) live ONLY in resources/views/layouts/nav-data.php. navbar.blade.php (desktop: categories as dropdowns in top bar) and mobile-drawer.blade.php both require it. No role filtering anymore — since single-role, every group is shown to all authenticated users (key `roles` dihapus dari nav-data). Grup P1-2 single-item menjadi tautan langsung; grup multi-item menjadi dropdown. When adding a new page/route, register it in nav-data.php — never hardcode nav links in other views. No left sidebar; there is no layouts/sidebar or topbar anymore.

## Grup Pengaturan
Memuat "Profil Perusahaan" (`pengaturan.index`) dan "Data" (satuan, kategori, unit kerja) — item "Kelola Pengguna" TIDAK ada (dikelola aplikasi luar via API user-management, lihat controllers.md).

## nav-data.php: data-only, required twice, no functions
nav-data.php is `require`d TWICE (navbar + mobile-drawer). It must contain ONLY data arrays — never `function`s (double-require would fatal 'cannot redeclare') and no top-level Laravel helpers that error outside a request (leave filtering to PlanService::saringNav). Route-defined attributes (url, badge) are permitted and evaluated lazily at render.
