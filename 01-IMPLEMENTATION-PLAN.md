# Implementation Plan — Sistem Informasi Rumah Sakit (SIMRS) Responsif dengan Full Laravel

> Seluruh sistem dibangun sebagai **satu aplikasi Laravel monolith** (portal publik, area pasien, layar antrean, dan panel operasional/medis dalam satu codebase) dengan desain **Adaptive Responsive** untuk layar Desktop dan Mobile. Ganti `[NAMA_RS_ATAU_KLINIK]` dengan nama institusi kesehatan kamu.

---

## 1. Tujuan & Ruang Lingkup

**Tujuan bisnis & layanan**
- Sistem informasi manajemen rumah sakit/klinik terpadu milik `[NAMA_RS_ATAU_KLINIK]` yang mengelola alur pasien dari ujung ke ujung (pendaftaran → pemeriksaan medis → farmasi → kasir).
- Memangkas antrean fisik di loket melalui pendaftaran mandiri dan pemantauan antrean real-time dari smartphone pasien.
- Memudahkan dokter, perawat, farmasi, dan kasir bekerja secara terhubung tanpa kertas (*paperless*) baik melalui PC desktop di ruangan maupun tablet/HP saat visite keliling.

**Dua kelompok peran utama**

| Peran | Akses | Perangkat Utama |
|---|---|---|
| **Staf Medis & Operasional** (Super Admin, Dokter, Perawat, Admisi, Farmasi, Kasir) | Panel `/admin`: admisi, antrean, RME (SOAP), resep farmasi, bed rawat inap, kasir/billing, laporan, master data | Desktop (Poli/Loket) & Mobile/Tablet (Visite Bangsal) |
| **Pasien & Keluarga** | Portal publik + area `/akun`: kartu berobat digital, anggota keluarga, booking antrean, pantau antrean live, riwayat berobat ringkas, bayar tagihan | Mobile-First (Smartphone) & Desktop |

**MVP**
- Direktori jadwal dokter, informasi ketersediaan bed rawat inap real-time, dan layar TV antrean
- Registrasi/login pasien, manajemen anggota keluarga, booking antrean poliklinik online
- Modul Admisi (pendaftaran walk-in, No. RM otomatis, check-in QR, alokasi bed kamar)
- Rekam Medis Elektronik (asesmen TTV keperawatan, pemeriksaan SOAP dokter, kode ICD-10)
- E-Prescribing dari ruang dokter ke instalasi farmasi & pemotongan stok obat otomatis
- Kasir & Auto-Billing (tunai dan pembayaran online via QRIS/VA/E-Wallet)
- Notifikasi status antrean, obat, dan pembayaran (Email + WhatsApp)

**Fase 2**
- Bridging API SATUSEHAT Kemenkes (FHIR) dan BPJS Kesehatan (Antrean Online & VClaim), modul Laboratorium & Radiologi, Telemedicine, pengadaan gudang farmasi (PO & Batch/Expired Tracking lanjutan)

---

## 2. Asumsi

| Item | Asumsi |
|---|---|
| Regulasi & Bahasa | Standar fasilitas kesehatan Indonesia (IDR, Bahasa Indonesia, kepatuhan UU PDP) |
| Skala awal | Klinik Utama atau RS Tipe C/D: 5–25 Poliklinik, 15–60 Dokter, 20–100 Bed Rawat Inap |
| Tim | 1 developer (atau AI coding agent) + 1 perwakilan manajemen/medis RS |
| Alur pendaftaran | Mendukung booking online (pasien login) **dan** pendaftaran langsung (walk-in) di loket admisi |
| Hosting | VPS / Cloud Server (Ubuntu + Nginx + PHP-FPM + Redis) demi keamanan data rekam medis |
| Trafik awal | 100–2.000 kunjungan & cek antrean/hari |

---

## 3. Tech Stack (Full Laravel)

