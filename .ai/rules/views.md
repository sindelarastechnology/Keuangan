---
paths:
  - 'resources/views/**/*.blade.php'
  - 'resources/views/**'
  - 'resources/views/**/create.blade.php'
---

# Views

## Buka/close x-modal via window.dispatchEvent
Pola untuk membuka-tutup komponen x-modal: `window.dispatchEvent(new CustomEvent('open-modal', { detail: '<nama>' }))` dan `...('close-modal', ...)`. Komponen x-modal mendengarkan `x-on:open-modal.window` / `x-on:close-modal.window` pada elemen root-nya sendiri, jadi event ini bisa dikirim dari mana pun.

## Elemen DI LUAR scope x-data tidak diproses Alpine
Direktif Alpine `@click`/`x-on` di elemen yang tidak berada dalam subtree `x-data` TIDAK dieksekusi (mis. tombol toolbar "Atur Kolom" di halaman index — layout tidak memberi x-data di akar). Tombol semacam ini wajib memakai `onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: '<nama>' }))"` (vanilla JS, tanpa Alpine). Di dalam elemen yang punya x-data (form, komponen modal) boleh memakai `@click` dengan ekspresi Alpine.

## Hindari ?? di dalam interpolasi string @php
Jangan menulis operator `??` di dalam interpolasi string PHP (mis. `"... {$variants[$variant] ?? $variants['primary']}"` di blok @php) — PHP melarang `??` dalam interpolasi string dan akan memicu ParseError saat render. Pecah ke variabel perantara dulu, barulah interpolasi.

## Confirm dialog per-baris dengan x-confirm-dialog
Jangan pakai onsubmit="return confirm(...)" lagi. Pola baku: tombol hapus/batal konfirmasi = <button type="button" ... onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: '<prefix>-<rowid>' }))">, lalu <x-confirm-dialog name="<prefix>-<rowid>" ...> ditaruh DI DALAM form target (tombol Konfirmasi bersifat type=submit untuk form itu). Nama modal unik per baris agar dialog tepat sasaran. Ikon aksi (show/edit/delete) wajib aria-label deskriptif. Verifikasi via php artisan view:cache + test suite.

## Error validasi per-field pakai x-input-error
Jangan pakai @error('field') + <p> inline untuk field input. Gunakan komponen <x-input-error :messages="$errors->get('field')" class="mt-1" /> tepat di bawah input. Catatan: blok error global @if($errors->any()) di bagian atas form (mis. inventori) adalah concern UI-B6 yang terpisah — biarkan dan jangan disatukan dengan per-field.

## Alpine form components are inline blade scripts, not app.js
Forms using x-data="xxxForm()" (pembelianForm, penjualanForm, returPembelianForm, kasKeluarForm, etc.) define those functions as global `function xxxForm()` inside a `<script>` block at the bottom of the same .blade.php file (some via @push('scripts')). app.js only boots Alpine; it registers no data components. Bare method calls inside an Alpine method must use `this.methodName()` (e.g. stok-opname selisihText calling formatAngka(s) without `this.` throws ReferenceError).

## JS fetch() calls must use same-origin relative URLs
APP_URL is http://localhost:8000 but the app is commonly opened at http://127.0.0.1:8000. A fetch('/...') with Content-Type: application/json + X-CSRF-TOKEN to an absolute route() URL then becomes cross-origin, is CORS-blocked, and surfaces as 'Terjadi kesalahan koneksi.'. Always pass relative paths to fetch: {{ str_replace(url('/'), '', route('...')) }} (see retur-penjualan/retur-pembelian create.blade.php for the established pattern).
