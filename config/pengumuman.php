<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mode pengumuman
    |--------------------------------------------------------------------------
    |
    | 'percobaan' menampilkan strip berwarna amber; ganti ke mode lain (mis.
    | 'maintenance') untuk menyesuaikan gaya strip di layouts/navbar.blade.php.
    |
    */

    'mode' => env('PENGUMUMAN_MODE', 'percobaan'),

    /*
    |--------------------------------------------------------------------------
    | Jarak antar pesan (ticker)
    |--------------------------------------------------------------------------
    |
    | Jarak (dalam piksel) di antara setiap pesan pada teks berjalan.
    |
    */

    'jarak' => 48,

    /*
    |--------------------------------------------------------------------------
    | Pesan berjalan (ticker)
    |--------------------------------------------------------------------------
    |
    | Pesan ditampilkan sebagai teks berjalan di bagian atas navbar. Kosongkan
    | array untuk menyembunyikan strip sepenuhnya.
    |
    */

    'pesan' => [
        'Sistem ini masih dalam masa percobaan.',
        'Mohon jangan memasukkan data penting Anda.',
        'Berikan kritik dan saran Anda melalui kolom chat',
    ],

];
