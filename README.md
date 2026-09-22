# Sistem Informasi Akuntansi

Aplikasi web untuk mengelola transaksi keuangan, akuntansi, dan inventori usaha kecil menengah.

## Fitur Utama

### 📊 Akuntansi
- **Jurnal Umum** - Pencatatan transaksi dengan double-entry bookkeeping
- **Buku Besar** - Laporan saldo per akun dengan drill-down detail
- **Periode Akuntansi** - Manajemen periode buka/tutup
- **Tutup Buku** - Proses penutupan otomatis dengan jurnal penutup
- **Approval Workflow** - Sistem persetujuan transaksi untuk kontrol internal

### 💰 Transaksi
- **Kas Masuk/Keluar** - Pencatatan penerimaan dan pengeluaran kas
- **Penjualan** - Tunai dan kredit dengan management piutang
- **Pembelian** - Tunai dan kredit dengan management hutang
- **Mutasi Bank** - Transfer antar rekening

### 📦 Inventori
- **Manajemen Stok** - Weighted average cost method
- **Retur Penjualan/Pembelian** - Pengembalian barang dengan jurnal reversal
- **Stok Opname** - Penyesuaian stok fisik vs sistem
- **Perubahan Stok Manual** - Adjustment untuk berbagai keperluan

### 📈 Laporan
- **Laba Rugi** - Income statement dengan periode custom
- **Neraca** - Balance sheet dengan check balanced
- **Arus Kas** - Cash flow statement (metode tidak langsung)
- **Neraca Saldo** - Trial balance dengan verifikasi balance
- **Export** - PDF, Excel, dan CSV untuk semua laporan

### 👥 Multi-User & Authorization
- **Role-Based Access Control** - Admin, Akuntan, Kasir
- **Menu Filtering** - Sidebar dinamis sesuai role
- **Route Protection** - Middleware authorization per module

### 🔧 Master Data
- Chart of Accounts, Customer, Supplier, Barang, Jasa, Rekening (Kas & Bank), Pajak, Daftar Harga

## Teknologi

- **Backend:** Laravel 11.x + PHP 8.3
- **Frontend:** Blade Templates + Alpine.js + Tailwind CSS
- **Database:** MySQL (production) / SQLite (testing)
- **PDF:** DomPDF
- **Excel:** Laravel Excel (Maatwebsite)

## Persyaratan Sistem

- PHP 8.3 atau lebih tinggi
- Composer
- Node.js & NPM
- MySQL 8.0+ atau MariaDB 10.3+
- Web server (Apache/Nginx)

## Instalasi

### 1. Clone Repository & Install Dependencies

```bash
git clone <repository-url>
cd keuangan
composer install
npm install
```

### 2. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` dan sesuaikan database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=keuangan
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Migrasi Database & Seeding

```bash
php artisan migrate --seed
```

**Default Admin User:**
- Email: `admin@keuangan.test`
- Password: `password`
- Role: `admin`

### 4. Build Assets & Jalankan Server

```bash
npm run build
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

## Development

### Running Tests

```bash
php artisan test
```

Test suite menggunakan SQLite in-memory untuk performance.

### Code Style

```bash
vendor/bin/pint
```

Laravel Pint digunakan untuk code formatting (PSR-12).

### Struktur Direktori Penting

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Akuntansi/      # Jurnal, Buku Besar, Periode, Tutup Buku
│   │   ├── Transaksi/      # Kas, Penjualan, Pembelian, Mutasi
│   │   ├── Inventori/      # Retur, Stok Opname, Perubahan Stok
│   │   ├── Laporan/        # Laba Rugi, Neraca, Arus Kas, Neraca Saldo
│   │   └── Master/         # CRUD master data
│   ├── Requests/
│   │   └── Transaksi/      # Form Request classes
│   └── Middleware/
│       └── CheckRole.php   # Role authorization
├── Models/                 # Eloquent models dengan HasFactory
├── Services/
│   ├── JournalService.php  # Double-entry posting engine
│   ├── StockService.php    # Weighted average stock management
│   ├── ReportService.php   # Financial report generation
│   ├── NomorGenerator.php  # Transaction number generator
│   └── PeriodeService.php  # Accounting period management
├── Exports/                # Laravel Excel export classes
└── Traits/
    └── HasApprovalWorkflow.php  # Approval system trait

database/
├── factories/              # Model factories untuk testing
└── seeders/               # DatabaseSeeder dengan admin user

tests/
└── Feature/               # 156 tests / 795 assertions
```

## Role & Permissions

| Role | Akses |
|------|-------|
| **Admin** | Full access semua modul + Pengaturan |
| **Akuntan** | Master Data + Transaksi + Inventori + Akuntansi + Laporan |
| **Kasir** | Master Data + Transaksi + Inventori + Laporan |

Gate `approve-transactions` hanya untuk Admin dan Akuntan.

## Kontribusi

Proyek ini dikembangkan untuk keperluan internal. Untuk kontribusi atau saran perbaikan, silakan hubungi tim development.

## Lisensi

Proprietary - Internal Use Only
