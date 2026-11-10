# Prompt — Membangun Sistem Informasi Rumah Sakit (SIMRS) Responsif dengan Full Laravel

> Cara pakai: salin **Master Prompt** ke AI coding agent (mis. Claude Code, Cursor, atau Cline) sebagai konteks awal, lalu jalankan **Prompt per Fase** satu per satu. Review hasil tiap fase sebelum lanjut. Ganti semua `[PLACEHOLDER]`. Dokumen pendamping: `implementation-plan.md`.

---

## 1. Master Prompt (Konteks Proyek)

```text
Kamu adalah senior Laravel engineer sekaligus UI/UX designer yang berpengalaman membangun sistem informasi kesehatan dan rumah sakit (SIMRS) di Indonesia.

## Proyek
Bangun aplikasi web Sistem Informasi Rumah Sakit (SIMRS) terintegrasi untuk: [NAMA_RS_ATAU_KLINIK].
- Model layanan: rumah sakit / klinik utama satu entitas (single hospital/clinic, bukan marketplace multi-RS)
- Unit layanan: Pendaftaran/Admisi, Poliklinik Rawat Jalan, IGD, Rawat Inap (Bed Management), Rekam Medis Elektronik (RME), Instalasi Farmasi, dan Kasir/Billing
- Atribut layanan & medis: Nomor Rekam Medis (No. RM), jadwal praktik dokter, kuota antrean per sesi, pencatatan SOAP (Subjective, Objective, Assessment, Plan), tanda-tanda vital (TTV), kode ICD-10, stok obat per batch/expired date, dan tarif tindakan/kamar
- Target pengguna:
  1. Pasien & keluarga (mayoritas mengakses lewat smartphone/mobile untuk booking antrean, cek jadwal dokter, dan pantau antrean)
  2. Dokter & perawat (mengakses lewat desktop di poliklinik dan tablet/smartphone saat visite rawat inap)
  3. Staf admisi, farmasi, kasir, dan manajemen (mengakses lewat layar desktop)
- Bahasa antarmuka: Bahasa Indonesia. Mata uang: Rupiah (IDR), format "Rp 150.000"
- Karakter visual: [MISAL: bersih, menenangkan, profesional, medis modern dengan kontras jelas untuk indikator darurat/triase]
- Palet warna utama: [HEX WARNA, misal Medical Teal #0284c7 & Slate #f8fafc]. Font: [NAMA FONT atau "pilihkan yang cocok, misal Plus Jakarta Sans"]

## Arsitektur: FULL LARAVEL (monolith)
Seluruh sistem dalam satu aplikasi Laravel: portal publik, area pasien, layar TV antrean, dan panel operasional/medis.

Stack wajib:
- Laravel (versi stabil terbaru), PHP 8.3+
- Frontend publik & area pasien: Blade + Livewire + Alpine.js + Tailwind CSS (Vite). Jangan memakai SPA terpisah.
- Panel operasional & medis: Filament (panel "admin" di /admin)
- Auth pasien: Laravel Breeze (Livewire stack): register, login, verifikasi email, reset password
- Role & izin: spatie/laravel-permission dengan role super_admin, doctor, nurse, admission, pharmacist, cashier, patient + Laravel Policies
- Database: [MySQL / MariaDB / PostgreSQL]
- Media & berkas medis: spatie/laravel-medialibrary (konversi WebP untuk foto profil/artikel + disk privat untuk lampiran hasil lab/rontgen)
- Pembayaran online/kasir: [Midtrans / Xendit] (QRIS, VA bank, e-wallet) + pencatatan tunai/penjamin
- Integrasi eksternal: WhatsApp [Fonnte / WhatsApp Cloud API] via custom Notification channel; siapkan interface untuk bridging SATUSEHAT (FHIR) & BPJS
- Queue + Scheduler (driver [database / Redis])
- Email: Laravel Mail ([Resend / Brevo / SMTP])
- PDF dokumen medis: barryvdh/laravel-dompdf (tiket antrean, kartu berobat, resep & etiket obat, surat keterangan sakit, invoice billing)
- Testing: Pest. Kualitas: Laravel Pint + Larastan

## Dua Kelompok Peran Utama
1. STAF RS & MEDIS (super_admin, doctor, nurse, admission, pharmacist, cashier) → panel /admin (Filament)
   - Dashboard sesuai role (kunjungan harian, pendapatan, keterisian bed/BOR, antrean aktif, stok obat menipis/mendekati expired)
   - CRUD Master Poliklinik, Dokter + Jadwal Praktik (hari, jam, kuota), Bangsal & Tempat Tidur (Bed), Tindakan Medis, Obat/Alkes, Voucher/Diskon Layanan
   - Admisi & Antrean: registrasi pasien baru/lama, generate No. RM otomatis, check-in booking online, kelola panggilan antrean, alokasi bed rawat inap
   - Rekam Medis Elektronik (RME): input TTV & skrining oleh perawat, input pemeriksaan SOAP + ICD-10 + tindakan oleh dokter, riwayat medis pasien
   - E-Prescribing & Farmasi: dokter input resep langsung di RME → masuk daftar kerja farmasi → verifikasi & potong stok obat → cetak etiket
   - Kasir & Billing: akumulasi otomatis biaya pendaftaran, tindakan medis, sewa kamar inap, dan resep obat; terima pembayaran tunai atau online; cetak invoice PDF
   - Kelola akun pasien & staf (ubah role/nonaktifkan hanya super_admin)
2. PASIEN → portal publik + area /akun
   - Registrasi, login, verifikasi email, lupa password
   - Profil pasien, kartu berobat digital (QR/Barcode No. RM), buku anggota keluarga (daftarkan anak/orang tua lewat 1 akun)
   - Booking antrean poliklinik online (pilih anggota keluarga, poli, dokter, tanggal, cara bayar Umum/BPJS/Asuransi)
   - Pantau antrean berjalan secara real-time dari HP, batalkan booking jika belum check-in
   - Riwayat kunjungan ringkas (tanggal, dokter, diagnosis umum, resep obat, unduh surat sakit/invoice) & bayar tagihan online

## Portal Publik & Layar Antrean
- Beranda (hero cari dokter & booking cepat, poliklinik unggulan, info ketersediaan bed real-time, edukasi kesehatan, tombol darurat IGD)
- Jadwal dokter dengan filter (poliklinik, hari praktik, pencarian nama) dan indikator kuota
- Halaman ketersediaan kamar & bed rawat inap per kelas secara real-time
- Layar TV Antrean (/layar-antrean) untuk monitor ruang tunggu poliklinik & farmasi
- Halaman statis: Tentang RS, Alur Pendaftaran, Hak & Kewajiban Pasien, FAQ, Kebijakan Privasi Data Medis, Kontak & Ambulans; tombol WhatsApp

## Aturan Desain Responsif (Adaptive Desktop vs Mobile — Wajib)
1. Navigasi:
   - Desktop (>= 1024px): Collapsible Sidebar vertikal di kiri + Topbar dengan pencarian cepat pasien (No. RM / NIK / Nama) dan indikator darurat.
   - Mobile (< 768px): Sembunyikan sidebar di portal pasien & mode visite, ganti dengan Bottom Navigation Bar ramah sentuhan (tinggi min. 64px, 4–5 menu utama) dan header ringkas di atas.
2. Penyajian Data Antrean, Jadwal & Pasien:
   - Desktop: Data Table lengkap dengan filter multi-kolom, badge status warna, dan tombol aksi cepat per baris.
   - Mobile: Transformasikan baris tabel menjadi Stacked Card View (kartu vertikal) agar tidak perlu scroll horizontal. Tiap kartu memuat No. Antrean, Nama Pasien, No. RM, Poliklinik/Kamar, Status, dan tombol aksi utama.
3. Form Pemeriksaan Rekam Medis (SOAP):
   - Desktop: Layout split-screen 2 kolom (kiri: riwayat kunjungan, alergi, dan TTV; kanan: form input SOAP, ICD-10, tindakan, dan resep obat).
   - Mobile/Tablet: Layout 1 kolom dengan sistem Accordion atau Step-by-step Tabs (Riwayat | TTV | SOAP | Resep) agar mudah diisi dokter saat visite keliling.

## Aturan Teknis (wajib)
1. Logika bisnis di Services/Actions (QueueService, MedicalRecordService, PrescriptionService, BillingService, BedAllocationService), bukan di controller/komponen Livewire.
2. Integrasi pihak ketiga lewat interface (PaymentGateway, WhatsAppProvider, HealthBridgingInterface) agar mudah diganti.
3. Tarif tindakan, harga obat, diskon, dan total billing SELALU dihitung ulang di server.
4. Simpan snapshot nama tindakan, tarif, dan harga obat pada invoice_items/prescription_items serta snapshot TTV pada saat kunjungan.
5. Kuota antrean jadwal dokter, alokasi bed kamar, dan stok obat farmasi: gunakan DB::transaction + lockForUpdate() untuk mencegah kuota jebol, bed ganda, atau stok minus.
6. Webhook pembayaran: di luar CSRF, verifikasi signature, idempotent, log payload.
7. Otorisasi ketat dengan Policies (UU PDP): pasien hanya bisa mengakses data booking, anggota keluarga, dan tagihan miliknya (cegah IDOR); staf non-medis (kasir/admisi) tidak bisa membaca detail catatan klinis SOAP. Tulis test-nya.
8. Gunakan Form Request untuk validasi, $fillable untuk mass assignment, PHP backed Enum untuk status.
9. Rate limiting pada login, register, booking antrean, dan cek No. RM.
10. Semua secret lewat .env; sediakan .env.example. Jangan hardcode kredensial.
11. SEO: meta per halaman publik, Open Graph, sitemap, JSON-LD MedicalOrganization & Physician. Gambar WebP + lazy loading.
12. Aksesibilitas dan mobile-first; target Lighthouse mobile ≥ 90 untuk portal publik dan pasien.
13. Ikuti konvensi Laravel; jalankan Pint dan Pest sebelum menyatakan fase selesai.

## Desain
- Terasa seperti aplikasi kesehatan modern yang bersih, tepercaya, dan menenangkan, bukan template admin generik
- Kontras warna jelas untuk status medis (hijau: tersedia/selesai, kuning: menunggu/observasi, merah: alergi/darurat/kritis)
- Skeleton loading, empty state, pesan error ramah
- Tema Filament disesuaikan dengan palet warna rumah sakit

## Cara Kerja
- Kerjakan per fase yang kuberikan secara bertahap. Jangan lompat ke fase berikutnya sebelum aku bilang lanjut.
- Di awal tiap fase: tulis rencana singkat (file yang akan dibuat/diubah).
- Di akhir tiap fase: ringkas yang selesai, cara menjalankan & mengetes (termasuk tes tampilan Desktop vs Mobile), dan yang belum.
- Jika ada keputusan ambigu, ajukan maksimal 3 pertanyaan singkat, lalu lanjutkan dengan asumsi terbaik dan sebutkan asumsinya.
- Seed data harus jelas berlabel "CONTOH".
```

