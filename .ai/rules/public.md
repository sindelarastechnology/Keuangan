---
paths:
  - 'public/**'
---

# Public

## public/storage junction must be a Windows junction, not a real folder
On Windows, public/storage can silently become a real empty folder instead of a junction. Symptoms: product photos upload fine (file in storage/app/public + DB foto set) but all storage/... URLs 404 and images vanish after save. Fix: rmdir public/storage (only if empty) then `cmd /c mklink /J public\storage storage\app\public`. `php artisan storage:link` will refuse if the folder exists. Verify with `ls public/storage/barang`.
