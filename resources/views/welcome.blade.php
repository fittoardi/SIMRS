<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>RS Aisyah Medika — Sistem Informasi Rumah Sakit Terpadu</title>
    <meta name="description" content="Portal resmi RS Aisyah Medika. Booking antrean poliklinik online, cek jadwal dokter, ketersediaan kamar rawat inap real-time.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Schema.org JSON-LD MedicalOrganization -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'MedicalOrganization',
        'name' => 'RS Aisyah Medika',
        'url' => url('/'),
        'telephone' => '(021) 8765-4321',
        'emergencyTelephone' => '(021) 8765-4322',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Jl. Kesehatan No. 45',
            'addressLocality' => 'Jakarta Selatan',
            'addressRegion' => 'DKI Jakarta',
            'postalCode' => '12810',
            'addressCountry' => 'ID',
        ],
        'medicalSpecialty' => [
            'Internal Medicine',
            'General Practice',
            'Dentistry',
            'Pediatrics',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
</head>
<body class="font-sans antialiased text-slate-800 dark:text-slate-100 min-h-screen flex flex-col pb-20 md:pb-0 relative bg-slate-950">

    <!-- Full-Screen Hospital Background Layer with Transparent Overlay -->
    <div class="fixed inset-0 -z-20 overflow-hidden pointer-events-none">
        <img
            src="{{ asset('images/rs-aisyah-medika-bg.jpg') }}"
            alt="Gedung RS Aisyah Medika"
            class="w-full h-full object-cover object-center scale-105"
        />
        <!-- Semi-transparent atmospheric gradient overlay so foreground elements pop while hospital stays visible -->
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950/75 via-slate-900/55 to-teal-950/65 backdrop-blur-[1px]"></div>
    </div>

    <!-- Top Navigation Bar -->
    @include('layouts.navigation')

    <!-- Emergency Hotline Banner (IGD 24 Jam) -->
    <div class="bg-rose-600/95 backdrop-blur-md text-white text-xs sm:text-sm font-semibold py-2 px-4 shadow-sm relative z-20">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-1 text-center sm:text-left">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-white animate-pulse"></span>
                <span>Instalasi Gawat Darurat (IGD) & Ambulans Siaga 24 Jam</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="tel:02187654322" class="underline hover:text-rose-100 font-bold tracking-wide">
                    Telepon: (021) 8765-4322 / 119
                </a>
            </div>
        </div>
    </div>

    <!-- Full-Screen Hero Section (Foreground Objects Floating Over Hospital Backdrop) -->
    <section class="relative min-h-[calc(100vh-6.25rem)] flex items-center py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">

                <!-- Left Foreground Content -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/15 backdrop-blur-md border border-white/25 text-teal-200 text-xs font-semibold shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Pelayanan Prima, Ramah & Berbasis Digital</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-[1.12] drop-shadow-sm">
                        Kesehatan Anda Prioritas <span class="text-teal-300">RS Aisyah Medika</span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-100/90 max-w-2xl mx-auto lg:mx-0 leading-relaxed drop-shadow-sm">
                        Sistem informasi rumah sakit terpadu memudahkan pendaftaran mandiri, booking antrean poliklinik online, dan pemantauan antrean real-time langsung dari smartphone Anda.
                    </p>

                    <!-- Quick Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 pt-2">
                        @auth
                            <a href="{{ url('/akun/booking') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 rounded-xl text-base font-semibold text-white bg-teal-600 hover:bg-teal-500 shadow-lg shadow-teal-900/40 transition transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                Booking Antrean Online
                            </a>
                        @else
                            <a href="{{ route('public.booking') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 rounded-xl text-base font-semibold text-white bg-teal-600 hover:bg-teal-500 shadow-lg shadow-teal-900/40 transition transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                Booking Online (Tanpa Akun)
                            </a>

                            <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-3.5 rounded-xl text-base font-semibold text-white bg-white/15 hover:bg-white/25 backdrop-blur-md border border-white/30 shadow-lg transition">
                                Daftar Akun Pasien
                            </a>
                        @endauth

                        <a href="{{ url('/jadwal-dokter') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-3.5 rounded-xl text-base font-semibold text-white bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/25 shadow-lg transition">
                            Jadwal Dokter
                        </a>
                    </div>

                    <!-- Quick stats / translucent glass pills -->
                    <div class="grid grid-cols-3 gap-3 sm:gap-4 pt-4 max-w-xl mx-auto lg:mx-0">
                        <div class="rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 p-3.5 text-center lg:text-left">
                            <p class="text-xl sm:text-2xl font-extrabold text-teal-300">4+</p>
                            <p class="text-xs text-slate-200 mt-0.5">Poliklinik Spesialis</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 p-3.5 text-center lg:text-left">
                            <p class="text-xl sm:text-2xl font-extrabold text-emerald-300">10</p>
                            <p class="text-xs text-slate-200 mt-0.5">Bed Rawat Inap</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 p-3.5 text-center lg:text-left">
                            <p class="text-xl sm:text-2xl font-extrabold text-amber-300">24 Jam</p>
                            <p class="text-xs text-slate-200 mt-0.5">IGD & Farmasi</p>
                        </div>
                    </div>
                </div>

                <!-- Right Foreground Object: Frosted Glass Layanan Mandiri Card -->
                <div class="lg:col-span-5">
                    <div class="bg-white/85 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl p-6 sm:p-7 shadow-2xl border border-white/50 dark:border-slate-700/60 space-y-3.5">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200/70 dark:border-slate-700/70">
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Layanan Mandiri Pasien</h3>
                            <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-100/90 dark:bg-emerald-900/50 px-2.5 py-1 rounded-full">Digital & Cepat</span>
                        </div>

                        <!-- Card 1: Booking Antrean -->
                        <a href="{{ auth()->check() ? url('/akun/booking') : route('public.booking') }}" class="block p-3.5 rounded-2xl bg-white/75 dark:bg-slate-800/70 hover:bg-teal-50/90 dark:hover:bg-slate-800 transition border border-slate-200/60 dark:border-slate-700 group shadow-sm">
                            <div class="flex items-center gap-3.5">
                                <div class="w-11 h-11 rounded-xl bg-teal-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-teal-600/20 group-hover:scale-105 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-teal-700 dark:group-hover:text-teal-400">Pendaftaran & Booking Online</p>
                                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">Ambil nomor antrean dari rumah tanpa antre di loket</p>
                                </div>
                            </div>
                        </a>

                        <!-- Card 2: Ketersediaan Bed Kamar -->
                        <a href="{{ url('/ketersediaan-kamar') }}" class="block p-3.5 rounded-2xl bg-white/75 dark:bg-slate-800/70 hover:bg-emerald-50/90 dark:hover:bg-slate-800 transition border border-slate-200/60 dark:border-slate-700 group shadow-sm">
                            <div class="flex items-center gap-3.5">
                                <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-600/20 group-hover:scale-105 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-emerald-700 dark:group-hover:text-emerald-400">Info Bed Rawat Inap Real-time</p>
                                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">Cek sisa kamar VVIP, VIP, Kelas 1, dan ICU</p>
                                </div>
                            </div>
                        </a>

                        <!-- Card 3: Layar Antrean TV -->
                        <a href="{{ url('/layar-antrean') }}" class="block p-3.5 rounded-2xl bg-white/75 dark:bg-slate-800/70 hover:bg-sky-50/90 dark:hover:bg-slate-800 transition border border-slate-200/60 dark:border-slate-700 group shadow-sm">
                            <div class="flex items-center gap-3.5">
                                <div class="w-11 h-11 rounded-xl bg-sky-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-sky-600/20 group-hover:scale-105 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-sky-700 dark:group-hover:text-sky-400">Layar Antrean Ruang Tunggu</p>
                                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">Tampilan monitor panggilan poli & farmasi</p>
                                </div>
                            </div>
                        </a>

                        <!-- Card 4: Kiosk Check-In Mandiri -->
                        <a href="{{ url('/check-in') }}" class="block p-3.5 rounded-2xl bg-white/75 dark:bg-slate-800/70 hover:bg-indigo-50/90 dark:hover:bg-slate-800 transition border border-slate-200/60 dark:border-slate-700 group shadow-sm">
                            <div class="flex items-center gap-3.5">
                                <div class="w-11 h-11 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-indigo-600/20 group-hover:scale-105 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-700 dark:group-hover:text-indigo-400">Kiosk Check-In Kedatangan</p>
                                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">Konfirmasi kedatangan cepat via Kode Booking / NIK</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Poliklinik Section (Translucent Frosted Surface Above Background) -->
    <section class="py-14 bg-white/90 dark:bg-gray-900/90 backdrop-blur-lg border-t border-white/30 dark:border-gray-800 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs uppercase font-bold tracking-wider text-teal-600 dark:text-teal-400">Unit Pelayanan</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mt-1">Poliklinik Unggulan</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-2">Dukungan dokter spesialis berpengalaman dan fasilitas penunjang medis modern.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Poli Umum -->
                <div class="rounded-2xl p-6 bg-slate-50/90 dark:bg-gray-800/80 border border-slate-200/80 dark:border-gray-700 hover:shadow-lg transition">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-sm mb-4">
                        PU
                    </div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white">Poliklinik Umum</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">Pemeriksaan kesehatan dasar, pengobatan penyakit umum, dan rujukan.</p>
                </div>

                <!-- Poli Penyakit Dalam -->
                <div class="rounded-2xl p-6 bg-slate-50/90 dark:bg-gray-800/80 border border-slate-200/80 dark:border-gray-700 hover:shadow-lg transition">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm mb-4">
                        PD
                    </div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white">Poli Penyakit Dalam</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">Konsultasi dokter Sp.PD untuk hipertensi, diabetes, ginjal, dan organ dalam.</p>
                </div>

                <!-- Poli Gigi -->
                <div class="rounded-2xl p-6 bg-slate-50/90 dark:bg-gray-800/80 border border-slate-200/80 dark:border-gray-700 hover:shadow-lg transition">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm mb-4">
                        PG
                    </div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white">Poli Gigi & Mulut</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">Perawatan konservasi gigi, scaling karang gigi, penambalan, dan pencabutan.</p>
                </div>

                <!-- Poli Anak -->
                <div class="rounded-2xl p-6 bg-slate-50/90 dark:bg-gray-800/80 border border-slate-200/80 dark:border-gray-700 hover:shadow-lg transition">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm mb-4">
                        PA
                    </div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white">Poliklinik Anak</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">Pemeriksaan tumbuh kembang, nutrisi balita, dan imunisasi berkala.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <x-footer />

    <!-- Floating Emergency & WhatsApp Button -->
    <x-floating-emergency />

    <!-- Mobile Bottom Navigation (< 768px) -->
    <x-bottom-navigation />

</body>
</html>