| Lapisan | Teknologi |
|---|---|
| Framework | Laravel (versi stabil terbaru), PHP 8.3+ |
| Frontend publik & pasien | Blade + **Livewire** + Alpine.js + Tailwind CSS (Vite) |
| Panel operasional & medis | **Filament** (panel `admin`, resource CRUD, custom page RME, widget dashboard, relation manager) |
| Auth pasien | Laravel **Breeze** (Livewire stack): register, login, verifikasi email, reset password; opsional login Google via Socialite |
| Role & izin | `spatie/laravel-permission` (role: `super_admin`, `doctor`, `nurse`, `admission`, `pharmacist`, `cashier`, `patient`) + Laravel Policies |
| Database | MySQL/MariaDB atau PostgreSQL |
| Media & berkas medis | `spatie/laravel-medialibrary` (konversi WebP untuk publik, penyimpanan disk privat untuk hasil lab/rontgen) |
| Pembayaran | Midtrans (`midtrans/midtrans-php`) atau Xendit SDK: QRIS, VA bank, e-wallet + pencatatan tunai/penjamin |
| Integrasi eksternal | Interface siap sambung untuk SATUSEHAT (FHIR) & BPJS via Laravel HTTP Client |
| Antrian job & jadwal | Laravel Queue (database/Redis) + Scheduler + Supervisor |
| Cache | Redis (atau database di tahap awal) |
| Email | Laravel Mail + Resend/Brevo/SMTP |
| WhatsApp | Fonnte / WhatsApp Cloud API lewat Notification channel kustom |
| PDF cetakan medis | `barryvdh/laravel-dompdf` (tiket antrean, kartu pasien, resep, etiket obat, surat sakit, kwitansi) |
| SEO | `spatie/laravel-sitemap` + JSON-LD (`MedicalOrganization`, `Physician`) |
| Testing | Pest (feature + unit test) |
| Kualitas kode | Laravel Pint, Larastan |
| Monitoring | Sentry, Laravel Telescope (hanya di lokal/staging) |
| Deploy | VPS + Nginx + Supervisor + Let's Encrypt |

---

## 4. Pembagian Area Aplikasi & Desain Responsif

```text
Publik (guest)        → beranda, jadwal dokter, info bed kamar, layar TV antrean, edukasi, halaman statis
Pasien (auth)         → /akun: kartu berobat, keluarga, booking antrean, pantau antrean, riwayat, tagihan
Staf & Medis (role)   → /admin (Filament): admisi, antrean, RME (SOAP), farmasi, bed, kasir, master data
Webhook / API         → /webhooks/payment (tanpa CSRF, dengan verifikasi signature)
```

**Aturan Adaptasi Layar (Desktop vs Mobile)**

| Komponen | Tampilan Desktop (`>= 1024px`) | Tampilan Mobile (`< 768px`) |
|---|---|---|
| Navigasi Utama | Collapsible Sidebar vertikal di kiri + Topbar pencarian global (`Ctrl+K`) | Bottom Navigation Bar ramah sentuhan (4–5 ikon utama) + Header ringkas |
| Tabel Antrean & Pasien | Data Table multi-kolom lengkap dengan filter dan tombol aksi per baris | Otomatis bertransformasi menjadi **Stacked Card View** (kartu vertikal tanpa scroll horizontal) |
| Form Pemeriksaan RME (SOAP) | **Split-screen 2 kolom** (kiri: riwayat medis, alergi & TTV; kanan: input SOAP & E-Resep) | **1 kolom dengan Tabs/Accordion** (Riwayat \| TTV \| SOAP \| Resep) untuk kemudahan dokter saat visite |

**Matriks hak akses**

| Fitur | Guest | Pasien | Admisi | Perawat | Dokter | Farmasi | Kasir | Super Admin |
|---|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|
| Lihat jadwal dokter & info bed | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Booking antrean & kelola anggota keluarga | – | ✅ | – | – | – | – | – | – |
| Registrasi walk-in, panggil antrean, alokasi bed | – | – | ✅ | ✅ | – | – | – | ✅ |
| Input asesmen awal & TTV | – | – | – | ✅ | ✅ | – | – | ✅ |
| Baca & isi catatan klinis SOAP + ICD-10 | – | ringkasan | – | baca | ✅ | – | – | ✅ |
| Input E-Prescribing (resep obat) | – | – | – | – | ✅ | – | – | ✅ |
| Verifikasi resep, potong stok & cetak etiket | – | – | – | – | – | ✅ | – | ✅ |
| Kelola invoice billing & terima pembayaran | – | bayar milik sendiri | – | – | – | – | ✅ | ✅ |
| Master data, laporan & kelola staf | – | – | – | – | – | – | – | ✅ |

---

## 5. Daftar Fitur

### 5.1 Portal Publik & Area Pasien