---

## 2. Prompt per Fase

### Fase 1 — Fondasi, Role Medis/Pasien & Layout Responsif

```text
Fase 1: Fondasi.
1. Inisialisasi proyek Laravel + Tailwind (Vite) + Livewire. Install Breeze (Livewire stack) untuk auth pasien,
   Filament (panel "admin" di /admin), dan spatie/laravel-permission.
2. Buat migrasi, model, factory untuk: users, patients (No. RM unik, NIK, gol. darah, alergi), patient_family_members,
   polyclinics, doctors, doctor_schedules, rooms, beds, medicines, medical_actions, visits, queues,
   medical_records (SOAP & TTV), prescriptions, prescription_items, invoices, invoice_items, payments, settings.
   Tambahkan index, relasi, dan PHP backed Enum (QueueStatus, VisitStatus, BedStatus, PrescriptionStatus, PaymentStatus).
3. Seeder: role super_admin, doctor, nurse, admission, pharmacist, cashier, patient; 1 akun contoh per role;
   4 poliklinik; 6 dokter dengan jadwal praktik; 4 kamar (10 bed); 10 obat "CONTOH"; 5 tindakan medis "CONTOH".
4. Pisahkan akses: hanya staf RS (super_admin/doctor/nurse/admission/pharmacist/cashier) yang bisa masuk /admin;
   pasien diarahkan ke /akun. Implementasikan lewat Filament canAccessPanel + middleware role.
5. Layout dasar responsif (Desktop vs Mobile) dengan design tokens dari palet RS:
   - Desktop (>=1024px): navigasi atas/samping lengkap.
   - Mobile (<768px): Bottom Navigation Bar ramah sentuhan (Beranda, Jadwal, Booking, Antrean, Akun).
6. Siapkan .env.example, Pint, Larastan, Pest, dan README cara menjalankan lokal.
7. Test: pasien tidak bisa membuka /admin; dokter & super_admin bisa; guest diarahkan ke login.
Berhenti setelah selesai dan beri ringkasan + cara tes.
```

