<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Per-User Database (Tenant)
    |--------------------------------------------------------------------------
    |
    | Setiap user memiliki database SQLite sendiri yang menyimpan seluruh data
    | akuntansi (tabel dengan trait BelongsToUser). MySQL hanya menyimpan data
    | shared: users, mapping, chat, donasi, notifications, sesi, cache, jobs.
    |
    | Nonaktif secara default; diuji-off agar seluruh test suite tetap berjalan
    | pada satu database seperti sebelumnya, dan diaktifkan di .env produksi.
    |
    */

    'enabled' => env('TENANT_DB_ENABLED', false),

    /*
    | Nama koneksi database tenant yang didefinisikan di config/database.php.
    */
    'connection' => 'tenant',

    /*
    | Direktori penyimpanan file SQLite per user: {path}/{user_id}/app.sqlite
    */
    'path' => env('TENANT_DB_PATH', storage_path('app/tenant')),

    /*
    | Opsi SQLite untuk konkurensi web.
    */
    'journal_mode' => env('TENANT_DB_JOURNAL_MODE', 'wal'),
    'busy_timeout' => (int) env('TENANT_DB_BUSY_TIMEOUT', 5000),
    'synchronous' => env('TENANT_DB_SYNCHRONOUS', 'NORMAL'),

];
