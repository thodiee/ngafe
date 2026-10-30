# ☕ Ngafe

Sistem manajemen operasional kafe & F&B modern berbasis web: POS kasir, pemesanan mandiri (QR Meja), Kitchen Display (KDS), manajemen stok & resep (BOM), presensi & HR, serta laporan keuangan pemilik.

---

## 🚀 Fitur Utama

- **Point of Sale (POS)**: Buka/tutup shift laci kasir, manajemen meja, split bill, cetak struk, dan pembayaran tunai/QRIS.
- **Self-Service QR Meja**: Pemesanan e-menu mandiri oleh pelanggan dari meja dan pembayaran online.
- **Kitchen (KDS) & Waiter**: Antrean tiket pesanan dapur real-time dan notifikasi siap saji untuk pelayan.
- **Inventaris & Resep (BOM)**: Stok bahan baku otomatis terpotong saat pesanan dibuat, Purchase Order, dan Stock Opname.
- **HR & Presensi**: Absensi PIN & verifikasi wajah, jadwal shift kerja, cuti, serta kalkulasi gaji (payroll).
- **Laporan & Keuangan**: Dashboard Owner (omzet, laba rugi, HPP), pencatatan biaya, dan loyalitas pelanggan (CRM).
- **Karir (ATS)**: Portal publik lowongan kerja kafe & manajemen pelamar.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Tailwind CSS v4, Blade, Vite
- **Database**: MySQL / MariaDB (atau SQLite)

---

## ⚡ Instalasi Cepat

```bash
# 1. Masuk ke direktori
cd Ngafe

# 2. Install dependensi
composer install
npm install

# 3. Setup environment & application key
cp .env.example .env
php artisan key:generate

# 4. Migrasi database & seeder
php artisan migrate --seed

# 5. Jalankan aplikasi
npm run dev          # Terminal 1 (Frontend Vite)
php artisan serve    # Terminal 2 (Server Laravel)
```

Buka di browser: **`http://localhost:8000`**

---

## 🔑 Akun Demo (Default)

> **Password semua akun:** `password`

| Role | Email | Akses Utama |
| :--- | :--- | :--- |
| **Owner** | `owner@ngafe.com` | Laporan Keuangan Eksekutif & Pengaturan Kafe |
| **Admin** | `admin@ngafe.com` | Master Data (Menu, Meja, Akun, Reservasi) |
| **Kasir** | `cashier@ngafe.com` | POS Kasir & Buka/Tutup Shift |
| **Kitchen** | `kitchen@ngafe.com` | Kitchen Display System (KDS) |
| **Waiter** | `waiter@ngafe.com` | Order Ready Tracker & Pengantaran Meja |
| **HR** | `hr@ngafe.com` | Presensi, Shift, Cuti, & Payroll |
| **Finance** | `finance@ngafe.com` | Pengeluaran Operasional & Kas |
| **Inventory** | `inventory@ngafe.com` | Bahan Baku, PO, & Stock Opname |

---

## 📄 Lisensi
MIT License
