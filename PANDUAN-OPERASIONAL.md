# Panduan Operasional Singkat SIMRS — RS Aisyah Medika

Panduan praktis operasional harian Sistem Informasi Manajemen Rumah Sakit (SIMRS) RS Aisyah Medika untuk seluruh tenaga kesehatan dan staf penunjang medis.

---

## 1. Unit Pendaftaran & Admisi Pasien

### A. Pasien Datang Langsung (Walk-In)
1. Buka menu **Panel Admisi** di `/admin/loket-panggilan` atau menu **Pasien** di `/admin/patients`.
2. Klik tombol **"Daftar Pasien Baru"**.
3. Masukkan data identitas lengkap: NIK KTP, Nama Pasien, Tanggal Lahir, Jenis Kelamin, Golongan Darah, Riwayat Alergi (jika ada), No. HP WhatsApp, dan Alamat.
4. Sistem otomatis menerbitkan **Nomor Rekam Medis (No. RM)** unik format `RM-YYYYMM-XXXX`.
5. Pilih poliklinik tujuan dan dokter spesialis yang sedang bertugas.
6. Sistem akan otomatis menerbitkan nomor antrean poliklinik dan mencetak struk tiket antrean.

### B. Pasien Booking Online yang Datang ke RS (Check-In)
1. Pasien dapat langsung menuju mesin **Kiosk Check-In Mandiri** di lobi utama (`/check-in`).
2. Pasien melakukan scan QR Code dari tiket di HP atau memasukkan NIK/Kode Booking.
3. Status antrean berubah menjadi **"Waiting" (Menunggu Panggilan)** dan otomatis muncul di layar monitor poliklinik.

---

## 2. Unit Keperawatan (Perawat Poliklinik)

1. Buka menu **Antrean Poliklinik** di `/admin/loket-panggilan`.
2. Klik tombol **"Panggil Antrean"** untuk membunyikan suara panggilan otomatis di ruang tunggu.
3. Lakukan pemeriksaan awal Tanda-Tanda Vital (TTV):
   - Tekanan Darah (Sistolik/Diastolik, misal: `120/80 mmHg`)
   - Denyut Nadi (`x/menit`)
   - Laju Pernapasan (`x/menit`)
   - Suhu Tubuh (`°C`)
   - Berat Badan (`kg`) dan Tinggi Badan (`cm`)
4. Input skrining riwayat alergi pasien (makanan atau obat tertentu seperti Penisilin).
5. Klik **"Simpan & Serahkan ke Dokter"** sehingga data langsung tampil di layar dokter poli.

---

## 3. Tenaga Medis (Dokter Poliklinik)

1. Buka menu **Rekam Medis (RME)** di `/admin/medical-records` atau dari dasbor poli.
2. Di layar desktop komputer dokter, sistem menampilkan tampilan **Split-Screen Ergonomis**:
   - **Sisi Kiri**: Riwayat alergi pasien (banner merah kontras tinggi), hasil TTV perawat, dan riwayat rekam medis terdahulu.
   - **Sisi Kanan**: Formulir input pemeriksaan klinis standar **SOAP**:
     - **S (Subjective)**: Keluhan utama dan riwayat perjalanan penyakit dari pasien.
     - **O (Objective)**: Hasil pemeriksaan fisik langsung dan status lokalis dokter.
     - **A (Assessment)**: Diagnosis kerja dokter dengan pencarian cepat katalog **ICD-10** (otomatis muncul kode dan nama resmi internasional).
     - **P (Plan)**: Rencana terapi, instruksi tindakan medis poliklinik, dan edukasi pasien.
3. **E-Prescribing (Resep Elektronik)**:
   - Pilih obat dari katalog farmasi RS Aisyah Medika.
   - Tentukan dosis, aturan pakai (misal: *3x1 tablet sesudah makan*), dan jumlah obat.
4. Klik **"Selesai Periksa"**. Resep otomatis terkirim secara *real-time* ke Instalasi Farmasi dan draft tagihan terakumulasi ke Kasir.

---

## 4. Instalasi Farmasi (Apoteker & Tenaga Teknis Kefarmasian)

