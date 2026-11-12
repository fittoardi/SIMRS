<x-guest-layout maxWidth="max-w-4xl" :card="false">
    <x-slot name="title">Masuk Portal Pasien — RS Aisyah Medika</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-12 rounded-3xl overflow-hidden shadow-2xl border border-white/40 dark:border-slate-700/60 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl">

        <!-- Left Showcase Panel (Desktop) -->
        <div class="hidden lg:flex lg:col-span-5 flex-col justify-between p-8 bg-gradient-to-br from-teal-700 via-teal-800 to-slate-900 text-white relative overflow-hidden">
            <!-- Decorative ambient glow -->
            <div class="absolute -top-20 -right-20 w-56 h-56 rounded-full bg-emerald-400/20 blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -left-16 w-56 h-56 rounded-full bg-teal-400/15 blur-2xl pointer-events-none"></div>

            <div class="relative z-10 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 border border-white/20 text-teal-100 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Portal Pasien Digital</span>
                </div>

                <div>
                    <h2 class="text-2xl font-extrabold leading-snug tracking-tight">
                        Akses Layanan Kesehatan Lebih Mudah & Cepat
                    </h2>
                    <p class="text-sm text-teal-100/85 mt-2 leading-relaxed">
                        Satu akun untuk booking jadwal dokter, memantau antrean ruang tunggu, hingga mengelola riwayat keluarga.
                    </p>
                </div>

                <ul class="space-y-4 pt-2">
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold">Booking Antrean Tanpa Antre</p>
                            <p class="text-xs text-teal-100/75 mt-0.5">Dapatkan nomor antrean & tiket QR langsung dari rumah.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold">Pemantauan Real-Time</p>
                            <p class="text-xs text-teal-100/75 mt-0.5">Ketahui estimasi waktu panggilan dokter secara langsung.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold">Manajemen Profil Keluarga</p>
                            <p class="text-xs text-teal-100/75 mt-0.5">Daftarkan anak atau orang tua dalam satu akun terpadu.</p>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="relative z-10 mt-8 pt-5 border-t border-white/15">
                <p class="text-xs text-teal-100/80">Layanan Darurat / IGD 24 Jam</p>
                <p class="text-sm font-bold text-white mt-0.5">(021) 8765-4322 / 119</p>
            </div>
        </div>

        <!-- Right Login Form Panel -->
        <div class="lg:col-span-7 p-6 sm:p-10 flex flex-col justify-center">
            <div class="mb-7">
                <span class="inline-block lg:hidden px-2.5 py-1 rounded-full bg-teal-50 dark:bg-teal-900/40 text-teal-700 dark:text-teal-300 text-[11px] font-semibold mb-2">
                    Portal Pasien RS Aisyah Medika
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Selamat Datang Kembali
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1.5">
                    Silakan masuk menggunakan email dan kata sandi akun Anda.
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="nama@email.com"
                            class="block w-full pl-11 pr-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm placeholder-slate-400 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password with Alpine Show/Hide Toggle -->
                <div x-data="{ showPassword: false }">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                            Kata Sandi
                        </label>
                        @if (Route::has('password.request'))
                            <a class="text-xs font-semibold text-teal-600 hover:text-teal-700 dark:text-teal-400 hover:underline" href="{{ route('password.request') }}">
                                Lupa kata sandi?
                            </a>
                        @endif
                    </div>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Masukkan kata sandi Anda"
                            class="block w-full pl-11 pr-11 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm placeholder-slate-400 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition"
                            aria-label="Tampilkan atau sembunyikan kata sandi"
                        >
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" class="rounded border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-teal-600 shadow-sm focus:ring-teal-500" name="remember">
                        <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">Ingat sesi login saya</span>
                    </label>
                </div>

                <!-- Primary Submit Button -->
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 shadow-lg shadow-teal-600/25 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition transform hover:-translate-y-0.5">
                    <span>Masuk ke Portal Pasien</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            <!-- Register Prompt -->
            <div class="mt-7 pt-6 border-t border-slate-200 dark:border-slate-800 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                    Pasien baru di RS Aisyah Medika?
                </p>
                <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-5 rounded-xl font-semibold text-sm text-teal-700 dark:text-teal-300 bg-teal-50 hover:bg-teal-100 dark:bg-teal-900/30 dark:hover:bg-teal-900/50 border border-teal-200 dark:border-teal-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span>Daftar Pasien Baru Sekarang</span>
                </a>
                <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                    Ingin daftar cepat tanpa akun?
                    <a href="{{ route('public.booking') }}" class="font-bold text-teal-600 hover:underline">Booking Sebagai Tamu →</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
