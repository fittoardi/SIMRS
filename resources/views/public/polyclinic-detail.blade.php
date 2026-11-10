<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-bold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                    {{ $polyclinic->name }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Kode: {{ $polyclinic->code }} • Lokasi: {{ $polyclinic->location ?? 'Gedung RS Aisyah Medika' }}</p>
            </div>
            <a href="{{ url('/jadwal-dokter') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                ← Kembali ke Jadwal Dokter
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Polyclinic Info Card -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-gray-700 shadow-sm space-y-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-700 dark:bg-teal-900/50 dark:text-teal-300 flex items-center justify-center font-bold text-lg">
                    {{ strtoupper(substr($polyclinic->code, -2)) }}
                </div>
                <div>
                    <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $polyclinic->name }}</h3>
                    <p class="text-xs text-teal-600 font-semibold">{{ $polyclinic->location }}</p>
                </div>
            </div>

            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                {{ $polyclinic->description ?? 'Poliklinik unggulan RS Aisyah Medika dengan pelayanan ramah dan tenaga medis profesional.' }}
            </p>
        </div>

        <!-- Doctors in this Polyclinic -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Dokter Spesialis & Praktik di Poliklinik Ini</h3>
                <span class="text-xs text-slate-500">{{ $polyclinic->doctors->count() }} Dokter</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($polyclinic->doctors as $doctor)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-slate-200 dark:border-gray-700 shadow-sm flex flex-col justify-between space-y-4">
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-base">
                                    {{ strtoupper(substr(str_replace('dr. ', '', $doctor->name), 0, 2)) }}
                                </div>
                                <div>
                                    <a href="{{ url('/dokter/' . $doctor->slug) }}" class="font-bold text-base text-slate-900 dark:text-white hover:text-teal-600 transition">
                                        {{ $doctor->name }}
                                    </a>
                                    <p class="text-xs text-slate-500">{{ $doctor->specialization }}</p>
                                </div>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-gray-700/50 text-xs space-y-1">
                                <p class="font-semibold text-slate-600 dark:text-slate-400">Jadwal Praktik:</p>
                                @forelse($doctor->schedules as $sched)
                                    <div class="flex justify-between text-slate-700 dark:text-slate-300">
                                        <span class="font-bold text-teal-700 dark:text-teal-400">{{ $sched->day_name }}</span>
                                        <span class="font-mono">{{ substr($sched->start_time, 0, 5) }} - {{ substr($sched->end_time, 0, 5) }}</span>
                                    </div>
                                @empty
                                    <span class="text-slate-400 italic">Belum ada jadwal</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 dark:border-gray-700 flex items-center justify-between">
                            <span class="text-xs font-bold text-teal-700 dark:text-teal-400">
                                Rp {{ number_format($doctor->consultation_fee, 0, ',', '.') }}
                            </span>
                            <a href="{{ url('/akun/booking?doctor=' . $doctor->id) }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-teal-600 hover:bg-teal-700 text-white shadow-sm transition">
                                Booking Antrean
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-8 text-center text-slate-500 bg-white rounded-2xl border border-slate-200">
                        Belum ada dokter yang ditugaskan di poliklinik ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

