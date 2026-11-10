<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                    Area Pasien — RS Aisyah Medika
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Selamat datang kembali, <span class="font-semibold text-teal-700 dark:text-teal-400">{{ $user->name }}</span>
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ url('/akun/booking') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium text-xs rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Booking Antrean
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Banner Kartu Pasien Digital & Alergi -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kartu Pasien Digital -->
            <div class="lg:col-span-2 rounded-2xl bg-gradient-to-br from-teal-700 via-teal-800 to-emerald-900 text-white p-6 sm:p-8 shadow-xl relative overflow-hidden">
                <!-- Background pattern -->
                <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
                    <svg class="w-72 h-72" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4"/>
                    </svg>
                </div>

                <div class="flex justify-between items-start mb-6">
                    <div>
                        <span class="text-xs uppercase tracking-widest font-semibold text-teal-200">Kartu Berobat Digital</span>
                        <h3 class="text-xl sm:text-2xl font-bold mt-1 tracking-tight">RS Aisyah Medika</h3>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-200 border border-emerald-400/30">
                        Aktif
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-4">
                    <div>
                        <p class="text-xs text-teal-200">Nama Pasien</p>
                        <p class="text-lg font-semibold">{{ $patient?->name ?? $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-teal-200">Nomor Rekam Medis (No. RM)</p>
                        <p class="text-xl font-mono font-bold tracking-wider text-teal-100">
                            {{ $patient?->medical_record_number ?? 'Belum terdaftar di loket' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-teal-200">NIK (KTP)</p>
                        <p class="font-mono text-sm">{{ $patient?->nik ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-teal-200">Golongan Darah</p>
                        <p class="text-sm font-semibold">{{ $patient?->blood_type ?? '-' }}</p>
                    </div>
                </div>

                @if($patient?->allergies)
                    <!-- High-contrast alert banner for allergies -->
                    <div class="mt-4 p-3 rounded-xl bg-rose-500/20 border border-rose-400/40 text-rose-100 text-xs flex items-center space-x-2">
                        <svg class="w-5 h-5 text-rose-300 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <span class="font-bold text-rose-200 uppercase tracking-wider">Perhatian Alergi:</span>
                            <span class="ml-1">{{ $patient->allergies }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Akses Cepat Menu Pasien -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-slate-200 dark:border-gray-700 shadow-sm flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-base text-slate-800 dark:text-slate-100 mb-1">Akses Layanan Cepat</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Navigasi mudah untuk rawat jalan & informasi</p>

                    <div class="space-y-2.5">
                        <a href="{{ url('/jadwal-dokter') }}" class="flex items-center p-3 rounded-xl hover:bg-teal-50 dark:hover:bg-gray-700/60 transition group border border-slate-100 dark:border-gray-700">
                            <div class="w-9 h-9 rounded-lg bg-teal-100 dark:bg-teal-900/50 text-teal-700 dark:text-teal-300 flex items-center justify-center mr-3 group-hover:scale-105 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 group-hover:text-teal-700">Cek Jadwal Dokter</p>
                                <p class="text-[11px] text-slate-500">Lihat spesialis & sisa kuota</p>
                            </div>
                        </a>

                        <a href="{{ url('/ketersediaan-kamar') }}" class="flex items-center p-3 rounded-xl hover:bg-teal-50 dark:hover:bg-gray-700/60 transition group border border-slate-100 dark:border-gray-700">
                            <div class="w-9 h-9 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 flex items-center justify-center mr-3 group-hover:scale-105 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 group-hover:text-teal-700">Ketersediaan Bed Rawat Inap</p>
                                <p class="text-[11px] text-slate-500">VVIP, VIP, Kelas 1-3 & ICU</p>
                            </div>
                        </a>

                        <a href="{{ url('/layar-antrean') }}" class="flex items-center p-3 rounded-xl hover:bg-teal-50 dark:hover:bg-gray-700/60 transition group border border-slate-100 dark:border-gray-700">
                            <div class="w-9 h-9 rounded-lg bg-sky-100 dark:bg-sky-900/50 text-sky-700 dark:text-sky-300 flex items-center justify-center mr-3 group-hover:scale-105 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 group-hover:text-teal-700">Layar Antrean Live</p>
                                <p class="text-[11px] text-slate-500">Pantau panggilan antrean poli</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-gray-700 mt-4 text-center">
                    <p class="text-[11px] text-slate-500">Bantuan loket pendaftaran: <span class="font-semibold text-teal-600">(021) 8765-4321</span></p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

