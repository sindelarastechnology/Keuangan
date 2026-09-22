# Project Rules Index

Before planning or editing, find the row whose globs match the file's path and read that rule file.

| Applies to | Rule file |
| --- | --- |
| resources/views/master/{barang,jasa}/** | .ai/rules/barangjasa.md |
| resources/views/components/*.blade.php, resources/views/components/modal.blade.php, resources/views/components/loading-button.blade.php | .ai/rules/components.md |
| app/Http/Controllers/DashboardController.php, app/Http/Controllers/ChatController.php, app/Http/Controllers/NotifikasiController.php | .ai/rules/controllers.md |
| app/Services/ReportService.php, tests/Feature/AsetPenyusutanTest.php | .ai/rules/feature.md |
| ** | .ai/rules/general.md |
| resources/views/inventori/retur-*/create.blade.php | .ai/rules/inventori.md |
| resources/js/app.js, resources/views/master/barang/form.blade.php | .ai/rules/js.md |
| resources/views/layouts/**, resources/views/layouts/nav-data.php | .ai/rules/layouts.md |
| app/Http/Controllers/Master/RekeningController.php | .ai/rules/master.md |
| database/migrations/** | .ai/rules/migrations.md |
| app/{Models,Traits}/** | .ai/rules/models-traits.md |
| app/Models/Barang.php, app/Models/Penjualan.php, app/Models/** | .ai/rules/models.md |
| resources/views/transaksi/{pembelian,penjualan}/create.blade.php | .ai/rules/pembelianpenjualan.md |
| public/** | .ai/rules/public.md |
| resources/views/inventori/retur-penjualan/create.blade.php,resources/views/inventori/retur-pembelian/create.blade.php | .ai/rules/retur-pembelian.md |
| routes/web.php, routes/auth.php | .ai/rules/routes.md |
| app/Services/JournalService.php, app/Services/NomorGenerator.php, app/Services/**, app/Services/PengaturanSistemService.php, app/Services/ReportService.php | .ai/rules/services.md |
| tests/** | .ai/rules/tests.md |
| app/Http/Controllers/Transaksi/Retur*Controller.php, app/Http/Controllers/Transaksi/*Controller.php, app/Http/Controllers/Transaksi/MutasiBankController.php, app/Http/Controllers/Transaksi/**, app/Http/Controllers/Transaksi/{Pembelian,Penjualan}Controller.php, app/Http/Controllers/Transaksi/KasMasukController.php, app/Http/Controllers/Transaksi/KasKeluarController.php, app/Http/Controllers/Transaksi/PenjualanController.php | .ai/rules/transaksi.md |
| resources/views/transaksi/*/index.blade.php | .ai/rules/views-transaksi.md |
| resources/views/**/*.blade.php, resources/views/**, resources/views/**/create.blade.php | .ai/rules/views.md |
