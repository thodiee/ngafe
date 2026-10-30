# ☕ Ngafe — Integrated Smart F&B & Cafe Management System

<p align="center">
  <img src="https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=1200&auto=format&fit=crop&q=80" alt="Ngafe Hero Banner" width="100%" style="border-radius: 12px; max-height: 320px; object-fit: cover;">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Tailwind_CSS-v4.0-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Vite-8.x-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite">
  <img src="https://img.shields.io/badge/MySQL-Supported-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</p>

---

## 📌 Tentang Ngafe

**Ngafe** adalah sistem manajemen operasional kafe dan *Food & Beverage* (F&B) berbasis web modern dan terpadu. Dirancang untuk mengotomatisasi seluruh siklus bisnis kuliner — mulai dari pengalaman pemesanan mandiri oleh pelanggan melalui QR meja, transaksi kasir (POS), alur kerja dapur (KDS) dan pelayan, kontrol inventaris berbasis resep (*Bill of Materials*), hingga manajemen SDM & absensi cerdas serta laporan keuangan komprehensif untuk pemilik bisnis (*Owner*).

---

## ✨ Fitur Utama

### 1. 🖥️ Point of Sale (POS) & Operasional Meja
- **Manajemen Shift Kasir**: Buka/tutup shift laci kasir (*cash float*), perhitungan kas masuk/keluar, dan rekonsiliasi kas riil.
- **Visual Table Management**: Pemetaan denah meja dan area kafe dengan status okupansi langsung (*occupied*, *available*, *reserved*).
- **Fleksibilitas Pesanan**: Mendukung varian menu, opsi *add-on*, catatan khusus, *split bill*, dan cetak struk kasir (*thermal receipt*).
- **Multi-Payment Ready**: Pembayaran Tunai, Debit/Kartu, dan pembayaran digital berbasis QRIS / Payment Gateway.

### 2. 📱 Self-Service Ordering (QR Code Meja)
- Pelanggan dapat memindai QR Code unik di meja untuk membuka e-menu interaktif.
- Pemesanan langsung tanpa antre dengan kalkulasi harga otomatis.
- Terhubung dengan simulasi & integrasi pembayaran online (Midtrans/QRIS).

### 3. 🍳 Kitchen Display System (KDS) & Waiter Hub
- **Layar Dapur Real-time**: Antrean pesanan masuk yang terorganisir per tiket dengan penanda status kesiapan (*Ready to Serve*).
- **Dashboard Pelayan (Waiter)**: Notifikasi pesanan siap saji untuk diantarkan langsung ke nomor meja yang tepat.

### 4. 📦 Inventaris Cerdas & Bill of Materials (BOM)
- **Resep & Pengurangan Stok Otomatis**: Setiap menu yang terjual langsung memotong stok bahan baku mentah sesuai takaran resep.
- **Pengadaan & Logistik**: Modul Supplier, *Purchase Order* (PO), dan *Goods Receipt* (penerimaan barang).
- **Kontrol Mutu & Stok**: *Stock Opname* berkala dengan alur persetujuan, mutasi antar-lokasi, dan pencatatan bahan terbuang (*Waste Tracking*).

### 5. 👥 Manajemen SDM (HR) & Absensi Cerdas
- **Absensi Multi-Metode**: Presensi kehadiran menggunakan PIN unik dan verifikasi wajah (*Face Registration & Verification*).
- **Jadwal & Shift Kerja**: Pengaturan shift kerja dan generator jadwal karyawan otomatis.
- **Pengajuan Cuti & Izin**: Alur persetujuan permohonan cuti terintegrasi.
- **Otomasi Penggajian (Payroll)**: Perhitungan gaji pokok, uang harian, lembur, dan penalti keterlambatan berdasarkan data kehadiran riil.

### 6. 📊 Laporan Finansial & Dashboard Pemilik
- **Owner Executive Dashboard**: Pantauan omzet, laba kotor, HPP/COGS, biaya operasional, dan laba bersih secara berkala.
- **Manajemen Pengeluaran**: Pencatatan beban operasional kafe (*expenses*) berdasarkan kategori.
- **CRM & Program Loyalitas**: Pengelolaan data pelanggan, akumulasi poin loyalitas, dan diskon reward ulang tahun pelanggan.

### 7. 💼 Portal Rekrutmen Terbuka (ATS)
- Halaman karir publik untuk publikasi lowongan kerja kafe.
- Pengajuan lamaran online langsung ke dalam sistem pelacakan kandidat internal.

---

## 🛠️ Tech Stack

