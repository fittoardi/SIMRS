<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-bold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                    Profil Dokter — {{ $doctor->name }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ $doctor->specialization }} • {{ $doctor->polyclinic?->name }}</p>
            </div>
            <a href="{{ url('/jadwal-dokter') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                ← Kembali ke Jadwal Dokter
            </a>
        </div>
    </x-slot>

    <!-- Schema.org JSON-LD for Physician -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Physician',
        'name' => $doctor->name,
        'medicalSpecialty' => $doctor->specialization,
        'hospitalAffiliation' => [
            '@type' => 'Hospital',
            'name' => 'RS Aisyah Medika',
            'telephone' => '(021) 8765-4321',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Jl. Kesehatan No. 45',
                'addressLocality' => 'Jakarta Selatan',
                'addressRegion' => 'DKI Jakarta',
                'addressCountry' => 'ID',
            ],
        ],
        'priceRange' => 'Rp ' . number_format($doctor->consultation_fee, 0, ',', '.'),
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Doctor Profile Card (Left) -->
            <div class="lg:col-span-7 bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-gray-700 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left space-y-4 sm:space-y-0 sm:space-x-5">
                    <div class="w-24 h-24 rounded-2xl bg-gradient-to-tr from-teal-600 to-emerald-500 text-white flex items-center justify-center font-bold text-2xl shadow-lg shadow-teal-600/20 shrink-0">
                        {{ strtoupper(substr(str_replace('dr. ', '', $doctor->name), 0, 2)) }}
                    </div>
                    <div class="space-y-1.5">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300">
                            {{ $doctor->polyclinic?->name }}
                        </span>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $doctor->name }}</h3>
                        <p class="text-sm text-teal-600 dark:text-teal-400 font-medium">{{ $doctor->specialization }}</p>
                        @if($doctor->sip_number)
                            <p class="text-xs text-slate-400">Nomor SIP: {{ $doctor->sip_number }}</p>
                        @endif
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-gray-700">
                    <h4 class="text-xs uppercase font-bold tracking-wider text-slate-500 mb-2">Tentang & Pengalaman Klinis</h4>
                    <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                        {{ $doctor->bio ?? 'Dokter spesialis berdedikasi tinggi di RS Aisyah Medika, berkomitmen memberikan pelayanan klinis yang menyeluruh, aman, dan berorientasi pada kesembuhan pasien.' }}
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-gray-700/40 border border-slate-200 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-500">Tarif Jasa Konsultasi</span>
                        <p class="text-xl font-bold text-teal-700 dark:text-teal-400">
                            Rp {{ number_format($doctor->consultation_fee, 0, ',', '.') }}
                        </p>
                    </div>
                    <a href="{{ url('/akun/booking?doctor=' . $doctor->id) }}" class="px-5 py-2.5 rounded-xl font-semibold text-xs text-white bg-teal-600 hover:bg-teal-700 shadow-sm transition">
                        Ambil Antrean
                    </a>
                </div>
            </div>

            <!-- Schedules (Right) -->
            <div class="lg:col-span-5 bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-gray-700 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-gray-700">
                    <h4 class="font-bold text-base text-slate-900 dark:text-white">Jadwal Sesi Praktik</h4>
                    <span class="text-xs text-teal-600 font-semibold">{{ $doctor->schedules->count() }} Sesi</span>
                </div>

                <div class="space-y-3">
                    @forelse($doctor->schedules as $schedule)
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-gray-700/50 border border-slate-200/80 dark:border-gray-700 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-sm text-teal-700 dark:text-teal-400 block">{{ $schedule->day_name }}</span>
                                <span class="text-xs font-mono text-slate-700 dark:text-slate-300">{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</span>
                                <span class="text-[11px] text-slate-400 block mt-0.5">{{ $schedule->room_name ?? 'Ruang Poliklinik' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400">Maksimal Kuota</span>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $schedule->max_quota }} Pasien</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic text-center py-4">Belum ada jadwal praktik aktif.</p>
                    @endforelse
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-gray-700">
                    <a href="{{ url('/akun/booking?doctor=' . $doctor->id) }}" class="w-full inline-flex items-center justify-center px-4 py-3 rounded-xl text-sm font-semibold bg-teal-600 hover:bg-teal-700 text-white shadow-md shadow-teal-600/20 transition">
                        Daftar Antrean Dokter Ini
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
