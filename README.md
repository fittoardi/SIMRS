# SIMRS RS Aisyah Medika — Sistem Informasi Rumah Sakit Terpadu

Sistem Informasi Manajemen Rumah Sakit (SIMRS) responsif dan terintegrasi untuk **RS Aisyah Medika**, dibangun dengan arsitektur **Full Laravel Monolith** yang mengelola seluruh alur pasien mulai dari pendaftaran/booking antrean, rekam medis elektronik (RME/SOAP), e-prescribing instalasi farmasi, hingga kasir & billing otomatis.

---

## 🏥 Karakteristik & Tech Stack

- **Framework**: Laravel 12.x / 13.x (PHP 8.4+)
- **Panel Medis & Operasional**: [Filament v5](https://filamentphp.com) di `/admin`
- **Portal Publik & Pasien**: Blade + Livewire + Tailwind CSS + Alpine.js
- **Autentikasi Pasien**: Laravel Breeze (Livewire & Blade Stack) di `/masuk` / `/login`
- **Role & Hak Akses**: `spatie/laravel-permission` dengan pemisahan ketat UU PDP
- **Database**: MySQL / MariaDB (Laragon port 3310)
- **Desain Adaptif**:
  - **Desktop (`>= 1024px`)**: Topbar lengkap, Sidebar navigasi, Data Table multi-kolom
  - **Mobile (`< 768px`)**: Bottom Navigation Bar ramah sentuhan, Stacked Card View (bebas scroll horizontal)

---

## 🔑 Akun Contoh untuk Pengujian (Password: `password`)

Semua akun terisi otomatis lewat `php artisan db:seed`:

| Role | Nama | Email | Akses Panel |
|---|---|---|---|
| **Super Admin** | Super Administrator (CONTOH) | `superadmin@aisyahmedika.id` | Panel `/admin` (Penuh) |
| **Dokter** | dr. Siti Aisyah, Sp.PD (CONTOH) | `dokter@aisyahmedika.id` | Panel `/admin` (RME, Jadwal, Resep) |
| **Perawat** | Ns. Rahmat Hidayat, S.Kep (CONTOH) | `perawat@aisyahmedika.id` | Panel `/admin` (TTV, Skrining, Antrean) |
| **Admisi** | Dina Admisi (CONTOH) | `admisi@aisyahmedika.id` | Panel `/admin` (Walk-in, Antrean, Kamar) |
| **Farmasi** | apt. Farhan Pratama, S.Farm (CONTOH) | `farmasi@aisyahmedika.id` | Panel `/admin` (Resep, Stok Obat) |
| **Kasir** | Kurnia Kasir (CONTOH) | `kasir@aisyahmedika.id` | Panel `/admin` (Invoice Billing, Pembayaran) |
| **Pasien** | Budi Santoso (CONTOH) | `pasien@aisyahmedika.id` | Area `/akun` (Dilarang akses `/admin`) |

---

## 🚀 Panduan Menjalankan Secara Lokal

### 1. Kebutuhan Sistem
- PHP >= 8.3 (dengan ekstensi `pdo_mysql`, `zip`, `intl`, `mbstring`, `bcmath`)
- Composer >= 2.8
- Node.js >= 18 & NPM
- MySQL / MariaDB (aktifkan di Laragon)

### 2. Langkah Instalasi

```bash
# 1. Clone repository & masuk ke direktori
cd d:\Laravel\SIMRS

# 2. Salin environment & sesuaikan kredensial DB
copy .env.example .env

# 3. Jalankan migrasi dan seeder
php artisan migrate --force
php artisan db:seed

# 4. Build asset CSS/JS (Vite & Tailwind)
npm run build
```

### 3. Menjalankan Server Pengembangan

```bash
# Jalankan server aplikasi Laravel
php artisan serve

# (Opsional) Jalankan Vite dev server untuk hot reload tampilan
npm run dev
```

Buka di browser:
- **Portal Publik**: `http://localhost:8000`
- **Area Pasien**: `http://localhost:8000/akun`
- **Panel Medis & Staf**: `http://localhost:8000/admin`

---

## 🧪 Pengujian (Pest) & Format Kode (Pint)

```bash
# Menjalankan seluruh test suite
php artisan test

# Menjalankan test otorisasi akses panel staf vs pasien
php artisan test --filter=AdminPanelAccessTest

# Format kode sesuai standar Laravel Pint
vendor/bin/pint --format agent
```
