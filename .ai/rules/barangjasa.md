---
paths:
  - 'resources/views/master/{barang,jasa}/**'
---

# Barangjasa

## Checkbox is_aktif butuh hidden input value=0
Checkbox `is_aktif` yang tidak dicentang tidak ikut terkirim oleh browser, sehingga `$request->boolean('is_aktif', true)` tetap mengembalikan true. Selalu sertakan `<input type="hidden" name="is_aktif" value="0">` tepat sebelum checkbox agar barang/jasa bisa dinonaktifkan.
