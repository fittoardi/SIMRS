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
                <a href="{{ route('patient.booking') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium text-xs rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ambil Antrean Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Widget Antrean Aktif Hari Ini / Mendatang (Jika Ada) -->
        @if(isset($activeQueues) && $activeQueues->isNotEmpty())
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-teal-500 mr-2 animate-ping"></span>
                        Antrean Aktif Berjalan
                    </h3>
                    <a href="{{ route('patient.queues') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                        Lihat Semua Antrean →
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($activeQueues as $actVisit)
                        @php $actQ = $actVisit->queue; @endphp
                        <div class="rounded-3xl p-5 bg-gradient-to-tr from-teal-800 to-emerald-900 text-white shadow-lg flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 rounded-2xl bg-white/10 border border-white/20 flex flex-col items-center justify-center">
                                    <span class="text-[9px] uppercase font-bold tracking-wider text-teal-200">Nomor</span>
                                    <span class="text-lg font-black font-mono">{{ $actQ?->queue_code }}</span>
                                </div>
                                <div class="space-y-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-sm">{{ $actVisit->patient?->name }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-400 text-amber-950">
                                            {{ $actQ?->status?->value === 'called' ? 'Dipanggil!' : 'Menunggu' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-teal-200">{{ $actVisit->polyclinic?->name }} • {{ $actVisit->doctor?->name }}</p>
                                    <p class="text-[11px] text-teal-300 font-mono">
                                        {{ \Carbon\Carbon::parse($actVisit->visit_date)->translatedFormat('d M Y') }} • Est. {{ $actQ?->estimated_time ?? substr($actQ?->doctorSchedule?->start_time, 0, 5) }} WIB
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('patient.ticket', $actVisit->id) }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-teal-800 hover:bg-teal-50 transition shadow-sm shrink-0">
                                Buka e-Tiket
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Banner Kartu Pasien Digital & Akses Layanan Cepat -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kartu Pasien Digital -->
            <div class="lg:col-span-2 rounded-3xl bg-gradient-to-br from-teal-700 via-teal-800 to-emerald-900 text-white p-6 sm:p-8 shadow-xl relative overflow-hidden flex flex-col justify-between">
                <!-- Background pattern -->
                <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
                    <svg class="w-72 h-72" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4"/>
                    </svg>
                </div>

                <div>
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
                        <div class="mt-4 p-3 rounded-2xl bg-rose-500/30 border border-rose-400/50 text-rose-100 text-xs flex items-center space-x-2">
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

                <div class="pt-6 border-t border-teal-600/50 mt-6 flex flex-wrap items-center justify-between gap-3">
                    <span class="text-xs text-teal-200">Keluarga Terdaftar: <strong>{{ $familyCount ?? 0 }} Anggota</strong></span>
                    <div class="flex items-center space-x-2">
                        @if($patient)
                            <a href="{{ route('patient.card', $patient->id) }}" target="_blank" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-emerald-500/20 hover:bg-emerald-500/30 border border-emerald-400/40 transition text-emerald-200 flex items-center space-x-1">
                                <span>🖨️ Cetak Kartu</span>
                            </a>
                        @endif
                        <a href="{{ route('patient.profile') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-white/10 hover:bg-white/20 transition text-white">
                            Edit Profil
                        </a>
                        <a href="{{ route('patient.family') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-white text-teal-900 hover:bg-teal-50 transition">
                            Keluarga
                        </a>
                    </div>
                </div>
            </div>

            <!-- Akses Cepat Menu Pasien -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-slate-200 dark:border-gray-700 shadow-sm flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-base text-slate-800 dark:text-slate-100 mb-1">Menu Layanan Pasien</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Akses mandiri rawat jalan & info rumah sakit</p>

                    <div class="space-y-2.5">
                        <a href="{{ route('patient.booking') }}" class="flex items-center p-3 rounded-2xl hover:bg-teal-50 dark:hover:bg-gray-700/60 transition group border border-teal-100 dark:border-gray-700 bg-teal-50/40">
                            <div class="w-9 h-9 rounded-xl bg-teal-600 text-white flex items-center justify-center mr-3 group-hover:scale-105 transition shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-teal-900 dark:text-teal-200">Ambil Antrean Online</p>
                                <p class="text-[11px] text-teal-600 dark:text-teal-400">Booking dokter spesialis rawat jalan</p>
                            </div>
                        </a>

                        <a href="{{ route('patient.queues') }}" class="flex items-center p-3 rounded-2xl hover:bg-slate-50 dark:hover:bg-gray-700/60 transition group border border-slate-100 dark:border-gray-700">
                            <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 flex items-center justify-center mr-3 group-hover:scale-105 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 group-hover:text-teal-700">Antrean & Riwayat Kunjungan</p>
                                <p class="text-[11px] text-slate-500">Lihat status & buka e-tiket</p>
                            </div>
                        </a>

                        <a href="{{ route('patient.invoices') }}" class="flex items-center p-3 rounded-2xl hover:bg-slate-50 dark:hover:bg-gray-700/60 transition group border border-slate-100 dark:border-gray-700">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 flex items-center justify-center mr-3 group-hover:scale-105 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 group-hover:text-teal-700">Tagihan & Pembayaran</p>
                                <p class="text-[11px] text-slate-500">Rincian invoice & bayar online</p>
                            </div>
                        </a>

                        <a href="{{ route('patient.family') }}" class="flex items-center p-3 rounded-2xl hover:bg-slate-50 dark:hover:bg-gray-700/60 transition group border border-slate-100 dark:border-gray-700">
                            <div class="w-9 h-9 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 flex items-center justify-center mr-3 group-hover:scale-105 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 group-hover:text-teal-700">Kelola Anggota Keluarga</p>
                                <p class="text-[11px] text-slate-500">Daftarkan anak, pasangan, orang tua</p>
                            </div>
                        </a>

                        <a href="{{ url('/jadwal-dokter') }}" class="flex items-center p-3 rounded-2xl hover:bg-slate-50 dark:hover:bg-gray-700/60 transition group border border-slate-100 dark:border-gray-700">
                            <div class="w-9 h-9 rounded-xl bg-teal-100 dark:bg-teal-900/50 text-teal-700 dark:text-teal-300 flex items-center justify-center mr-3 group-hover:scale-105 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 group-hover:text-teal-700">Cek Jadwal Dokter</p>
                                <p class="text-[11px] text-slate-500">Lihat spesialis & sisa kuota</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-gray-700 mt-4 text-center">
                    <p class="text-[11px] text-slate-500">Bantuan pendaftaran: <span class="font-semibold text-teal-600">(021) 8765-4321</span></p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
