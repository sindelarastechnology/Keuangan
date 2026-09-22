---
paths:
  - 'tests/**'
---

# Tests

## Clear stale config cache before running tests (test DB isolation)
Before ANY test run, run `php artisan optimize:clear` (or at least `config:clear`). A stale `bootstrap/cache/config.php` (from config:cache) bakes in the prod `mysql/keuangan` connection and silently overrides phpunit.xml's `DB_CONNECTION=sqlite :memory:`, so RefreshDatabase will run migrations/destroys against the REAL MySQL DB and fail with FK/index errors. After clearing, `php artisan test` correctly uses the in-memory sqlite DB.
