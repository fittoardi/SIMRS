<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ url('/') }}" class="hover:text-teal-600">Beranda</a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200 font-semibold">Kebijakan Privasi</span>
                </nav>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Kebijakan Privasi & Perlindungan Data Medis</h1>
            </div>
            <span class="text-xs text-slate-500">Kepatuhan UU PDP No. 27/2022 & Permenkes No. 24/2022</span>
        </div>
    </x-slot>

    <div class="py-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="bg-white dark:bg-gray-800 p-8 sm:p-10 rounded-3xl border border-slate-200 dark:border-gray-700 shadow-sm space-y-8 text-slate-700 dark:text-slate-300 text-sm leading-relaxed">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">1. Komitmen Privasi Pasien</h2>
                <p>
                    Rumah Sakit Aisyah Medika berkomitmen melindungi kerahasiaan, keutuhan, dan keamanan data pribadi serta data kesehatan pasien sesuai amanat <strong>Undang-Undang No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP)</strong> dan <strong>Peraturan Menteri Kesehatan No. 24 Tahun 2022 tentang Rekam Medis Elektronik (RME)</strong>.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">2. Data yang Kami Kumpulkan</h2>
                <p>Untuk kepentingan penyelenggaraan pelayanan medis dan administrasi rumah sakit, kami mengumpulkan:</p>
                <ul class="list-disc list-inside mt-2 space-y-1.5 ml-2">
                    <li><strong>Data Identitas Diri:</strong> Nama lengkap, NIK, tanggal lahir, jenis kelamin, alamat domisili, nomor telepon, dan hubungan keluarga.</li>
                    <li><strong>Data Kesehatan Spesifik (Data Sensitif):</strong> Riwayat penyakit, diagnosis klinis, riwayat alergi obat/makanan, tanda-tanda vital, hasil pemeriksaan laboratorium/penunjang, dan resep obat.</li>
                    <li><strong>Data Finansial:</strong> Nomor kartu BPJS Kesehatan, polis asuransi komersial, rincian tagihan, dan bukti transaksi pembayaran.</li>
                </ul>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">3. Pengendalian Hak Akses Berjenjang (Role-Based Access Control)</h2>
                <p>
                    Sistem Informasi Manajemen Rumah Sakit (SIMRS) RS Aisyah Medika menerapkan pembatasan hak akses ketat guna mencegah akses tidak sah (*unauthorized access*) dan kebocoran data:
                </p>
                <ul class="list-disc list-inside mt-2 space-y-1.5 ml-2">
                    <li><strong>Tenaga Medis (Dokter & Perawat):</strong> Memiliki akses penuh terhadap catatan klinis SOAP dan riwayat pemeriksaan pasien yang ditangani demi keselamatan terapi.</li>
                    <li><strong>Staf Non-Medis (Admisi & Kasir):</strong> <em>Dilarang keras dan diblokir secara sistem</em> dari membaca detail catatan subjektif, objektif, dan pemeriksaan fisik dokter. Akses dibatasi hanya pada identitas pendaftaran dan total biaya tagihan.</li>
                    <li><strong>Portal Pasien:</strong> Pasien dan perwakilan keluarga yang sah hanya dapat mengakses data medis dan invoice milik akun mereka sendiri (dilindungi proteksi Anti-IDOR).</li>
                </ul>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">4. Penyimpanan & Keamanan Data</h2>
                <p>
                    Seluruh data medis disimpan di server terisolasi dengan enkripsi saat transit (*TLS/HTTPS*) dan enkripsi data (*at-rest*). Berkas hasil pemeriksaan radiologi dan laboratorium disimpan dalam direktori privat (*private storage*) yang hanya dapat diakses melalui tautan terautentikasi bertanda tangan digital (*signed URL*).
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">5. Hak Subjek Data</h2>
                <p>
                    Sebagai pasien, Anda berhak memperoleh ringkasan rekam medis Anda, memperbarui data demografis yang keliru, dan meminta penjelasan mengenai pemrosesan data medis Anda melalui Unit Pengaduan & Layanan Informasi Medis RS Aisyah Medika.
                </p>
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-gray-700 text-xs text-slate-500">
                Terakhir diperbarui: 13 November 2026 • Petugas Perlindungan Data Pribadi (DPO) RS Aisyah Medika.
            </div>
        </div>
    </div>
</x-app-layout>