**MVP**
- [ ] Beranda: hero pencarian jadwal dokter, poliklinik unggulan, ringkasan ketersediaan bed rawat inap, artikel kesehatan, tombol darurat IGD
- [ ] Halaman jadwal dokter: filter (poliklinik, hari praktik, pencarian nama), indikator kuota tersedia
- [ ] Halaman ketersediaan kamar: daftar bangsal & sisa tempat tidur real-time per kelas (VVIP, VIP, Kelas 1–3, ICU)
- [ ] Layar TV Antrean (`/layar-antrean`): tampilan monitor ruang tunggu untuk antrean poliklinik & farmasi
- [ ] Registrasi, login, verifikasi email, lupa password pasien
- [ ] Area akun pasien (`/akun`): kartu berobat digital (QR No. RM), profil & alergi mandiri, **buku anggota keluarga**, **booking antrean online**, **pantau antrean real-time**, riwayat kunjungan ringkas, unduh surat sakit/invoice, pembayaran tagihan online
- [ ] Halaman statis: Tentang RS, Alur Berobat, Hak & Kewajiban Pasien, FAQ, Kebijakan Privasi Data Medis, Kontak
- [ ] Tombol WhatsApp & IGD cepat, responsif mobile-first

**Fase 2**
- [ ] Telemedicine (konsultasi chat/video dengan dokter)
- [ ] Unduh hasil pemeriksaan Laboratorium & Radiologi langsung dari akun pasien
- [ ] Pengingat jadwal kontrol rutin & jadwal minum obat via WhatsApp

### 5.2 Panel Operasional & Medis (Filament)

**MVP**
- [ ] Login staf & tenaga medis di `/admin` dengan pembagian menu otomatis berdasarkan role
- [ ] Resource **Master Data**: Poliklinik, Dokter + Jadwal Praktik (relation manager), Kamar & Bed, Tindakan Medis, Obat & Alkes
- [ ] Resource **Pasien**: data demografi, No. RM otomatis, riwayat alergi, penautan anggota keluarga
- [ ] Modul **Admisi & Antrean**: pendaftaran walk-in, check-in QR booking online, pemanggilan antrean poli, cetak tiket antrean & gelang pasien
- [ ] Modul **Manajemen Bed Rawat Inap**: visualisasi status bed (`available`, `occupied`, `cleaning`, `maintenance`), mutasi/pulangkan pasien
- [ ] Modul **Rekam Medis Elektronik (RME)**: form TTV perawat, form SOAP dokter (split-screen di desktop, tabs di mobile), pencarian kode ICD-10, input tindakan, cetak surat sakit
- [ ] Modul **E-Prescribing & Farmasi**: antrean resep masuk dari dokter, pemeriksaan interaksi/alergi, potong stok obat otomatis, cetak etiket obat PDF
- [ ] Modul **Kasir & Billing**: kalkulasi tagihan otomatis (pendaftaran + tindakan + kamar + obat), pembayaran tunai/online, cetak invoice PDF
- [ ] **Dashboard**: statistik kunjungan, pendapatan, BOR kamar, stok obat kritis, antrean dokter hari ini
- [ ] Manajemen staf, role & pengaturan identitas rumah sakit

**Fase 2**
- [ ] Bridging SATUSEHAT Kemenkes (sinkronisasi Encounter, Condition, MedicationRequest)
- [ ] Bridging BPJS Kesehatan (Antrean Online Mobile JKN & VClaim SEP)
- [ ] Modul Order Laboratorium & Radiologi terintegrasi RME
- [ ] Log audit akses rekam medis lengkap (`spatie/laravel-activitylog`)

---

## 6. Struktur Route (Ringkas)

```text
GET   /                                  beranda publik
GET   /jadwal-dokter                     direktori & filter jadwal dokter
GET   /poliklinik/{polyclinic:slug}      detail poliklinik & dokter
GET   /dokter/{doctor:slug}              profil dokter & jadwal praktik
GET   /ketersediaan-kamar                info bed rawat inap real-time
GET   /layar-antrean                     tampilan TV antrean ruang tunggu
GET   /tentang | /alur-berobat | /faq | /kebijakan-privasi | /kontak

# Auth Pasien (Breeze)
GET   /masuk | /daftar | /lupa-password

# Pasien (middleware: auth, verified, role:patient)
GET   /akun                              dashboard & kartu berobat digital
GET   /akun/profil                       profil & data alergi
GET   /akun/keluarga                     daftar anggota keluarga
GET   /akun/booking                      form booking antrean poliklinik
GET   /akun/antrean/{queue}              tiket QR & pantau antrean live
GET   /akun/riwayat                      riwayat kunjungan & unduh dokumen
GET   /akun/tagihan/{invoice}            detail tagihan & bayar online

# Panel RS (Filament, middleware: auth, role:super_admin|doctor|nurse|admission|pharmacist|cashier)
/admin  → dashboard, admisi, antrean, rekam-medis, farmasi, kamar-bed, kasir, master-data, pengaturan

# Webhook
POST  /webhooks/payment                  verifikasi signature Midtrans/Xendit
```