### Fase 2 — Master Data RS, Jadwal Dokter & Portal Publik

```text
Fase 2: Master Data & Portal Publik.
1. Filament: PolyclinicResource, DoctorResource dengan relation manager Jadwal Praktik (hari, jam mulai/selesai, kuota, ruang),
   RoomResource dengan relation manager Bed (nomor bed, status), MedicineResource (SKU, satuan, harga, stok, min_stock, expired_at),
   dan MedicalActionResource (kode, nama tindakan, poli, tarif).
2. Portal Publik (Livewire):
   - /jadwal-dokter dan /poliklinik/{slug} dengan filter (poliklinik, hari praktik, pencarian nama dokter) dan indikator sisa kuota.
   - /ketersediaan-kamar menampilkan kapasitas & sisa bed tersedia per kelas kamar secara real-time.
   - Halaman detail dokter /dokter/{slug}: profil, spesialisasi, jadwal praktik, tombol cepat booking antrean.
   - Pastikan daftar jadwal dan kamar tampil sebagai tabel di Desktop dan otomatis menjadi Stacked Card View di Mobile.
3. Cache query jadwal dokter dan ringkasan ketersediaan bed.
4. Metadata SEO + JSON-LD MedicalOrganization & Physician.
5. Test fitur untuk filter jadwal dokter dan perhitungan bed tersedia.
Berhenti setelah selesai dan beri ringkasan + cara tes.
```

