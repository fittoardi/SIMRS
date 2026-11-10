<div class="space-y-6">
    <!-- Filter Box & Search Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-slate-200 dark:border-gray-700 shadow-sm space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Search by Name or Specialization -->
            <div class="sm:col-span-5 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama dokter atau spesialis..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:text-white transition"
                >
            </div>

            <!-- Filter by Polyclinic -->
            <div class="sm:col-span-4">
                <select
                    wire:model.live="selectedPolyclinic"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:text-white transition"
                >
                    <option value="">Semua Poliklinik</option>
                    @foreach($polyclinics as $poli)
                        <option value="{{ $poli->id }}">{{ $poli->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter by Day of Week -->
            <div class="sm:col-span-3">
                <select
                    wire:model.live="selectedDay"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:text-white transition"
                >
                    <option value="">Semua Hari</option>
                    <option value="1">Senin</option>
                    <option value="2">Selasa</option>
                    <option value="3">Rabu</option>
                    <option value="4">Kamis</option>
                    <option value="5">Jumat</option>
                    <option value="6">Sabtu</option>
                    <option value="7">Minggu</option>
                </select>
            </div>
        </div>

        <!-- Filter stats and reset -->
        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-gray-700 text-xs text-slate-500">
            <div class="flex items-center space-x-2">
                <span>Ditemukan: <strong class="text-teal-700 dark:text-teal-400 font-bold">{{ $doctors->count() }}</strong> dokter</span>
                <span wire:loading class="text-teal-600 font-semibold inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-1.5 h-3.5 w-3.5 text-teal-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    Memuat...
                </span>
            </div>

            @if($search !== '' || $selectedPolyclinic !== '' || $selectedDay !== '')
                <button
                    wire:click="resetFilters"
                    class="text-xs text-rose-600 hover:text-rose-700 font-semibold underline"
                >
                    Reset Filter
                </button>
            @endif
        </div>
    </div>

    <!-- Desktop Table View (>= 768px) -->
    <div class="hidden md:block bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-gray-700 text-left text-sm">
            <thead class="bg-slate-50 dark:bg-gray-700/50 text-slate-600 dark:text-slate-300 text-xs font-semibold uppercase">
                <tr>
                    <th class="px-6 py-4">Dokter & Spesialisasi</th>
                    <th class="px-6 py-4">Poliklinik</th>
                    <th class="px-6 py-4">Hari & Jam Praktik</th>
                    <th class="px-6 py-4">Tarif Konsultasi</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
                @forelse($doctors as $doctor)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-700/30 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-teal-100 dark:bg-teal-900/50 text-teal-700 dark:text-teal-300 flex items-center justify-center font-bold text-sm shrink-0">
                                    {{ strtoupper(substr(str_replace('dr. ', '', $doctor->name), 0, 2)) }}
                                </div>
                                <div>
                                    <a href="{{ url('/dokter/' . $doctor->slug) }}" class="font-bold text-slate-900 dark:text-white hover:text-teal-600 transition">
                                        {{ $doctor->name }}
                                    </a>
                                    <p class="text-xs text-slate-500">{{ $doctor->specialization }}</p>
                                    @if($doctor->sip_number)
                                        <p class="text-[11px] text-slate-400">SIP: {{ $doctor->sip_number }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ url('/poliklinik/' . $doctor->polyclinic?->slug) }}" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300 hover:bg-teal-100 transition">
                                {{ $doctor->polyclinic?->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-1.5">
                                @forelse($doctor->schedules as $sched)
                                    <div class="flex items-center space-x-2 text-xs">
                                        <span class="font-bold text-teal-700 dark:text-teal-400 w-14">{{ $sched->day_name }}</span>
                                        <span class="text-slate-600 dark:text-slate-300 font-mono">{{ substr($sched->start_time, 0, 5) }} - {{ substr($sched->end_time, 0, 5) }}</span>
                                        <span class="text-[11px] px-1.5 py-0.5 rounded bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-slate-300">
                                            Kuota {{ $sched->max_quota }}
                                        </span>
                                    </div>
                                @empty
                                    <span class="text-xs text-slate-400 italic">Belum ada jadwal aktif</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-200">
                            Rp {{ number_format($doctor->consultation_fee, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ url('/dokter/' . $doctor->slug) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-gray-700 dark:hover:bg-gray-600 transition">
                                Profil
                            </a>
                            <a href="{{ url('/akun/booking?doctor=' . $doctor->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-teal-600 hover:bg-teal-700 text-white shadow-sm transition">
                                Booking
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            <p class="font-semibold text-base">Tidak ada dokter yang cocok dengan kriteria pencarian.</p>
                            <p class="text-xs text-slate-400 mt-1">Silakan ubah filter poliklinik, hari, atau kata kunci pencarian.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Stacked Card View (< 768px) -->
    <div class="md:hidden space-y-4">
        @forelse($doctors as $doctor)
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-slate-200 dark:border-gray-700 shadow-sm space-y-3">
                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-11 h-11 rounded-full bg-teal-100 dark:bg-teal-900/50 text-teal-700 dark:text-teal-300 flex items-center justify-center font-bold text-sm shrink-0">
                            {{ strtoupper(substr(str_replace('dr. ', '', $doctor->name), 0, 2)) }}
                        </div>
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300 mb-0.5">
                                {{ $doctor->polyclinic?->name }}
                            </span>
                            <a href="{{ url('/dokter/' . $doctor->slug) }}" class="block font-bold text-sm text-slate-900 dark:text-white">
                                {{ $doctor->name }}
                            </a>
                            <p class="text-xs text-slate-500">{{ $doctor->specialization }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400">Tarif</span>
                        <p class="text-xs font-bold text-teal-700 dark:text-teal-400">
                            Rp {{ number_format($doctor->consultation_fee, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <!-- Schedule Section -->
                <div class="pt-2 border-t border-slate-100 dark:border-gray-700 space-y-1.5">
                    <p class="text-[11px] font-semibold text-slate-500">Jadwal Praktik:</p>
                    @forelse($doctor->schedules as $sched)
                        <div class="flex items-center justify-between text-xs py-1 px-2 rounded bg-slate-50 dark:bg-gray-700/50">
                            <span class="font-bold text-teal-700 dark:text-teal-400">{{ $sched->day_name }}</span>
                            <span class="font-mono text-slate-700 dark:text-slate-300">{{ substr($sched->start_time, 0, 5) }} - {{ substr($sched->end_time, 0, 5) }}</span>
                            <span class="text-[10px] text-slate-400">Kuota: {{ $sched->max_quota }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada jadwal praktik</p>
                    @endforelse
                </div>

                <!-- Action buttons -->
                <div class="pt-2 grid grid-cols-2 gap-2">
                    <a href="{{ url('/dokter/' . $doctor->slug) }}" class="text-center py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 hover:bg-slate-200 dark:bg-gray-700 transition">
                        Profil Dokter
                    </a>
                    <a href="{{ url('/akun/booking?doctor=' . $doctor->id) }}" class="text-center py-2 rounded-xl text-xs font-semibold bg-teal-600 hover:bg-teal-700 text-white shadow-sm transition">
                        Booking Antrean
                    </a>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-slate-500 bg-white rounded-2xl border border-slate-200">
                <p class="font-semibold text-sm">Tidak ada dokter yang cocok.</p>
                <p class="text-xs text-slate-400 mt-1">Silakan ubah filter pencarian Anda.</p>
            </div>
        @endforelse
    </div>
</div>