---

## 7. Model Data & Migrasi

| Tabel / Model | Kolom utama |
|---|---|
| `users` | id, name, email, phone, password, email_verified_at, is_active, (role via spatie) |
| `patients` | id, user_id (nullable), medical_record_number (No. RM unik), nik, name, gender, birth_date, blood_type, allergies, phone, address |
| `patient_family_members` | id, user_id, patient_id, relationship (diri_sendiri, anak, pasangan, orang_tua) |
| `polyclinics` | id, code, name, slug, description, location, is_active |
| `doctors` | id, user_id, polyclinic_id, name, slug, sip_number, specialization, consultation_fee, is_active |
| `doctor_schedules` | id, doctor_id, day_of_week, start_time, end_time, max_quota, room_name, is_active |
| `rooms`, `beds` | rooms: id, name, ward_name, class_type, price_per_day; beds: id, room_id, bed_number, status, current_visit_id |
| `visits` | id, visit_number, patient_id, doctor_id, polyclinic_id, visit_date, visit_type (outpatient, inpatient, emergency), payment_type (general, bpjs, insurance), status |
| `queues` | id, visit_id, doctor_schedule_id, queue_number, queue_code, status, called_at, served_at, expires_at |
| `medical_records` | id, visit_id, doctor_id, nurse_id, blood_pressure, heart_rate, respiratory_rate, temperature, weight, height, subjective, objective, assessment, icd10_code, icd10_description, plan, notes |
| `medical_actions` | id, polyclinic_id, code, name, price, is_active |
| `medicines` | id, sku, name, category, unit, price, stock, min_stock, expired_at, is_active |
| `prescriptions`, `prescription_items` | id, visit_id, doctor_id, pharmacist_id, status, notes; items: prescription_id, medicine_id, medicine_name_snapshot, qty, dosage, price_snapshot |
| `invoices`, `invoice_items` | id, number, visit_id, patient_id, subtotal, discount, total, status, payment_status; items: invoice_id, item_type, name_snapshot, qty, price_snapshot |
| `payments` | id, invoice_id, provider, provider_ref, method, amount, status, paid_at, raw_payload (json) |
| `settings` | key, value (json) |
| `media` | tabel spatie medialibrary |
| spatie permission | roles, permissions, model_has_roles, dst. |

**Enum (PHP backed enum):** `QueueStatus` (booked, waiting, called, serving, completed, skipped, cancelled), `VisitStatus` (registered, in_examination, pharmacy, billing, completed, discharged, cancelled), `BedStatus` (available, occupied, cleaning, maintenance), `PrescriptionStatus` (pending, preparing, ready, handed_over), `PaymentStatus` (unpaid, paid, expired, refunded).

**Aturan penting**
- Snapshot nama tindakan, nama obat, dan harga di `prescription_items` serta `invoice_items` agar perubahan tarif master tidak mengubah riwayat tagihan lama.
- Total billing selalu dihitung ulang di server (`BillingService`), bukan dari input klien.
- Kuota jadwal dokter, alokasi bed kamar, dan stok obat farmasi: gunakan `DB::transaction` + `lockForUpdate()` untuk mencegah nomor antrean ganda, bed terisi dua pasien, atau stok obat minus.
- Webhook pembayaran bersifat idempotent berdasarkan `provider_ref` + status.

---

## 8. Arsitektur Kode