### Fase 3 — Area Pasien, Profil Keluarga & Booking Antrean

```text
Fase 3: Akun pasien & booking antrean online.
1. Lengkapi alur auth pasien: register, login, verifikasi email, reset password (Bahasa Indonesia). Opsional: login Google (Socialite) sebagai toggle.
2. Area /akun (Livewire, adaptif Desktop Sidebar & Mobile Bottom Nav):
   - Dashboard pasien (kartu berobat digital dengan QR No. RM, antrean aktif hari ini).
   - Profil & data medis dasar (NIK, tanggal lahir, golongan darah, riwayat alergi mandiri).
   - Anggota Keluarga (CRUD data anak/pasangan/orang tua agar bisa didaftarkan berobat lewat 1 akun).
3. Fitur Booking Antrean Online (QueueService + Livewire):
   - Pilih pasien (diri sendiri atau anggota keluarga), poliklinik, dokter, tanggal periksa, dan penjamin (Umum/BPJS/Asuransi).
   - Buat Visit & nomor Queue berurutan per jadwal dokter di dalam DB::transaction + lockForUpdate() untuk mencegah kuota jebol atau nomor ganda.
4. Policies: pasien hanya bisa mengakses data anggota keluarga dan antrean miliknya. Tulis test otorisasi.
5. Sinkronkan pasien walk-in → akun online: jika pasien lama sudah punya No. RM di RS, sediakan fitur tautkan No. RM berdasarkan verifikasi NIK & tanggal lahir.
Berhenti setelah selesai dan beri ringkasan + cara tes.
```

### Fase 4 — Admisi, Mesin Antrean Real-time & Manajemen Bed