| Komponen | Teknologi |
| :--- | :--- |
| **Backend Framework** | [Laravel 12](https://laravel.com/) |
| **Bahasa Pemrograman** | [PHP 8.2+](https://www.php.net/) |
| **Frontend Styling** | [Tailwind CSS v4](https://tailwindcss.com/) & Blade Templating |
| **Asset Bundler** | [Vite 6](https://vitejs.dev/) |
| **Database** | MySQL / MariaDB (Kompatibel dengan SQLite) |
| **Icons & Font** | [FontAwesome 6](https://fontawesome.com/) & Google Fonts |
| **Payment Gateway** | Midtrans & Standar QRIS Callback API |

---

## 🚀 Panduan Instalasi & Menjalankan

Ikuti langkah-langkah di bawah ini untuk menjalankan project di lingkungan lokal:

### 1. Prasyarat Sistem
Pastikan perangkat Anda telah terpasang:
- PHP >= 8.2 (dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `gd`)
- [Composer](https://getcomposer.org/)
- [Node.js & npm](https://nodejs.org/) (versi LTS direkomendasikan)
- MySQL / MariaDB server

### 2. Kloning Repositori & Instal Dependensi
```bash
# Masuk ke direktori project
cd Ngafe

# Install dependensi PHP (Composer)
composer install

# Install dependensi JavaScript (NPM)
npm install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin file konfigurasi environment dan sesuaikan kredensial database Anda:
```bash
cp .env.example .env
```
Buka file `.env` dan perbarui pengaturan database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ngafe_db
DB_USERNAME=root
DB_PASSWORD=
```

Generate application key:
```bash
php artisan key:generate
```

### 4. Migrasi & Seeder Database
Jalankan migrasi skema tabel sekaligus mengisi data master demo:
```bash
php artisan migrate --seed
```

### 5. Kompilasi Aset & Jalankan Server
Jalankan Vite untuk kompilasi frontend dan server aplikasi Laravel:

```bash
# Terminal 1: Build aset frontend
npm run dev

# Terminal 2: Jalankan server Laravel
php artisan serve
```

Aplikasi sekarang dapat diakses melalui peramban di: **`http://127.0.0.1:8000`**

---

## 🔐 Akun Demo Bawaan (Default Credentials)

Semua akun pengujian di bawah ini dibuat otomatis melalui `DatabaseSeeder` dengan kata sandi yang seragam:

> **Kata Sandi Default:** `password`

| Peran (Role) | Email | Akses Halaman |
| :--- | :--- | :--- |
| **Owner** | `owner@ngafe.com` | Dashboard Finansial, Laporan Eksekutif, Pengaturan Usaha |
| **Administrator** | `admin@ngafe.com` | Full Control Panel, Katalog Menu, Meja, Reservasi, Akun |
| **HR Manager** | `hr@ngafe.com` | Manajemen Karyawan, Presensi, Shift, Cuti, Payroll |
| **Kasir (Cashier)** | `cashier@ngafe.com` | Kasir POS, Buka/Tutup Shift Laci, Live Status Meja |
| **Dapur (Kitchen)** | `kitchen@ngafe.com` | Kitchen Display System (KDS) & Manajemen Stok Dapur |
| **Pelayan (Waiter)**| `waiter@ngafe.com` | Order Ready Tracker & Waiter Service Board |
| **Inventory Staff** | `inventory@ngafe.com` | Bahan Baku, PO, Penerimaan Barang, Opname & Waste |
| **Finance Manager** | `finance@ngafe.com` | Pencatatan Pengeluaran Operasional & Laporan Kas |

---

## 📁 Struktur Direktori Utama

```text
Ngafe/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Controller master data, inventaris, & pengaturan
│   │   │   ├── PosController.php
│   │   │   ├── KitchenController.php
│   │   │   ├── WaiterController.php
│   │   │   ├── HrController.php
│   │   │   └── SelfServiceController.php
│   │   └── Middleware/         # Otentikasi & kontrol hak akses peran (Role-based)
│   └── Models/                 # Model Eloquent (User, Product, Order, Recipe, dll)
├── database/
│   ├── migrations/             # Skema basis data terstruktur
│   └── seeders/                # Dummy data siap pakai untuk demonstrasi
├── resources/
│   ├── css/                    # Desain stylesheet kustom & Tailwind
│   └── views/
│       ├── admin/              # Panel administrasi & laporan
│       ├── pos/                # Tampilan antarmuka kasir
│       ├── kitchen/            # Tampilan KDS dapur
│       ├── waiter/             # Tampilan pelayan
│       ├── self-service/       # Halaman pemesanan pelanggan via QR
│       └── welcome.blade.php   # Landing page utama
├── routes/
│   └── web.php                 # Rute web aplikasi
└── vite.config.js              # Konfigurasi build Vite
```

---

## 🔒 Keamanan & Kebijakan Akses

- **Role-Based Access Control (RBAC)**: Pembatasan akses berbasis peran mencegah pengguna mengakses data sensitif di luar wewenangnya.
- **Proteksi Brute-force & Rate Limiting**: Batasan frekuensi percobaan login, pendaftaran pelamar, dan pembuatan pesanan.
- **Audit & Keamanan Keuangan**: Validasi kas laci kasir pada setiap penutupan shift untuk mencegah selisih kas yang tidak terdata.

---

## 📄 Lisensi

Proyek ini dikembangkan untuk kebutuhan operasional kafe dan komersial F&B berlisensi [MIT License](LICENSE).
