---
paths:
  - resources/js/app.js
  - resources/views/master/barang/form.blade.php
---

# Js

## Barcode scanner pakai pola x-effect (port konveksi-v2)
`window.Html5Qrcode = Html5Qrcode` diekspos di `resources/js/app.js` (import dari 'html5-qrcode'). Pemindai diatur dari blade via Alpine `x-effect`: saat `scanTerbuka` true → `mulaiPemindai()` membuat instance `new Html5Qrcode('barang-scan-region').start(...)` tanpa `await` (jangan pernah menunggu promise `Html5Qrcode.start()` — bisa hang selamanya jika event video 'playing' tidak pernah terjadi); saat false → `hentikanPemindai()` memanggil `stop().catch(()=>{}).finally(()=>clear())`. Ganti mode scan (barcode/qr) saat kamera menyala = stop lama lalu mulai ulang. Handler `.catch()` hanya untuk menampilkan pesan error izin kamera, tidak menggantung UI.