```text
Fase 4: Admisi, antrean & rawat inap.
1. Filament QueueResource & VisitResource (Modul Admisi):
   - Pendaftaran pasien walk-in (pasien baru langsung dapat No. RM otomatis) dan check-in pasien booking online via kode booking/QR.
   - Aksi cepat: Panggil Antrean, Lewati (Skip), Mulai Periksa, Selesai.
   - Cetak tiket antrean & label/gelang pasien (dompdf).
2. Layar TV Antrean Publik (/layar-antrean) dengan Livewire polling: menampilkan antrean yang sedang dipanggil per poliklinik & antrean farmasi.
   Di halaman /akun/antrean/{queue}, pasien juga bisa memantau sisa antrean di depannya secara real-time lewat HP.
3. BedAllocationService: alokasi pasien rawat inap ke bed yang tersedia dalam DB::transaction + lockForUpdate(), ubah status bed menjadi occupied,
   dan ubah ke cleaning/available saat pasien dipulangkan (discharge).
4. Scheduler + job ExpireUncheckedBookings untuk membatalkan booking online yang tidak check-in setelah jam praktik berakhir.
5. Test: dua pasien booking bersamaan pada sisa 1 kuota dokter; dua admisi mengalokasikan bed yang sama secara bersamaan.
Berhenti setelah selesai dan beri ringkasan + cara tes.
```

### Fase 5 — Rekam Medis Elektronik (RME), Farmasi & Kasir/Billing

```text
Fase 5: RME, E-Prescribing & Pembayaran.
1. Pemeriksaan Medis di Filament (MedicalRecordResource / halaman periksa Visit):
   - Desktop: layout split-screen 2 kolom (kiri: riwayat kunjungan pasien, banner merah alergi, TTV dari perawat; kanan: form SOAP dokter, pencarian ICD-10, pilih tindakan medis, dan input resep obat).
   - Mobile/Tablet: layout 1 kolom dengan Tabs (Riwayat & Alergi | TTV | Form SOAP | E-Resep) agar nyaman saat dokter visite.
   - Cetak Surat Keterangan Sakit / Rujukan (dompdf).
2. Instalasi Farmasi (PrescriptionService & PrescriptionResource):
   - Resep dari dokter otomatis masuk antrean farmasi (status: pending → preparing → ready → handed_over).
   - Saat farmasi memverifikasi resep, potong stok obat di dalam DB::transaction + lockForUpdate(). Cetak etiket obat PDF.
3. Kasir & Billing (BillingService & InvoiceResource):
   - Hitung otomatis di server: biaya pendaftaran + tindakan medis dari RME + obat dari resep + sewa kamar rawat inap.
   - Pembayaran tunai di loket kasir ATAU pembayaran online via Interface PaymentGateway [Midtrans/Xendit] (QRIS, VA bank, e-wallet) dari halaman /akun/tagihan pasien.
4. Endpoint webhook di routes/webhooks.php (di luar CSRF): verifikasi signature, idempotent, simpan payload, ubah status invoice menjadi paid, jalankan event InvoicePaid.
5. Notifikasi (Laravel Notifications via queue): booking berhasil, giliran antrean dipanggil, obat siap diambil, dan pembayaran lunas lewat email dan WhatsApp.
6. Policies & Privasi Medis: staf kasir dan admisi TIDAK BISA melihat detail catatan klinis SOAP; pasien A tidak bisa melihat RME/invoice pasien B. Tulis test-nya.
Berhenti setelah selesai dan beri ringkasan + cara tes sandbox.
```

### Fase 6 — Dashboard Eksekutif, Konten Edukasi & SEO

```text
Fase 6: Dashboard, konten & SEO.
1. Widget Filament sesuai role:
   - Manajemen/Super Admin: pendapatan harian/bulanan, jumlah kunjungan per poliklinik, Bed Occupancy Rate (BOR), 10 diagnosis ICD-10 terbanyak.
   - Farmasi: daftar stok obat menipis dan obat mendekati tanggal kedaluwarsa (expired).
   - Dokter: ringkasan antrean pasiennya hari ini dan daftar pasien rawat inap yang sedang ditangani.
2. Polish beranda publik, artikel edukasi kesehatan, dan halaman statis (Tentang RS, Alur Berobat, Hak Pasien, FAQ, Privasi Data Medis, Kontak) yang bisa diedit dari admin; banner informasi.
3. Sitemap otomatis, robots.txt, canonical, Open Graph, JSON-LD (MedicalOrganization, Physician, BreadcrumbList).
4. GA4 + tombol darurat IGD & WhatsApp melayang; optimasi gambar WebP dan performa; laporkan skor Lighthouse mobile.
Berhenti setelah selesai dan beri ringkasan.
```