```text
app/
  Enums/                 QueueStatus, VisitStatus, BedStatus, PrescriptionStatus, PaymentStatus
  Models/                User, Patient, Polyclinic, Doctor, Visit, Queue, MedicalRecord, ...
  Policies/              PatientPolicy, VisitPolicy, MedicalRecordPolicy, InvoicePolicy
  Services/              QueueService, MedicalRecordService, PrescriptionService,
                         BillingService, BedAllocationService
  Integrations/
    Payment/             PaymentGateway (interface), MidtransGateway
    WhatsApp/            WhatsAppProvider (interface), FonnteProvider
    HealthBridging/      SatuSehatInterface, BpjsBridgingInterface
  Actions/               RegisterWalkInAction, SaveSoapExaminationAction, VerifyPrescriptionAction
  Jobs/                  ExpireUncheckedBookings, SendHospitalNotifications
  Notifications/         BookingConfirmed, QueueCalled, PrescriptionReady, InvoicePaid
  Events/ Listeners/     ExaminationCompleted → buat antrean farmasi & draft billing
                         InvoicePaid → update status kunjungan & kirim bukti bayar
  Livewire/
    Public/              DoctorScheduleFilter, BedAvailabilityBoard, QueueTvDisplay
    Patient/             FamilyMemberBook, OnlineBookingWizard, LiveQueueCard, BillingPayment
  Filament/Admin/
    Resources/           PatientResource, VisitResource, QueueResource, MedicalRecordResource,
                         PrescriptionResource, InvoiceResource, RoomResource, MedicineResource
    Widgets/             HospitalStatsOverview, BedOccupancyWidget, LowStockMedicineWidget
  Http/
    Controllers/Webhooks/
    Middleware/
resources/views/         layouts (desktop sidebar & mobile bottom-nav), components, livewire, pdf
routes/                  web.php, webhooks.php
database/                migrations, factories, seeders
tests/                   Feature/, Unit/ (Pest)
```

Prinsip: logika bisnis berada di **Services/Actions**, bukan di controller/komponen Livewire; integrasi pihak ketiga melalui **interface** agar mudah di-mock saat testing dan mudah diganti.

---

## 9. Alur Utama

**Booking Antrean, Pemeriksaan RME, Farmasi & Kasir**
1. Pasien booking jadwal dokter dari `/akun/booking` (atau daftar di loket admisi) → `QueueService` mengecek kuota dengan `lockForUpdate()` → terbit `Visit` & nomor `Queue`.
2. Pasien tiba di RS → check-in QR di admisi → status antrean `waiting`.
3. Perawat memanggil pasien → mengisi TTV & skrining alergi → dokter membuka form RME (tampil *split-screen* di PC poli atau *tabs* di tablet) → mengisi SOAP, ICD-10, tindakan, dan resep obat (`E-Prescribing`).
4. Dokter klik selesai periksa → event `ExaminationCompleted` mengirim resep ke antrean Farmasi dan menyusun tagihan otomatis di `BillingService`.
5. Farmasi memverifikasi resep → stok obat berkurang di dalam transaksi database → cetak etiket obat → status resep `ready`.
6. Pasien membayar di loket kasir (tunai) atau via HP (QRIS/VA Midtrans/Xendit) → webhook masuk → verifikasi signature → status `paid` → obat diserahkan dan pasien dapat mengunduh kwitansi/surat sakit PDF dari akunnya.

**Booking tidak check-in:** Scheduler menjalankan `ExpireUncheckedBookings` setelah sesi praktik dokter selesai untuk melepas status antrean yang tidak hadir.

---

## 10. Desain, Identitas Visual & Responsivitas

- **Palet Warna Medis:** Medical Teal/Blue sebagai warna primer yang menenangkan, hijau untuk indikator bed tersedia/antrean dipanggil, kuning untuk observasi/menunggu, dan merah kontras tinggi khusus untuk banner **Alergi Pasien**, kondisi kritis IGD, dan stok obat habis.
- **Mobile-First untuk Pasien:** Mengingat mayoritas pasien memantau antrean sambil menunggu menggunakan HP, seluruh halaman publik dan `/akun` dirancang seperti aplikasi mobile native dengan navigasi bawah (*Bottom Bar*) dan kartu vertikal yang mudah dibaca satu tangan.
- **Ergonomi Klinis untuk Dokter & Staf:** Di layar desktop, maksimalkan kepadatan informasi dengan *split-screen* agar dokter tidak perlu bolak-balik halaman saat membaca riwayat medis sambil mengetik SOAP baru.

---

## 11. Fase & Timeline (Estimasi)

