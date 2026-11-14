<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat Keterangan Sakit — RS Aisyah Medika</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 dark:bg-gray-900 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col antialiased">

    <!-- Top Hospital Bar -->
    <header class="bg-white dark:bg-gray-800 border-b border-slate-200 dark:border-gray-700 shadow-sm py-4">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center font-bold text-xl shadow-md">
                    +
                </div>
                <div>
                    <h1 class="font-extrabold text-base text-slate-900 dark:text-white leading-tight">RS AISYAH MEDIKA</h1>
                    <p class="text-[11px] font-medium text-teal-600 dark:text-teal-400">Portal Verifikasi Keaslian Dokumen Medis</p>
                </div>
            </a>
            <a href="{{ url('/') }}" class="text-xs font-semibold text-slate-500 hover:text-teal-600 dark:hover:text-teal-400 transition">
                ← Ke Beranda
            </a>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-3xl w-full mx-auto px-4 sm:px-6 py-8 sm:py-12">
        @if($certificate)
            <!-- STATUS: VALID / TERVERIFIKASI -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-slate-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <!-- Verified Header Banner -->
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 sm:px-8 py-8 text-white text-center sm:text-left flex flex-col sm:flex-row items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center shrink-0 border border-white/30">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 text-xs font-bold tracking-wide uppercase mb-1 border border-white/20">
                            <span>✓</span> Dokumen Resmi Terverifikasi
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black">SURAT KETERANGAN SAKIT SAH</h2>
                        <p class="text-xs sm:text-sm text-teal-100 mt-0.5">
                            Data dokumen tercatat secara sah dan autentik dalam sistem Rekam Medis RS Aisyah Medika.
                        </p>
                    </div>
                </div>

                <!-- Detail Metadata Card -->
                <div class="p-6 sm:p-8 space-y-6">
                    <!-- Key Numbers Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-6 border-b border-slate-100 dark:border-gray-700">
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-gray-700/50 border border-slate-100 dark:border-gray-700">
                            <span class="text-[11px] font-bold uppercase text-slate-400 block mb-1">Nomor Surat Medis</span>
                            <span class="text-base font-black font-mono text-teal-700 dark:text-teal-300">
                                {{ $certificate->certificate_number }}
                            </span>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-gray-700/50 border border-slate-100 dark:border-gray-700">
                            <span class="text-[11px] font-bold uppercase text-slate-400 block mb-1">Waktu Penerbitan</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                {{ \Carbon\Carbon::parse($certificate->issued_at ?? $certificate->created_at)->translatedFormat('l, d F Y — H:i') }} WIB
                            </span>
                        </div>
                    </div>

                    <!-- Detail Pasien & Pemeriksaan -->
                    <div class="space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Rincian Pasien & Istirahat Medis</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-6 text-sm">
                            <div>
                                <span class="text-slate-400 text-xs block">Nama Pasien:</span>
                                <span class="font-bold text-slate-900 dark:text-white">{{ $certificate->patient?->name }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 text-xs block">Nomor Rekam Medis (No. RM):</span>
                                <span class="font-bold font-mono text-slate-900 dark:text-white">{{ $certificate->patient?->medical_record_number }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 text-xs block">Dokter Pemeriksa:</span>
                                <span class="font-bold text-slate-900 dark:text-white">{{ $certificate->doctor?->name }}</span>
                                <span class="text-xs text-slate-500 block">SIP: {{ $certificate->doctor?->sip_number ?? 'SIP-RSAM-'.date('Y') }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 text-xs block">Layanan Poliklinik:</span>
                                <span class="font-bold text-slate-900 dark:text-white">{{ $certificate->visit?->polyclinic?->name ?? 'Rawat Jalan' }}</span>
                            </div>
                        </div>

                        <!-- Highlight Masa Istirahat -->
                        <div class="p-5 rounded-2xl bg-teal-50 dark:bg-teal-950/40 border border-teal-200 dark:border-teal-800 mt-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 rounded-xl bg-teal-600 text-white flex items-center justify-center shrink-0 font-bold text-sm">
                                    🏥
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-bold text-teal-900 dark:text-teal-200 text-sm">
                                        Diberikan Izin Istirahat Medis Selama:
                                    </h4>
                                    <p class="text-base font-extrabold text-teal-800 dark:text-teal-300">
                                        {{ $certificate->days_count }} Hari Kalender
                                    </p>
                                    <p class="text-xs text-teal-700 dark:text-teal-400">
                                        Terhitung: <strong>{{ \Carbon\Carbon::parse($certificate->start_date)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($certificate->end_date)->translatedFormat('d F Y') }}</strong>
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($certificate->notes)
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-gray-700/40 border border-slate-100 dark:border-gray-700 text-xs text-slate-600 dark:text-slate-300">
                                <strong>Catatan / Keterangan Dokter:</strong> {{ $certificate->notes }}
                            </div>
                        @endif
                    </div>

                    <!-- Security & Legal Notice -->
                    <div class="pt-4 border-t border-slate-100 dark:border-gray-700 text-[11px] text-slate-400 leading-relaxed space-y-1">
                        <p>
                            🔒 <strong>Keaslian Dokumen:</strong> Halaman verifikasi publik ini membuktikan bahwa Surat Keterangan Sakit dengan nomor di atas resmi diterbitkan oleh dokter pemeriksa RS Aisyah Medika sesuai standar Rekam Medis Elektronik (RME) yang berlaku.
                        </p>
                        <p>
                            Token Otentikasi: <span class="font-mono text-slate-500">{{ $certificate->verification_token }}</span>
                        </p>
                    </div>
                </div>
            </div>
        @else
            <!-- STATUS: NOT FOUND / INVALID -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-rose-200 dark:border-rose-900/60 shadow-sm overflow-hidden text-center p-8 sm:p-12 space-y-4">
                <div class="w-20 h-20 rounded-3xl bg-rose-100 dark:bg-rose-950/60 text-rose-600 mx-auto flex items-center justify-center">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <div class="space-y-1">
                    <h2 class="text-xl sm:text-2xl font-black text-rose-600 dark:text-rose-400">
                        Dokumen Medis Tidak Ditemukan / Tidak Valid
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
                        Token verifikasi <span class="font-mono text-slate-700 dark:text-slate-300 font-bold">{{ $token }}</span> tidak terdaftar dalam basis data sistem RS Aisyah Medika. Pastikan QR Code atau tautan yang Anda buka berasal dari dokumen resmi.
                    </p>
                </div>

                <div class="pt-4 flex flex-col sm:flex-row justify-center gap-3">
                    <a href="{{ url('/') }}" class="px-5 py-2.5 rounded-xl font-bold text-xs bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 transition">
                        Kembali ke Halaman Utama
                    </a>
                    <a href="tel:02187654321" class="px-5 py-2.5 rounded-xl font-bold text-xs bg-teal-600 hover:bg-teal-700 text-white shadow-sm transition">
                        Hubungi Call Center RS
                    </a>
                </div>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="py-6 text-center text-xs text-slate-400 border-t border-slate-200 dark:border-gray-800">
        <p>© {{ date('Y') }} RS Aisyah Medika. Seluruh Hak Cipta Dilindungi Undang-Undang.</p>
    </footer>

</body>
</html>
