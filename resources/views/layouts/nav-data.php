<?php

/*
 * Sumber kebenaran (single source of truth) untuk menu navigasi.
 * Dipakai bersama oleh layouts/navbar.blade.php dan layouts/mobile-drawer.blade.php.
 * Filter paket (Pro) dilakukan lewat PlanService::saringNav().
 */

return [
    [
        'label' => 'Menu Utama',
        'items' => [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10'],
        ],
    ],
    [
        'label' => 'Master Data',
        'items' => [
            ['label' => 'Data Nama', 'route' => 'data-nama.index', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label' => 'Data Rekening', 'route' => 'rekening.index', 'icon' => 'M3 7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7zM16 12h.01M6 12h4'],
            ['label' => 'Data Produk', 'route' => 'data-produk.index', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
            ['label' => 'Daftar Harga', 'route' => 'daftar-harga.index', 'icon' => 'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2', 'feature' => 'daftar_harga'],
            ['label' => 'Pajak', 'route' => 'pajak.index', 'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z'],
            ['label' => 'Data Gudang', 'route' => 'gudang.index', 'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2zm3 4h6v6H6v-6zm8 0h4v6h-4v-6zm-2-4v2m2-2v2m2-2v2'],
            ['label' => 'Data Harta Tetap', 'route' => 'aset.index', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'feature' => 'aset'],
        ],
    ],
    [
        'label' => 'Buku Besar',
        'items' => [
            ['label' => 'Akun Perkiraan', 'route' => 'akun-perkiraan.index', 'icon' => 'M8 7h12M8 12h12M8 17h12M4 7h.01M4 12h.01M4 17h.01'],
            ['label' => 'Jurnal Umum', 'route' => 'jurnal.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            [
                'label' => 'Buku Besar',
                'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                'children' => [
                    ['label' => 'Buku Besar Umum', 'route' => 'buku-besar.index', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                    ['label' => 'BB Piutang', 'route' => 'bb-piutang.index', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'feature' => 'bb_khusus'],
                    ['label' => 'BB Hutang', 'route' => 'bb-hutang.index', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h9a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z', 'feature' => 'bb_khusus'],
                    ['label' => 'BB Persediaan', 'route' => 'bb-persediaan.index', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'feature' => 'bb_khusus'],
                ],
            ],
        ],
    ],
    [
        'label' => 'Transaksi',
        'items' => [
            [
                'label' => 'Kas & Bank',
                'icon' => 'M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
                'children' => [
                    ['label' => 'Kas Masuk', 'route' => 'kas-masuk.index', 'icon' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12'],
                    ['label' => 'Kas Keluar', 'route' => 'kas-keluar.index', 'icon' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12'],
                    ['label' => 'Transfer Rekening', 'route' => 'mutasi-bank.index', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
                ],
            ],
            ['label' => 'Penjualan', 'route' => 'penjualan.index', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
            ['label' => 'Pembelian', 'route' => 'pembelian.index', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'],
            [
                'label' => 'Retur',
                'icon' => 'M10 19l-7-7m0 0l7-7m-7 7h18',
                'feature' => 'retur',
                'children' => [
                    ['label' => 'Retur Penjualan', 'route' => 'retur-penjualan.index', 'icon' => 'M10 19l-7-7m0 0l7-7m-7 7h18'],
                    ['label' => 'Retur Pembelian', 'route' => 'retur-pembelian.index', 'icon' => 'M14 5l7 7m0 0l-7 7m7-7H3'],
                ],
            ],
            [
                'label' => 'Tagihan',
                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'children' => [
                    ['label' => 'Piutang Customer', 'route' => 'tagihan.piutang', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label' => 'Hutang Supplier', 'route' => 'tagihan.hutang', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h9a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z'],
                ],
            ],
        ],
    ],
    [
        'label' => 'Persediaan',
        'items' => [
            ['label' => 'Transfer Barang Antar Gudang', 'route' => 'transfer-gudang.index', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'feature' => 'multi_gudang'],
            ['label' => 'Perubahan Stok', 'route' => 'perubahan-stok.index', 'icon' => 'M4 4v5h.582M15 4h5v5M4 9a8 8 0 0114-2M20 15a8 8 0 01-14 2', 'feature' => 'opname'],
            ['label' => 'Stok Opname', 'route' => 'stok-opname.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'feature' => 'opname'],
        ],
    ],
    [
        'label' => 'Laporan',
        'items' => [
            ['label' => 'Laba Rugi', 'route' => 'laporan.laba-rugi', 'icon' => 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z'],
            ['label' => 'Neraca', 'route' => 'laporan.neraca', 'icon' => 'M3 3h18v18H3V3zm4 12v2m4-6v6m4-10v10'],
            ['label' => 'Arus Kas', 'route' => 'laporan.arus-kas', 'icon' => 'M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
            ['label' => 'Neraca Saldo', 'route' => 'laporan.neraca-saldo', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['label' => 'Laporan Penyusutan', 'route' => 'laporan.penyusutan', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'feature' => 'aset'],
        ],
    ],
    [
        'label' => 'Pengaturan',
        'items' => [
            ['label' => 'Profil Perusahaan', 'route' => 'pengaturan.index', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
            ['label' => 'Paket Langganan', 'route' => 'upgrade.index', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
            ['label' => 'Pengaturan Sistem', 'route' => 'pengaturan.sistem.index', 'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-12V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m0 4v10'],
            ['label' => 'Periode', 'route' => 'periode.index', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            [
                'label' => 'Tutup Buku',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'feature' => 'tutup_buku',
                'children' => [
                    ['label' => 'Tutup Buku Bulanan', 'route' => 'tutup-buku.index', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['label' => 'Tutup Buku Tahunan', 'route' => 'tutup-buku-tahunan.index', 'icon' => 'M3 4h18M4 4h16v13a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm6 8v3m6-3v3m-3-3v3'],
                ],
            ],
            ['label' => 'Proses Penyusutan', 'route' => 'penyusutan.index', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'feature' => 'aset'],
            ['label' => 'Kalkulator Penyusutan', 'route' => 'penyusutan.kalkulator', 'icon' => 'M9 7h6m-6 4h6m-3 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'feature' => 'aset'],
        ],
    ],
];
