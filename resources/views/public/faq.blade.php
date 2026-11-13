<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ url('/') }}" class="hover:text-teal-600">Beranda</a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200 font-semibold">FAQ</span>
                </nav>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Tanya Jawab (FAQ) Pasien</h1>
            </div>
            <a href="https://wa.me/6281200000001?text=Halo%20saya%20ingin%20bertanya%20seputar%20layanan%20RS%20Aisyah%20Medika" target="_blank" class="inline-flex items-center gap-2 text-xs font-semibold px-4 py-2 rounded-xl bg-teal-600 text-white hover:bg-teal-700 transition self-start sm:self-auto shadow-sm">
                Tanya via WhatsApp
            </a>
        </div>
    </x-slot>

    <div class="py-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{ active: 1 }">
        <div class="text-center max-w-2xl mx-auto mb-8">
            <span class="text-xs uppercase font-bold tracking-wider text-teal-600 dark:text-teal-400">Pusat Bantuan Informasi</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mt-1">Pertanyaan yang Sering Diajukan</h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-2">
                Temukan jawaban cepat seputar pendaftaran, jaminan BPJS Kesehatan, asuransi, dan fasilitas rawat inap.
            </p>
        </div>

        <div class="space-y-4">
            <!-- FAQ Item 1 -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 overflow-hidden shadow-sm">
                <button @click="active = (active === 1 ? null : 1)" class="w-full p-5 text-left font-bold text-slate-900 dark:text-white flex items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-gray-700/50 transition">
                    <span class="text-base">Bagaimana cara mendaftar dan booking antrean dokter secara online?</span>
                    <span class="text-teal-600 text-xl font-bold" x-text="active === 1 ? '−' : '+'"></span>
                </button>
                <div x-show="active === 1" x-collapse class="px-5 pb-5 text-sm text-slate-600 dark:text-slate-300 border-t border-slate-100 dark:border-gray-700 pt-3 leading-relaxed">
                    Anda dapat memesan nomor antrean poliklinik secara mandiri melalui menu <strong>Booking Online</strong> di website ini tanpa harus membuat akun (Guest Booking) atau melalui portal <strong>Area Pasien</strong>. Cukup pilih poliklinik, dokter spesialis, tanggal berobat, dan isi identitas pasien. Anda akan langsung menerima Kode Booking dan tiket antrean digital dengan QR Code.
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 overflow-hidden shadow-sm">
                <button @click="active = (active === 2 ? null : 2)" class="w-full p-5 text-left font-bold text-slate-900 dark:text-white flex items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-gray-700/50 transition">
                    <span class="text-base">Apakah RS Aisyah Medika melayani pasien BPJS Kesehatan?</span>
                    <span class="text-teal-600 text-xl font-bold" x-text="active === 2 ? '−' : '+'"></span>
                </button>
                <div x-show="active === 2" x-collapse class="px-5 pb-5 text-sm text-slate-600 dark:text-slate-300 border-t border-slate-100 dark:border-gray-700 pt-3 leading-relaxed">
                    Ya, RS Aisyah Medika bekerja sama penuh dengan BPJS Kesehatan untuk layanan rawat jalan, rawat inap, dan IGD. Untuk rawat jalan poliklinik, pasien wajib membawa surat rujukan dari Fasilitas Kesehatan Tingkat Pertama (FKTP) seperti Puskesmas atau Klinik Pratama, serta kartu BPJS/KTP yang masih aktif. Untuk kondisi kegawatdaruratan IGD, pasien BPJS dapat langsung dilayani tanpa memerlukan surat rujukan.
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 overflow-hidden shadow-sm">
                <button @click="active = (active === 3 ? null : 3)" class="w-full p-5 text-left font-bold text-slate-900 dark:text-white flex items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-gray-700/50 transition">
                    <span class="text-base">Apa yang harus dilakukan saat tiba di rumah sakit setelah booking online?</span>
                    <span class="text-teal-600 text-xl font-bold" x-text="active === 3 ? '−' : '+'"></span>
                </button>
                <div x-show="active === 3" x-collapse class="px-5 pb-5 text-sm text-slate-600 dark:text-slate-300 border-t border-slate-100 dark:border-gray-700 pt-3 leading-relaxed">
                    Setibanya di rumah sakit, silakan menuju mesin <strong>Kiosk Check-In</strong> di lobi utama. Scan QR Code yang tertera pada tiket digital Anda atau ketikkan nomor NIK / Kode Booking. Mesin akan mencetak struk fisik dan nomor antrean Anda otomatis teraktivasi di ruang tunggu dokter.
                </div>
            </div>

            <!-- FAQ Item 4 -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 overflow-hidden shadow-sm">
                <button @click="active = (active === 4 ? null : 4)" class="w-full p-5 text-left font-bold text-slate-900 dark:text-white flex items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-gray-700/50 transition">
                    <span class="text-base">Bagaimana cara mengecek ketersediaan kamar rawat inap yang kosong?</span>
                    <span class="text-teal-600 text-xl font-bold" x-text="active === 4 ? '−' : '+'"></span>
                </button>
                <div x-show="active === 4" x-collapse class="px-5 pb-5 text-sm text-slate-600 dark:text-slate-300 border-t border-slate-100 dark:border-gray-700 pt-3 leading-relaxed">
                    Anda dapat memantau ketersediaan bed secara real-time pada menu <strong>Ketersediaan Bed</strong> di navigasi atas. Data selalu diperbarui otomatis mengikuti status terisi (occupied), kosong (available), atau sedang dibersihkan (cleaning).
                </div>
            </div>

            <!-- FAQ Item 5 -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 overflow-hidden shadow-sm">
                <button @click="active = (active === 5 ? null : 5)" class="w-full p-5 text-left font-bold text-slate-900 dark:text-white flex items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-gray-700/50 transition">
                    <span class="text-base">Apa saja metode pembayaran yang diterima di kasir?</span>
                    <span class="text-teal-600 text-xl font-bold" x-text="active === 5 ? '−' : '+'"></span>
                </button>
                <div x-show="active === 5" x-collapse class="px-5 pb-5 text-sm text-slate-600 dark:text-slate-300 border-t border-slate-100 dark:border-gray-700 pt-3 leading-relaxed">
                    Kasir RS Aisyah Medika menerima pembayaran tunai di loket kasir, QRIS (GoPay, OVO, Dana, ShopeePay, BCA, dll), Transfer Virtual Account (BCA, BNI, Mandiri, BRI), Debit/Kredit EDC, klaim BPJS Kesehatan, dan koordinasi asuransi swasta rekanan (AdMedika, Prudential, Allianz, AIA, Sinarmas).
                </div>
            </div>

            <!-- FAQ Item 6 -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 overflow-hidden shadow-sm">
                <button @click="active = (active === 6 ? null : 6)" class="w-full p-5 text-left font-bold text-slate-900 dark:text-white flex items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-gray-700/50 transition">
                    <span class="text-base">Kapan jam besuk pasien rawat inap?</span>
                    <span class="text-teal-600 text-xl font-bold" x-text="active === 6 ? '−' : '+'"></span>
                </button>
                <div x-show="active === 6" x-collapse class="px-5 pb-5 text-sm text-slate-600 dark:text-slate-300 border-t border-slate-100 dark:border-gray-700 pt-3 leading-relaxed">
                    Jam kunjungan pasien rawat inap terbagi menjadi dua sesi demi menjaga waktu istirahat pasien:
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li><strong>Siang:</strong> Pukul 11.00 – 13.00 WIB</li>
                        <li><strong>Sore/Malam:</strong> Pukul 17.00 – 20.00 WIB</li>
                    </ul>
                    Anak-anak di bawah usia 12 tahun tidak diperkenankan memasuki bangsal perawatan demi pencegahan risiko infeksi nosokomial.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