### Fase 7 — Hardening Keamanan Data Medis, QA & Deploy

```text
Fase 7: Finalisasi.
1. Audit keamanan & kepatuhan UU PDP: rate limiting, security headers (CSP, HSTS), audit log akses rekam medis (catat siapa membuka RME siapa), cek IDOR di semua route pasien & panel medis, validasi upload berkas lab, APP_DEBUG=false di produksi.
2. Lengkapi test suite Pest (unit + feature) dan pastikan Pint + Larastan lulus.
3. Pasang Sentry, halaman 404/500 ramah, dan spatie/laravel-backup (backup database harian terjadwal).
4. Siapkan panduan deploy: Nginx + PHP-FPM + Supervisor (queue:work) + cron scheduler, perintah optimasi (config/route/view/event cache), storage:link, daftar env variable, dan checklist go-live rumah sakit.
5. Tulis "Panduan Operasional Singkat" untuk admisi, perawat, dokter, farmasi, dan kasir.
Berikan laporan akhir: fitur selesai, yang tertunda, dan saran Fase 2.
```

---

## 3. Prompt Tambahan (Opsional)

**Review kode & keamanan data medis**
```text
Lakukan code review menyeluruh proyek SIMRS Laravel ini. Fokus: IDOR pada rekam medis dan tagihan, pemisahan policy antara tenaga medis (dokter/perawat) dan staf non-medis (kasir/admisi), validasi webhook pembayaran, race condition kuota antrean & stok obat farmasi, N+1 query, dan responsivitas komponen di layar mobile. Urutkan temuan dari severity tertinggi beserta perbaikannya.
```

**Generator profil poliklinik & edukasi kesehatan**
```text
Buat konten halaman layanan untuk poliklinik [NAMA POLIKLINIK]: layanan unggulan [DAFTAR LAYANAN], ditangani oleh dokter spesialis [SPESIALISASI]. Tone: [empatik/profesional/edukatif]. Sertakan: ringkasan 2 kalimat, 3–5 kondisi medis yang ditangani, persiapan pasien sebelum periksa, dan meta description ≤ 155 karakter. Jangan membuat klaim medis yang menjanjikan kesembuhan instan.
```

**Fitur Fase 2**
```text
Tambahkan fitur: [Bridging FHIR SATUSEHAT Kemenkes / Bridging Antrean & VClaim BPJS / Modul Order Laboratorium & Radiologi / Telemedicine Video Konsultasi / Manajemen Gudang & PO Farmasi]. Berikan rencana singkat dulu (migrasi, model, service interface, Livewire/Filament, policy, test), tunggu persetujuanku, lalu implementasikan.
```

---

## 4. Checklist Input yang Perlu Kamu Siapkan

- [ ] Nama rumah sakit/klinik, logo, palet warna, font
- [ ] Domain dan server/hosting (VPS sangat disarankan untuk SIMRS)
- [ ] Daftar poliklinik, nama dokter + SIP, dan jadwal praktik awal
- [ ] Daftar kamar & jumlah tempat tidur (bed) per kelas rawat inap
- [ ] Katalog awal tindakan medis beserta tarif dan daftar obat awal farmasi
- [ ] Akun & API key sandbox: payment gateway (Midtrans/Xendit), email, WhatsApp (Fonnte/WA Cloud)
- [ ] Teks Alur Pendaftaran, Hak & Kewajiban Pasien, FAQ, dan Kebijakan Privasi Data Medis
- [ ] Daftar akun staf awal (nama, email, role: super_admin, doctor, nurse, admission, pharmacist, cashier)