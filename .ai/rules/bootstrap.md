---
paths:
  - bootstrap/app.php
---

# Bootstrap

## Tenant connection must resolve before route model binding
`ResolveTenantDatabase` (alias `tenant-db`) is registered via `$middleware->prependToPriorityList(SubstituteBindings::class, ...)` so the tenant SQLite connection is switched BEFORE implicit route model binding runs. Without this, `{penjualan}`/`{pembelian}`/`{jurnal}` etc. bind on the shared `mysql` connection and every detail/preview/show route returns 404 in tenant mode. Any new middleware that reads tenant tables (before controllers) must be ordered after `ResolveTenantDatabase` too.
