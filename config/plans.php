<?php

return [
    'default' => 'pro',

    'email_admin' => env('PLAN_ADMIN_EMAILS', ''),

    'nama' => [
        'free' => 'Gratis',
        'pro' => 'Pro',
    ],

    'fitur' => [
        'aset' => ['label' => 'Data Harta Tetap (Aset) & Penyusutan'],
        'retur' => ['label' => 'Retur Penjualan & Retur Pembelian'],
        'multi_gudang' => ['label' => 'Transfer Barang Antar Gudang'],
        'opname' => ['label' => 'Perubahan Stok & Stok Opname'],
        'daftar_harga' => ['label' => 'Daftar Harga Bertingkat'],
        'tutup_buku' => ['label' => 'Tutup Buku Bulanan & Tahunan'],
        'bb_khusus' => ['label' => 'Buku Besar Piutang, Hutang & Persediaan'],
        'export' => ['label' => 'Export Laporan PDF, Excel & CSV'],
    ],

    'hak' => [
        'free' => [
            'aset' => false,
            'retur' => false,
            'multi_gudang' => false,
            'opname' => false,
            'daftar_harga' => false,
            'tutup_buku' => false,
            'bb_khusus' => false,
            'export' => false,
        ],
        'pro' => [
            '*' => true,
        ],
    ],

    'batas' => [
        'free' => [
            'barang' => 50,
        ],
        'pro' => [
            'barang' => PHP_INT_MAX,
        ],
    ],
];
