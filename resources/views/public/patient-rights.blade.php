<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ url('/') }}" class="hover:text-teal-600">Beranda</a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200 font-semibold">Hak & Kewajiban</span>
                </nav>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Hak dan Kewajiban Pasien</h1>
            </div>
            <span class="text-xs text-slate-500">Berdasarkan UU Kesehatan No. 17 Tahun 2023</span>
        </div>
    </x-slot>

    <div class="py-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        <div class="bg-gradient-to-r from-teal-50 to-emerald-50 dark:from-teal-950/40 dark:to-emerald-950/40 border border-teal-100 dark:border-teal-900 p-6 rounded-3xl text-sm text-teal-900 dark:text-teal-200 leading-relaxed">
            Rumah Sakit Aisyah Medika berkomitmen menjunjung tinggi hak-hak asasi setiap pasien tanpa diskriminasi suku, agama, ras, maupun golongan, serta mewujudkan hubungan kemitraan saling menghormati antara pasien, keluarga, dan tenaga medis.
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Hak Pasien -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-slate-200 dark:border-gray-700 shadow-sm space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold">
                        ⚖️
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Hak-Hak Pasien</h3>
                </div>
                <ul class="text-sm text-slate-600 dark:text-slate-300 space-y-3 list-decimal list-inside leading-relaxed">
                    <li>Memperoleh informasi mengenai tata tertib dan peraturan yang berlaku di rumah sakit.</li>
                    <li>Memperoleh layanan yang manusiawi, adil, jujur, dan tanpa diskriminasi.</li>
                    <li>Memperoleh layanan kesehatan yang bermutu sesuai dengan standar profesi dan standar prosedur operasional.</li>
                    <li>Memperoleh layanan yang efektif dan efisien sehingga pasien terhindar dari kerugian fisik dan materi.</li>
                    <li>Memilih dokter dan kelas perawatan sesuai dengan keinginannya dan peraturan yang berlaku di rumah sakit.</li>
                    <li>Meminta konsultasi tentang penyakit yang dideritanya kepada dokter lain yang mempunyai Surat Izin Praktik (SIP) baik di dalam maupun di luar rumah sakit (*second opinion*).</li>
                    <li>Mendapatkan privasi dan kerahasiaan penyakit yang diderita termasuk data-data medisnya.</li>
                    <li>Mendapat informasi yang meliputi diagnosis dan tata cara tindakan medis, tujuan tindakan medis, alternatif tindakan, risiko dan komplikasi yang mungkin terjadi, serta perkiraan biaya pengobatan.</li>
                    <li>Memberikan persetujuan atau menolak atas tindakan yang akan dilakukan oleh tenaga medis terhadap penyakit yang dideritanya (*informed consent*).</li>
                    <li>Menjalankan ibadah sesuai agama atau kepercayaan yang dianutnya selama tidak mengganggu pasien lain.</li>
                </ul>
            </div>

            <!-- Kewajiban Pasien -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-slate-200 dark:border-gray-700 shadow-sm space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        📋
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Kewajiban Pasien</h3>
                </div>
                <ul class="text-sm text-slate-600 dark:text-slate-300 space-y-3 list-decimal list-inside leading-relaxed">
                    <li>Menaati segala peraturan dan tata tertib yang berlaku di Rumah Sakit Aisyah Medika.</li>
                    <li>Menggunakan fasilitas rumah sakit secara bertanggung jawab.</li>
                    <li>Menghormati hak-hak pasien lain, pengunjung, dan tenaga kesehatan serta petugas lainnya yang bekerja di rumah sakit.</li>
                    <li>Memberikan informasi yang jujur, lengkap, dan akurat tentang masalah kesehatannya (riwayat penyakit, alergi obat, dan pengobatan yang sedang dijalani).</li>
                    <li>Memberikan informasi mengenai kemampuan finansial dan jaminan kesehatan yang dimilikinya (BPJS/Asuransi/Umum).</li>
                    <li>Mematuhi rencana terapi yang direkomendasikan oleh tenaga kesehatan di rumah sakit dan disetujui oleh pasien yang bersangkutan setelah mendapatkan penjelasan.</li>
                    <li>Menerima segala konsekuensi hukum atas keputusan pribadinya untuk menolak rencana terapi atau nasihat medis dokter.</li>
                    <li>Menyelesaikan kewajiban pembayaran atas pelayanan medis yang telah diterima sesuai dengan ketentuan tarif yang berlaku.</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>