| Fase | Durasi | Output |
|---|---|---|
| **0. Persiapan** | Minggu 1 | Identitas RS, daftar poliklinik, dokter + jadwal, tarif tindakan, daftar kamar/bed, katalog obat awal |
| **1. Fondasi & Role** | Minggu 1–2 | Install Laravel, Breeze, Filament, spatie permission, migrasi & seeder, layout responsif Desktop/Mobile |
| **2. Master Data & Publik** | Minggu 2–3 | CRUD Poli, Dokter, Jadwal, Kamar/Bed, Obat; halaman publik jadwal dokter & ketersediaan bed |
| **3. Area Pasien & Booking** | Minggu 3–4 | Register/login pasien, kartu berobat QR, anggota keluarga, booking antrean online |
| **4. Admisi, Antrean & Bed** | Minggu 4–5 | Loket walk-in, check-in QR, layar TV antrean, manajemen alokasi bed rawat inap |
| **5. RME, Farmasi & Billing** | Minggu 5–7 | Form SOAP adaptif, E-Prescribing, potong stok farmasi, auto-billing kasir, payment gateway & WA |
| **6. Dashboard & SEO** | Minggu 7–8 | Statistik RS (BOR, kunjungan, ICD-10), edukasi kesehatan, sitemap, JSON-LD |
| **7. QA & Uji Coba** | Minggu 8–9 | Pest tests, audit privasi rekam medis, simulasi alur pasien ujung-ke-ujung |
| **8. Launch** | Minggu 9 | Produksi, SSL, queue worker + scheduler aktif, pelatihan staf RS, soft launch |

---

## 12. Keamanan & Privasi Data Medis (UU PDP)

- HTTPS wajib, `APP_DEBUG=false` di produksi, seluruh secret dan kredensial API disimpan di `.env`
- Otorisasi berlapis dengan **Laravel Policies**:
  - Pasien hanya dapat melihat profil keluarga, antrean, ringkasan kunjungan, dan invoice miliknya sendiri (cegah IDOR).
  - Staf non-medis (Kasir & Admisi) diblokir dari mengakses isi detail catatan klinis `subjective`, `objective`, dan `assessment` pada tabel `medical_records`.
- Lampiran berkas medis (hasil lab/rontgen) disimpan di *private disk* dan hanya disajikan lewat route terautentikasi atau *signed URL*.
- Webhook pembayaran: dikecualikan dari CSRF, wajib verifikasi signature, idempotent, dan simpan log payload.
- Rate limiting pada login, registrasi, booking antrean, dan pencarian No. RM.
- Backup database terenkripsi setiap hari (`spatie/laravel-backup`) ke penyimpanan terpisah.

---

## 13. SEO & Edukasi Publik

- URL bersih menggunakan slug (`/poliklinik/penyakit-dalam`, `/dokter/dr-budi-sppd`), meta title/description, Open Graph, sitemap otomatis, `robots.txt`
- JSON-LD `MedicalOrganization`, `Physician`, dan `BreadcrumbList`
- Gambar WebP otomatis, lazy loading, target skor Lighthouse mobile ≥ 90 untuk halaman publik

---

## 14. Testing & QA

**Otomatis (Pest)**
- [ ] Unit: `QueueService` (penomoran & batas kuota), `BillingService` (akumulasi tindakan + kamar + obat), `PrescriptionService` (pemotongan stok obat)
- [ ] Feature: akses `/akun` vs `/admin` per role, policy rekam medis (staf kasir tidak bisa buka SOAP dokter; pasien A tidak bisa lihat data medis pasien B)
- [ ] Feature: dua request booking bersamaan pada kuota dokter terakhir, alokasi bed kamar yang sedang terisi
- [ ] Feature: webhook pembayaran valid/invalid signature, idempotency saat webhook dikirim ulang
- [ ] Job `ExpireUncheckedBookings` membatalkan antrean yang tidak check-in

**Manual / UAT**
- [ ] Responsif di layar Smartphone Android/iOS (Bottom Nav & Stacked Cards) dan layar Monitor Desktop (Sidebar & Split-Screen RME)
- [ ] Simulasi alur pasien dari booking HP → admisi → perawat → dokter → farmasi → bayar QRIS sampai cetak PDF
- [ ] Staf admisi, farmasi, dan dokter mencoba langsung antarmuka masing-masing

---

## 15. Deployment

