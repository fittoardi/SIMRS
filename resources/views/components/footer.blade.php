<footer class="mt-auto bg-slate-900 text-slate-400 text-xs py-12 border-t border-slate-800 relative z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
            <!-- Kolom 1: Profil RS -->
            <div class="md:col-span-1 space-y-3">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-teal-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-extrabold text-base text-white block leading-tight">RS Aisyah Medika</span>
                        <span class="text-[10px] uppercase font-bold text-teal-400 tracking-wider">Akreditasi Paripurna KARS</span>
                    </div>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Memberikan pelayanan kesehatan terpadu, berbasis empati dan teknologi modern, mengutamakan keselamatan serta kesembuhan pasien.
                </p>
                <div class="pt-2 text-[11px] text-slate-500 space-y-1">
                    <p>📍 Jl. Kesehatan No. 45, Jakarta Selatan 12810</p>
                    <p>📞 Call Center: (021) 8765-4321</p>
                    <p>🚨 Hotline IGD: (021) 8765-4322 / 119</p>
                </div>
            </div>

            <!-- Kolom 2: Layanan & Fasilitas -->
            <div class="space-y-2.5">
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-200">Layanan RS</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="{{ url('/jadwal-dokter') }}" class="hover:text-teal-400 transition">Jadwal Praktik Dokter</a></li>
                    <li><a href="{{ url('/ketersediaan-kamar') }}" class="hover:text-teal-400 transition">Ketersediaan Bed Rawat Inap</a></li>
                    <li><a href="{{ url('/booking-online') }}" class="hover:text-teal-400 transition">Booking Antrean Online (Guest)</a></li>
                    <li><a href="{{ url('/check-in') }}" class="hover:text-teal-400 transition">Kiosk Check-In Mandiri</a></li>
                    <li><a href="{{ url('/layar-antrean') }}" class="hover:text-teal-400 transition">Layar Monitor Antrean Poli & Farmasi</a></li>
                </ul>
            </div>

            <!-- Kolom 3: Informasi & Edukasi Publik -->
            <div class="space-y-2.5">
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-200">Informasi Pasien</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="{{ route('public.about') }}" class="hover:text-teal-400 transition">Tentang Kami & Profil RS</a></li>
                    <li><a href="{{ route('public.patient-flow') }}" class="hover:text-teal-400 transition">Alur & Panduan Pendaftaran</a></li>
                    <li><a href="{{ route('public.patient-rights') }}" class="hover:text-teal-400 transition">Hak & Kewajiban Pasien</a></li>
                    <li><a href="{{ route('public.faq') }}" class="hover:text-teal-400 transition">Tanya Jawab (FAQ) BPJS & Umum</a></li>
                    <li><a href="{{ route('public.privacy-policy') }}" class="hover:text-teal-400 transition">Kebijakan Privasi Data Medis (UU PDP)</a></li>
                    <li><a href="{{ route('public.contact') }}" class="hover:text-teal-400 transition">Kontak, Peta & Pengaduan</a></li>
                </ul>
            </div>

            <!-- Kolom 4: Area Pasien & Akun -->
            <div class="space-y-2.5">
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-200">Portal Pasien</h4>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Masuk ke area pasien untuk melihat riwayat berobat, kartu digital No. RM, booking antrean keluarga, dan bayar tagihan online.
                </p>
                <div class="pt-2 flex flex-col gap-2">
                    @auth
                        <a href="{{ route('patient.dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-semibold bg-teal-600 text-white hover:bg-teal-500 transition shadow-sm">
                            Buka Area Pasien Saya
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-semibold bg-teal-600 text-white hover:bg-teal-500 transition shadow-sm">
                            Masuk Akun Pasien
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-200 hover:bg-slate-700 transition border border-slate-700">
                            Daftar Pasien Baru
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-slate-500">
            <p>© {{ date('Y') }} RS Aisyah Medika. Seluruh hak cipta dilindungi undang-undang.</p>
            <div class="flex items-center gap-4">
                <span>SIMRS Terintegrasi Full Laravel Monolith</span>
                <span>•</span>
                <a href="{{ route('public.privacy-policy') }}" class="hover:text-slate-400">Privasi UU PDP</a>
                <span>•</span>
                <a href="{{ url('/sitemap.xml') }}" target="_blank" class="hover:text-slate-400">Sitemap</a>
            </div>
        </div>
    </div>
</footer>
