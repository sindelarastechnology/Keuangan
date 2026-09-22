---
paths:
  - '**'
---

# General

## Semua akun baru default Pro (free dihapus)
Kebijakan sudah berubah: DB default (migrasi add_plan) sekarang 'pro', begitu juga factory User, registrasi web (RegisteredUserController), login Google, dan API /api/v1/users — semua 'pro'. Kalian tidak perlu set plan saat membuat akun. PlanService::plan() masih fallback 'free' untuk akun legacy tanpa nilai. Tes DefaultPlanProTest menjaga invariant ini.