1. Server: Ubuntu LTS + Nginx + PHP-FPM 8.3 + MySQL/PostgreSQL + Redis + Supervisor
2. `composer install --no-dev --optimize-autoloader`, `npm ci && npm run build`, `php artisan migrate --force`
3. `php artisan config:cache route:cache view:cache event:cache`, `php artisan storage:link`
4. Supervisor menjalankan `queue:work`; cron `* * * * * php artisan schedule:run`
5. SSL (Let's Encrypt), firewall (UFW), backup database harian otomatis, monitoring error via Sentry

---

## 16. Rencana Launch

1. Lingkungan staging disetujui oleh perwakilan dokter, farmasi, dan manajemen RS
2. Input master data aktual (poliklinik, jadwal dokter, tarif tindakan, kamar/bed, dan stok awal obat)
3. Pelatihan singkat per unit kerja (Admisi, Perawat/Dokter, Farmasi, Kasir)
4. Soft launch pada 1–2 poliklinik rawat jalan selama 1 minggu pertama
5. Grand launch seluruh poliklinik dan pembukaan pendaftaran antrean online untuk pasien umum

---

## 17. Biaya (Komponen)

| Komponen | Sifat |
|---|---|
| Domain | Tahunan |
| VPS/Cloud Server + Backup Storage | Bulanan |
| Payment Gateway (Midtrans/Xendit) | Fee per transaksi online |
| Email & WhatsApp Gateway | Bulanan / per pesan notifikasi |
| Printer termal (tiket antrean & etiket obat) + Barcode/QR Scanner | Sekali beli (perangkat keras loket) |

> Laravel, Filament, dan Livewire bersifat gratis (open source); rumah sakit tidak dibebani biaya lisensi per akun staf/dokter.

---

## 18. Risiko & Mitigasi

| Risiko | Mitigasi |
|---|---|
| Nomor antrean ganda atau bed kamar terisi dua pasien | `lockForUpdate()` di dalam `DB::transaction` saat booking dan alokasi bed |
| Selisih stok obat di farmasi | Pemotongan stok terkunci saat resep diverifikasi + peringatan stok minimum & tanggal kedaluwarsa |
| Kebocoran privasi data rekam medis (IDOR / akses lintas role) | Laravel Policies ketat per profesi + automated test otorisasi + penyimpanan berkas privat |
| Tabel data sulit dibaca dokter/pasien lewat HP | Desain adaptif: tabel otomatis berubah menjadi *Stacked Card View* dan form RME menjadi *Tabs* di layar mobile |
| Webhook pembayaran terlambat/ganda | Idempotent handler, tombol cek status pembayaran, serta opsi konfirmasi manual oleh kasir dengan bukti bayar |

---

## 19. Definition of Done (MVP)

- [ ] Pasien bisa mendaftar akun, menambah anggota keluarga, booking antrean dokter, dan memantau antrean berjalan dari HP
- [ ] Antarmuka beradaptasi penuh antara Desktop (Sidebar, Data Table, Split-Screen RME) dan Mobile (Bottom Nav, Stacked Cards, Tabbed RME)
- [ ] Alur operasional terhubung tanpa putus dari Admisi → Pemeriksaan SOAP Dokter → Resep Farmasi (stok obat berkurang) → Tagihan Kasir (lunas tunai/online)
- [ ] Cetakan PDF (tiket antrean, kartu pasien, etiket obat, surat sakit, invoice) berfungsi rapi
- [ ] Notifikasi email/WhatsApp terkirim pada tahapan penting
- [ ] Seluruh test otorisasi (termasuk pemisahan akses RME) dan pembayaran lulus; Lighthouse mobile ≥ 90 pada portal publik
- [ ] Queue worker, scheduler, backup harian, dan monitoring aktif di server produksi

---

## 20. Langkah Berikutnya

1. Siapkan daftar poliklinik, dokter beserta jadwal praktik, kamar rawat inap, tarif tindakan, dan daftar obat awal
2. Daftar akun sandbox Midtrans/Xendit & WhatsApp gateway (mis. Fonnte)
3. Siapkan logo dan warna identitas rumah sakit/klinik
4. Salin **Master Prompt** dari `prompt.md` ke AI coding agent, lalu jalankan fase demi fase mulai dari **Fase 1**
5. Review hasil setiap fase (baik di tampilan Desktop maupun Mobile) sebelum melanjutkan ke fase berikutnya