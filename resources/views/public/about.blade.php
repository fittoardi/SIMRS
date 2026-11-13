<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ url('/') }}" class="hover:text-teal-600">Beranda</a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200 font-semibold">Tentang Kami</span>
                </nav>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Profil & Visi RS Aisyah Medika</h1>
            </div>
            <a href="{{ url('/jadwal-dokter') }}" class="inline-flex items-center gap-2 text-xs font-semibold px-4 py-2 rounded-xl bg-teal-600 text-white hover:bg-teal-700 transition self-start sm:self-auto shadow-sm">
                Lihat Jadwal Dokter
            </a>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-teal-800 via-teal-900 to-slate-900 text-white rounded-3xl p-8 sm:p-12 shadow-xl relative overflow-hidden">
            <div class="max-w-3xl relative z-10 space-y-4">
                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30">
                    🏥 Terakreditasi Paripurna KARS
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight leading-tight">
                    Dedikasi Pelayanan Kesehatan Modern, Humanis & Tepercaya
                </h2>
                <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                    Rumah Sakit Aisyah Medika hadir sebagai pusat layanan kesehatan rujukan terpadu yang memadukan keahlian dokter spesialis, teknologi diagnostik modern, dan keramahan islami dalam mendampingi kesembuhan setiap pasien.
                </p>
            </div>
        </div>

        <!-- Statistik Fasilitas -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm text-center">
                <p class="text-3xl font-extrabold text-teal-600">{{ $totalDoctors ?? 12 }}+</p>
                <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 mt-1">Dokter Spesialis & Umum</p>
                <p class="text-[11px] text-slate-400">Siap melayani konsultasi rawat jalan</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm text-center">
                <p class="text-3xl font-extrabold text-emerald-600">{{ $totalPolyclinics ?? 4 }}</p>
                <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 mt-1">Poliklinik Terpadu</p>
                <p class="text-[11px] text-slate-400">Poli Umum, Penyakit Dalam, Gigi, Anak</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm text-center">
                <p class="text-3xl font-extrabold text-sky-600">24 Jam</p>
                <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 mt-1">IGD & Farmasi</p>
                <p class="text-[11px] text-slate-400">Layanan kegawatdaruratan dan resep obat</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm text-center">
                <p class="text-3xl font-extrabold text-indigo-600">100%</p>
                <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 mt-1">RME Digital</p>
                <p class="text-[11px] text-slate-400">Rekam medis terstandar SATUSEHAT</p>
            </div>
        </div>

        <!-- Visi & Misi -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-slate-200 dark:border-gray-700 shadow-sm space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xl">
                    👁️
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Visi Kami</h3>
                <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
                    Menjadi rumah sakit pilihan utama masyarakat yang unggul dalam mutu pelayanan klinis, berkeadilan sosial, dan berbasis teknologi kesehatan terdepan di tingkat nasional pada tahun 2030.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-slate-200 dark:border-gray-700 shadow-sm space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xl">
                    🎯
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Misi Kami</h3>
                <ul class="text-slate-600 dark:text-slate-300 text-sm space-y-2.5 list-disc list-inside">
                    <li>Menyelenggarakan pelayanan medis profesional dengan mengutamakan keselamatan pasien (*patient safety*).</li>
                    <li>Mengembangkan kompetensi tenaga medis dan keperawatan yang berakhlak mulia dan empatik.</li>
                    <li>Menerapkan transformasi digital kesehatan yang transparan, terintegrasi, dan mudah diakses masyarakat luas.</li>
                    <li>Menyediakan sarana dan prasarana rawat inap yang nyaman, higienis, dan terjangkau bagi semua lapisan masyarakat.</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
