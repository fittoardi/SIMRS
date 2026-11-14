<x-guest-layout maxWidth="max-w-5xl" :card="false">
    <x-slot name="title">Pendaftaran Pasien Baru — RS Aisyah Medika</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-12 rounded-3xl overflow-hidden shadow-2xl border border-white/40 dark:border-slate-700/60 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl">

        <!-- Left Showcase Sidebar (Desktop) -->
        <div class="hidden lg:flex lg:col-span-4 flex-col justify-between p-8 bg-gradient-to-br from-teal-700 via-teal-800 to-slate-900 text-white relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-56 h-56 rounded-full bg-emerald-400/20 blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -left-16 w-56 h-56 rounded-full bg-teal-400/15 blur-2xl pointer-events-none"></div>

            <div class="relative z-10 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 border border-white/20 text-teal-100 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Pendaftaran Online 24 Jam</span>
                </div>

                <div>
                    <h2 class="text-2xl font-extrabold leading-snug tracking-tight">
                        Daftar Sekali, Nikmati Layanan Tanpa Antre
                    </h2>
                    <p class="text-sm text-teal-100/85 mt-2 leading-relaxed">
                        Registrasi pasien baru di RS Aisyah Medika kini lebih cepat, praktis, dan terintegrasi langsung dengan rekam medis.
                    </p>
                </div>

                <!-- Stepper Highlights -->
                <div class="space-y-4 pt-2">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-full bg-emerald-500/30 border border-emerald-300/40 text-emerald-200 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            1
                        </div>
                        <div>
                            <p class="text-sm font-bold">Terbit No. Rekam Medis Instan</p>
                            <p class="text-xs text-teal-100/75 mt-0.5">Nomor RM otomatis dibuat begitu pendaftaran selesai.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-full bg-emerald-500/30 border border-emerald-300/40 text-emerald-200 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            2
                        </div>
                        <div>
                            <p class="text-sm font-bold">Kelola Profil Keluarga</p>
                            <p class="text-xs text-teal-100/75 mt-0.5">Daftarkan anak, pasangan, atau orang tua dalam satu akun.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-full bg-emerald-500/30 border border-emerald-300/40 text-emerald-200 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            3
                        </div>
                        <div>
                            <p class="text-sm font-bold">Booking & Check-In Mandiri QR</p>
                            <p class="text-xs text-teal-100/75 mt-0.5">Langsung konfirmasi kedatangan di Kiosk rumah sakit.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative z-10 mt-8 pt-5 border-t border-white/15 space-y-3">
                <p class="text-xs text-teal-100/80">Sudah pernah berobat / memiliki akun portal?</p>
                <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-white/15 hover:bg-white/25 border border-white/25 text-xs font-bold text-white transition">
                    <span>Masuk ke Akun Anda</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Right Structured Form Panel -->
        <div class="lg:col-span-8 p-6 sm:p-8 lg:p-9">
            <div class="flex flex-wrap items-center justify-between gap-2 pb-5 mb-6 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Pendaftaran Pasien Baru
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Isi data sesuai identitas (KTP/KK) untuk penerbitan Nomor RM otomatis.
                    </p>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-bold">
                    Gratis & Instan
                </span>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- Section 1: Identitas Pasien -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-md bg-teal-600 text-white text-[11px] font-bold flex items-center justify-center">1</span>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-teal-700 dark:text-teal-400">Identitas Pasien</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Nama Lengkap (Sesuai KTP) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name', $pending['name'] ?? '') }}"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Contoh: Budi Santoso"
                                class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition"
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                        </div>

                        <div>
                            <label for="nik" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                NIK (16 Digit KTP)
                            </label>
                            <input
                                id="nik"
                                type="text"
                                name="nik"
                                value="{{ old('nik', $pending['nik'] ?? '') }}"
                                maxlength="16"
                                placeholder="3171xxxxxxxxxxxx"
                                class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-mono focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition"
                            />
                            <x-input-error :messages="$errors->get('nik')" class="mt-1.5" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="gender" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Jenis Kelamin
                            </label>
                            <select
                                id="gender"
                                name="gender"
                                class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition"
                            >
                                <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            <x-input-error :messages="$errors->get('gender')" class="mt-1.5" />
                        </div>

                        <div>
                            <label for="birth_date" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Tanggal Lahir
                            </label>
                            <input
                                id="birth_date"
                                type="date"
                                name="birth_date"
                                value="{{ old('birth_date') }}"
                                class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition"
                            />
                            <x-input-error :messages="$errors->get('birth_date')" class="mt-1.5" />
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                No. HP / WhatsApp <span class="text-rose-500">*</span>
                                <span class="text-[10px] text-teal-600 dark:text-teal-400 font-normal">(Verifikasi OTP)</span>
                            </label>
                            <input
                                id="phone"
                                type="tel"
                                name="phone"
                                value="{{ old('phone', $pending['phone'] ?? '') }}"
                                required
                                placeholder="0812xxxxxxxx"
                                class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition"
                            />
                            <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
                        </div>
                    </div>
                </div>

                <!-- Section 2: Informasi Medis & Jaminan -->
                <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-md bg-emerald-600 text-white text-[11px] font-bold flex items-center justify-center">2</span>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Data Medis & Jaminan (Opsional)</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="blood_type" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Golongan Darah
                            </label>
                            <select
                                id="blood_type"
                                name="blood_type"
                                class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition"
                            >
                                <option value="">- Belum Tahu -</option>
                                <option value="A" {{ old('blood_type') === 'A' ? 'selected' : '' }}>A</option>
                                <option value="B" {{ old('blood_type') === 'B' ? 'selected' : '' }}>B</option>
                                <option value="AB" {{ old('blood_type') === 'AB' ? 'selected' : '' }}>AB</option>
                                <option value="O" {{ old('blood_type') === 'O' ? 'selected' : '' }}>O</option>
                            </select>
                        </div>

                        <div>
                            <label for="bpjs_number" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                No. Kartu BPJS Kesehatan
                            </label>
                            <input
                                id="bpjs_number"
                                type="text"
                                name="bpjs_number"
                                value="{{ old('bpjs_number') }}"
                                placeholder="0001xxxxxxxxx"
                                class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-mono focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition"
                            />
                        </div>

                        <div>
                            <label for="allergies" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Riwayat Alergi Obat/Makanan
                            </label>
                            <input
                                id="allergies"
                                type="text"
                                name="allergies"
                                value="{{ old('allergies') }}"
                                placeholder="Mis: Penisilin, Udang"
                                class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition"
                            />
                        </div>
                    </div>
                </div>

                <!-- Section 3: Akun & Keamanan -->
                <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-md bg-sky-600 text-white text-[11px] font-bold flex items-center justify-center">3</span>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-sky-700 dark:text-sky-400">Akun & Keamanan Login</h2>
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Alamat Email (Untuk Login) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', $pending['email'] ?? '') }}"
                            required
                            autocomplete="username"
                            placeholder="nama@email.com"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div x-data="{ showPass: false }">
                            <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Kata Sandi <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    id="password"
                                    :type="showPass ? 'text' : 'password'"
                                    name="password"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Minimal 8 karakter"
                                    class="block w-full px-3.5 pr-10 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition"
                                />
                                <button
                                    type="button"
                                    @click="showPass = !showPass"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                                    aria-label="Tampilkan atau sembunyikan kata sandi"
                                >
                                    <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                        </div>

                        <div x-data="{ showConfirm: false }">
                            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Ulangi Kata Sandi <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    id="password_confirmation"
                                    :type="showConfirm ? 'text' : 'password'"
                                    name="password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Ketik ulang kata sandi"
                                    class="block w-full px-3.5 pr-10 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition"
                                />
                                <button
                                    type="button"
                                    @click="showConfirm = !showConfirm"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                                    aria-label="Tampilkan atau sembunyikan konfirmasi kata sandi"
                                >
                                    <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showConfirm" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="pt-3 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <a class="lg:hidden text-xs font-semibold text-teal-600 dark:text-teal-400 hover:underline" href="{{ route('login') }}">
                        Sudah punya akun? Masuk di sini
                    </a>

                    <p class="hidden lg:block text-[11px] text-slate-400 max-w-xs">
                        Dengan mendaftar, Anda menyetujui kebijakan privasi rekam medis RS Aisyah Medika.
                    </p>

                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 py-3.5 px-7 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 shadow-lg shadow-teal-600/25 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition transform hover:-translate-y-0.5">
                        <span>Daftar & Terbitkan No. RM</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
