<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ url('/') }}" class="hover:text-teal-600">Beranda</a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200 font-semibold">Alur Berobat</span>
                </nav>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Panduan Alur Pendaftaran & Pemeriksaan</h1>
            </div>
            <a href="{{ url('/booking-online') }}" class="inline-flex items-center gap-2 text-xs font-semibold px-4 py-2 rounded-xl bg-teal-600 text-white hover:bg-teal-700 transition self-start sm:self-auto shadow-sm">
                Booking Antrean Sekarang
            </a>
        </div>
    </x-slot>

    <div class="py-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="text-center max-w-2xl mx-auto">
            <span class="text-xs uppercase font-bold tracking-wider text-teal-600 dark:text-teal-400">Mudah, Cepat & Teratur</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mt-1">Langkah Berobat Pasien di RS Aisyah Medika</h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-2">
                Panduan praktis mulai dari pendaftaran antrean hingga pengambilan obat dan penyelesaian administrasi.
            </p>
        </div>

        <!-- Alur 5 Langkah (Step by Step) -->
        <div class="space-y-6">
            <!-- Langkah 1 -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row gap-6 items-start">
                <div class="w-12 h-12 rounded-2xl bg-teal-600 text-white flex items-center justify-center font-black text-xl shrink-0">
                    1
                </div>
                <div class="space-y-2 flex-1">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Pendaftaran & Booking Antrean</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300">Online / Di Loket</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                        Pasien dapat mendaftar dari rumah lewat menu <strong>Booking Online</strong> (bisa untuk diri sendiri atau anggota keluarga), memilih dokter dan tanggal periksa. Pasien yang datang langsung (walk-in) dapat menuju loket admisi untuk dibuatkan No. Rekam Medis (No. RM) baru.
                    </p>
                </div>
            </div>

            <!-- Langkah 2 -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row gap-6 items-start">
                <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-black text-xl shrink-0">
                    2
                </div>
                <div class="space-y-2 flex-1">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Check-In Mandiri & Menunggu Panggilan</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">Kiosk / Loket</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                        Saat tiba di rumah sakit, lakukan konfirmasi kedatangan di mesin <strong>Kiosk Check-In</strong> menggunakan scan QR Code atau memasukkan NIK/Kode Booking. Pantau nomor antrean melalui <strong>Layar Monitor TV</strong> di ruang tunggu atau langsung dari HP Anda.
                    </p>
                </div>
            </div>

            <!-- Langkah 3 -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row gap-6 items-start">
                <div class="w-12 h-12 rounded-2xl bg-sky-600 text-white flex items-center justify-center font-black text-xl shrink-0">
                    3
                </div>
                <div class="space-y-2 flex-1">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Pemeriksaan TTV & Konsultasi Dokter Spesialis</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 dark:bg-sky-950 dark:text-sky-300">Ruang Poliklinik</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                        Perawat akan memanggil nomor antrean untuk pemeriksaan awal tanda-tanda vital (tensi darah, nadi, suhu tubuh) dan skrining riwayat alergi. Selanjutnya dokter melakukan anamnesis, pemeriksaan fisik, diagnosis ICD-10, tindakan medis jika diperlukan, serta meresepkan obat secara elektronik (E-Prescribing).
                    </p>
                </div>
            </div>

            <!-- Langkah 4 -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row gap-6 items-start">
                <div class="w-12 h-12 rounded-2xl bg-amber-600 text-white flex items-center justify-center font-black text-xl shrink-0">
                    4
                </div>
                <div class="space-y-2 flex-1">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Pembayaran di Kasir (Tunai / QRIS / Online)</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300">Loket Kasir / HP Pasien</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                        Setelah selesai pemeriksaan, rincian biaya konsultasi, tindakan medis, dan obat otomatis terakumulasi dalam invoice terpadu. Pasien dapat membayar secara tunai di loket kasir atau langsung via scan QRIS / Virtual Account dari aplikasi di HP Anda.
                    </p>
                </div>
            </div>

            <!-- Langkah 5 -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row gap-6 items-start">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-xl shrink-0">
                    5
                </div>
                <div class="space-y-2 flex-1">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Pengambilan Obat di Instalasi Farmasi</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">Loket Farmasi</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                        Tunjukkan bukti bayar/kwitansi kasir ke loket farmasi. Apoteker kami akan menyerahkan obat dengan penjelasan lengkap mengenai aturan pakai, dosis, dan petunjuk penyimpanan obat.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