1. Buka menu **Farmasi & Resep** di `/admin/prescriptions`.
2. Resep yang baru dikirim dokter akan berstatus **"Pending"**.
3. Petugas farmasi memeriksa ketersediaan stok obat:
   - Klik **"Verifikasi Resep"**.
   - Sistem secara otomatis memotong stok fisik obat di database dalam transaksi aman anti-minus.
4. Siapkan sediaan obat dan klik tombol **"Cetak Etiket Obat"** (`/farmasi/etiket/{id}`):
   - Etiket otomatis memuat identitas pasien, No. RM, tanggal periksa, aturan pakai lengkap, dan peringatan batas kedaluwarsa.
5. Tempelkan etiket pada kemasan obat, lalu ubah status resep menjadi **"Ready" (Obat Siap Diambil)**.
6. Saat pasien menunjukkan kwitansi lunas dari kasir, serahkan obat dan ubah status menjadi **"Handed Over" (Selesai Diserahkan)**.

---

## 5. Loket Kasir & Keuangan Rumah Sakit

1. Buka menu **Kasir & Billing** di `/admin/invoices`.
2. Tagihan pasien yang telah selesai diperiksa dokter dan disiapkan resepnya akan muncul berstatus **"Belum Dibayar (Unpaid)"**.
3. Sistem secara otomatis menghitung akumulasi biaya:
   - Jasa konsultasi dokter & administrasi poli.
   - Tindakan medis dari form RME dokter.
   - Biaya obat dan alkes dari instalasi farmasi.
   - Sewa tempat tidur/kamar inap (jika rawat inap).
4. **Pembayaran Tunai**:
   - Klik aksi **"Bayar Tunai"** pada baris invoice.
   - Masukkan nominal uang yang diterima dari pasien.
   - Sistem otomatis menghitung uang kembalian (*change*) dan memvalidasi jika nominal kurang.
   - Klik konfirmasi: status tagihan langsung berubah menjadi **"Lunas (Paid)"**.
5. **Pembayaran QRIS / Non-Tunai**:
   - Klik aksi **"Bayar QRIS / Digital"** atau pasien dapat melakukan pembayaran mandiri lewat scan QRIS pada ponsel mereka.
6. Klik tombol **"Cetak Kwitansi"** (`/kasir/kwitansi/{id}`) untuk mencetak struk termal kasir ukuran 58mm/80mm untuk diserahkan ke pasien.

---

## 6. Pasien & Keluarga (Portal Pasien / Mandiri)

1. Akses portal pasien di `https://simrs.aisyahmedika.id/akun`.
2. **Kartu Berobat Digital**: Menampilkan Nomor Rekam Medis (No. RM) dan QR Code resmi untuk ditunjukkan saat berobat.
3. **Buku Anggota Keluarga**: Pasien dapat mendaftarkan anak, pasangan, atau orang tua dalam 1 akun sehingga pendaftaran keluarga menjadi sangat mudah.
4. **Booking Antrean Online**:
   - Pasien memilih dokter spesialis, poliklinik, dan tanggal praktik.
   - Pasien menerima nomor antrean dan estimasi jam layanan.
5. **Pantau Antrean Real-Time**: Pasien dapat melihat sisa nomor antrean di depannya langsung dari HP sambil menunggu di kafetaria atau rumah.
6. **Tagihan & Bayar Online**: Pasien dapat meninjau rincian tagihan medis dan melakukan pembayaran via QRIS/Virtual Account langsung dari ponsel.

---

## 7. Perlindungan Privasi Medis (Kepatuhan UU PDP No. 27 Tahun 2022)

- **Kerahasiaan Diagnostik**: Staf kasir dan admisi secara sistematis **dilarang dan diblokir** dari membaca catatan klinis SOAP dokter.
- **Pencegahan Akses Ilegal (Anti-IDOR)**: Pasien hanya dapat mengakses data keluarga dan tagihan miliknya sendiri. Upaya membuka data pasien lain akan diblokir dengan pesan error 403 Forbidden.
- **Audit Logging**: Seluruh riwayat staf yang membuka atau mencetak berkas medis pasien dicatat secara permanen di menu **Audit Log Rekam Medis** (`/admin/medical-record-access-logs`) untuk kepentingan akuntabilitas hukum rumah sakit.
