---
paths:
  - 'tests/Feature/**'
---

# Tests Feature

## Reset state statis TenantDatabase di tearDown test
TenantDatabase::$activeUserId (dan $skemaCache) bersifat statis dan bocor antar-test dalam satu proses PHP. Test yang memanggil connect()/provision() wajib memanggil TenantDatabase::disconnect() di tearDown, atau test lain (mis. NomorGenerator yang memfilter user_id via Auth::id() ?? activeUserId) akan gagal karena data tidak terlihat.
