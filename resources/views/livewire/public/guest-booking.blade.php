<div class="max-w-4xl mx-auto space-y-6">
    <!-- Step Indicator Progress Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-4 sm:p-6 border border-slate-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between">
            @php
                $steps = [
                    1 => ['title' => 'Data Diri', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                    2 => ['title' => 'Poli & Dokter', 'icon' => 'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    3 => ['title' => 'Jadwal', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    4 => ['title' => 'Konfirmasi', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
            @endphp

            @foreach($steps as $stepNum => $s)
                <div class="flex flex-col items-center flex-1 relative">
                    <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-2xl flex items-center justify-center font-bold text-xs sm:text-sm transition
                        {{ $currentStep === $stepNum ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30 ring-4 ring-teal-100 dark:ring-teal-900/40' : ($currentStep > $stepNum ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-gray-700 text-slate-400') }}">
                        @if($currentStep > $stepNum)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/>
                            </svg>
                        @endif
                    </div>
                    <span class="text-[10px] sm:text-xs font-semibold mt-1.5 {{ $currentStep >= $stepNum ? 'text-teal-700 dark:text-teal-300' : 'text-slate-400' }}">
                        {{ $s['title'] }}
                    </span>
                </div>
                @if(!$loop->last)
                    <div class="flex-1 h-0.5 mx-1 {{ $currentStep > $stepNum ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-gray-700' }} -mt-4"></div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Error Banner -->
    @if (session()->has('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm flex items-center space-x-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-gray-700 shadow-sm space-y-6">

        <!-- ==============================================
             LANGKAH 1: DATA DIRI PASIEN (TANPA LOGIN)
             ============================================== -->
        @if($currentStep === 1)
            <div class="space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-4 border-b border-slate-100 dark:border-gray-700">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Langkah 1: Identitas Pasien</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Booking cepat tanpa perlu mendaftar atau login akun.</p>
                    </div>
                    @guest
                        <a href="{{ route('login') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 underline">
                            Punya akun pasien? Masuk di sini
                        </a>
                    @endguest
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="guest_nik" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            NIK (16 Digit KTP) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="guest_nik"
                            type="text"
                            wire:model.live.debounce.400ms="nik"
                            maxlength="16"
                            placeholder="Masukkan 16 digit NIK"
                            class="w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-900 text-sm font-mono focus:ring-teal-500 focus:border-teal-500"
                        />
                        @if($existingPatientMatched)
                            <p class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 mt-1">
                                ✓ Data pasien lama ditemukan di sistem RS Aisyah Medika.
                            </p>
                        @endif
                        @error('nik') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="guest_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Nama Lengkap (Sesuai KTP) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="guest_name"
                            type="text"
                            wire:model="name"
                            placeholder="Contoh: Siti Aminah"
                            class="w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:ring-teal-500 focus:border-teal-500"
                        />
                        @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="guest_phone" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            No. HP / WhatsApp <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="guest_phone"
                            type="tel"
                            wire:model="phone"
                            placeholder="0812xxxxxxxx"
                            class="w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:ring-teal-500 focus:border-teal-500"
                        />
                        @error('phone') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="guest_gender" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Jenis Kelamin <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="guest_gender"
                            wire:model="gender"
                            class="w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:ring-teal-500 focus:border-teal-500"
                        >
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                        @error('gender') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="guest_birth_date" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Tanggal Lahir <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="guest_birth_date"
                            type="date"
                            wire:model="birthDate"
                            class="w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:ring-teal-500 focus:border-teal-500"
                        />
                        @error('birthDate') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        @endif

        <!-- ==============================================
             LANGKAH 2: POLIKLINIK & DOKTER
             ============================================== -->
        @if($currentStep === 2)
            <div class="space-y-6">
                <div class="pb-4 border-b border-slate-100 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Langkah 2: Pilih Poliklinik & Dokter</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Tentukan poli tujuan dan dokter spesialis yang Anda inginkan.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">1. Poliklinik Tujuan</label>
                    @error('selectedPolyclinicId') <p class="text-xs text-rose-600 mb-2">{{ $message }}</p> @enderror
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($this->polyclinics as $poli)
                            <div wire:click="$set('selectedPolyclinicId', {{ $poli->id }})"
                                 class="cursor-pointer rounded-2xl p-4 border-2 transition flex items-center space-x-3
                                    {{ $selectedPolyclinicId === $poli->id ? 'border-teal-600 bg-teal-50/70 dark:bg-teal-900/30 dark:border-teal-400' : 'border-slate-200 dark:border-gray-700 hover:border-teal-300' }}">
                                <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-700 font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ $poli->code }}
                                </div>
                                <div>
                                    <p class="font-bold text-sm text-slate-900 dark:text-white">{{ $poli->name }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $poli->doctors_count }} Dokter Tersedia</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($selectedPolyclinicId)
                    <div class="pt-4 border-t border-slate-100 dark:border-gray-700">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">2. Dokter Pemeriksa</label>
                        @error('selectedDoctorId') <p class="text-xs text-rose-600 mb-2">{{ $message }}</p> @enderror
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @forelse($this->doctors as $doc)
                                <div wire:click="$set('selectedDoctorId', {{ $doc->id }})"
                                     class="cursor-pointer rounded-2xl p-4 border-2 transition flex items-center space-x-3.5
                                        {{ $selectedDoctorId === $doc->id ? 'border-teal-600 bg-teal-50/70 dark:bg-teal-900/30 dark:border-teal-400' : 'border-slate-200 dark:border-gray-700 hover:border-teal-300' }}">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-teal-600 to-emerald-500 text-white font-bold flex items-center justify-center shrink-0">
                                        {{ substr($doc->name, 0, 2) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ $doc->name }}</p>
                                        <p class="text-xs text-teal-600 dark:text-teal-400 font-medium truncate">{{ $doc->specialization }}</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $doc->schedules->where('is_active', true)->count() }} Sesi Jadwal/Minggu</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500 col-span-2 italic">Belum ada dokter aktif pada poliklinik ini.</p>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- ==============================================
             LANGKAH 3: TANGGAL & SESI JAM PRAKTIK
             ============================================== -->
        @if($currentStep === 3)
            <div class="space-y-6">
                <div class="pb-4 border-b border-slate-100 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Langkah 3: Pilih Tanggal & Jam Praktik</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Jadwal & kuota antrean dihitung real-time sesuai tanggal kunjungan.</p>
                </div>

                <div>
                    <label for="guest_visit_date" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">1. Tanggal Kunjungan</label>
                    <input
                        id="guest_visit_date"
                        type="date"
                        wire:model.live="selectedDate"
                        min="{{ date('Y-m-d') }}"
                        class="w-full sm:w-72 rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:ring-teal-500 focus:border-teal-500"
                    />
                    @error('selectedDate') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">2. Sesi Jam Praktik ({{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d M Y') }})</label>
                    @error('selectedScheduleId') <p class="text-xs text-rose-600 mb-2">{{ $message }}</p> @enderror

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @forelse($this->doctorSchedules as $sch)
                            <div @if(!$sch['is_full']) wire:click="$set('selectedScheduleId', {{ $sch['id'] }})" @endif
                                 class="rounded-2xl p-4 border-2 transition flex items-center justify-between
                                    {{ $sch['is_full'] ? 'opacity-50 cursor-not-allowed bg-slate-100 border-slate-200 dark:bg-gray-900' : ($selectedScheduleId === $sch['id'] ? 'cursor-pointer border-teal-600 bg-teal-50/70 dark:bg-teal-900/30 dark:border-teal-400' : 'cursor-pointer border-slate-200 dark:border-gray-700 hover:border-teal-300') }}">
                                <div>
                                    <p class="font-bold text-base font-mono text-slate-900 dark:text-white">
                                        {{ $sch['start_time'] }} - {{ $sch['end_time'] }} WIB
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $sch['room_name'] }}</p>
                                </div>
                                <div class="text-right">
                                    @if($sch['is_full'])
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700">Penuh</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700">
                                            Sisa {{ $sch['remaining'] }} / {{ $sch['max_quota'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 p-6 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 text-amber-800 dark:text-amber-300 text-sm text-center">
                                Dokter tidak memiliki jadwal praktik pada hari {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l') }}. Silakan pilih tanggal lain.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        <!-- ==============================================
             LANGKAH 4: PENJAMIN & KONFIRMASI AKHIR
             ============================================== -->
        @if($currentStep === 4)
            <div class="space-y-6">
                <div class="pb-4 border-b border-slate-100 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Langkah 4: Pembayaran & Konfirmasi</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih metode jaminan pembayaran dan periksa kembali ringkasan booking Anda.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Metode Pembayaran / Jaminan</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div wire:click="$set('paymentType', 'general')"
                             class="cursor-pointer rounded-2xl p-4 border-2 transition text-center
                                {{ $paymentType === 'general' ? 'border-teal-600 bg-teal-50/70 dark:bg-teal-900/30' : 'border-slate-200 dark:border-gray-700 hover:border-teal-300' }}">
                            <p class="font-bold text-sm text-slate-900 dark:text-white">Umum / Mandiri</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Bayar tunai / QRIS di kasir</p>
                        </div>

                        <div wire:click="$set('paymentType', 'bpjs')"
                             class="cursor-pointer rounded-2xl p-4 border-2 transition text-center
                                {{ $paymentType === 'bpjs' ? 'border-teal-600 bg-teal-50/70 dark:bg-teal-900/30' : 'border-slate-200 dark:border-gray-700 hover:border-teal-300' }}">
                            <p class="font-bold text-sm text-slate-900 dark:text-white">BPJS Kesehatan</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Jaminan JKN-KIS aktif</p>
                        </div>

                        <div wire:click="$set('paymentType', 'insurance')"
                             class="cursor-pointer rounded-2xl p-4 border-2 transition text-center
                                {{ $paymentType === 'insurance' ? 'border-teal-600 bg-teal-50/70 dark:bg-teal-900/30' : 'border-slate-200 dark:border-gray-700 hover:border-teal-300' }}">
                            <p class="font-bold text-sm text-slate-900 dark:text-white">Asuransi Swasta</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Prudential, AXA, Allianz, dll</p>
                        </div>
                    </div>
                </div>

                @if($paymentType === 'bpjs')
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Kartu BPJS Kesehatan *</label>
                        <input type="text" wire:model="bpjsCardNumber" placeholder="13 digit nomor kartu BPJS"
                               class="w-full sm:w-80 rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-900 text-sm font-mono focus:ring-teal-500 focus:border-teal-500" />
                        @error('bpjsCardNumber') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                @elseif($paymentType === 'insurance')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Perusahaan Asuransi *</label>
                            <input type="text" wire:model="insuranceName" placeholder="Contoh: Prudential"
                                   class="w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:ring-teal-500 focus:border-teal-500" />
                            @error('insuranceName') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Polis / Kartu *</label>
                            <input type="text" wire:model="insuranceCardNumber" placeholder="Nomor kartu asuransi"
                                   class="w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-900 text-sm font-mono focus:ring-teal-500 focus:border-teal-500" />
                            @error('insuranceCardNumber') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Keluhan Singkat (Opsional)</label>
                    <textarea wire:model="complaint" rows="2" placeholder="Contoh: Demam sejak 2 hari lalu, batuk ringan"
                              class="w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:ring-teal-500 focus:border-teal-500"></textarea>
                </div>

                <!-- Summary Card -->
                <div class="rounded-2xl p-5 bg-teal-50/70 dark:bg-teal-950/30 border border-teal-200 dark:border-teal-800 space-y-2 text-xs">
                    <p class="font-bold uppercase tracking-wider text-teal-800 dark:text-teal-300 mb-2">Ringkasan Pendaftaran</p>
                    <div class="flex justify-between py-1 border-b border-teal-200/60 dark:border-teal-800/60">
                        <span class="text-slate-600 dark:text-slate-400">Nama Pasien:</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $name }} (NIK: {{ $nik }})</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-teal-200/60 dark:border-teal-800/60">
                        <span class="text-slate-600 dark:text-slate-400">Poliklinik & Dokter:</span>
                        <span class="font-bold text-teal-700 dark:text-teal-300">{{ $selectedPolyclinic?->name }} — {{ $selectedDoctor?->name }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-600 dark:text-slate-400">Tanggal & Jam Praktik:</span>
                        <span class="font-bold text-slate-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d M Y') }} ({{ substr($selectedSchedule?->start_time ?? '', 0, 5) }} - {{ substr($selectedSchedule?->end_time ?? '', 0, 5) }} WIB)
                        </span>
                    </div>
                </div>

                <!-- Section Verifikasi OTP WhatsApp -->
                <div class="rounded-2xl p-5 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-950/30 dark:to-teal-950/30 border border-emerald-200 dark:border-emerald-800 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🔐</span>
                            <div>
                                <p class="font-bold text-sm text-slate-900 dark:text-white">Verifikasi Nomor WhatsApp Pasien</p>
                                <p class="text-[11px] text-slate-500">Kirim kode OTP ke nomor: <span class="font-mono font-bold text-teal-700 dark:text-teal-300">{{ $phone }}</span></p>
                            </div>
                        </div>

                        @if($otpVerified)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300 font-bold text-xs shrink-0 self-start sm:self-auto">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                WhatsApp Terverifikasi
                            </span>
                        @else
                            <button
                                type="button"
                                wire:click="requestBookingOtp"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 rounded-xl font-bold text-xs bg-teal-600 hover:bg-teal-700 text-white shadow-sm transition shrink-0 self-start sm:self-auto"
                            >
                                <span wire:loading.remove wire:target="requestBookingOtp">
                                    {{ $otpSent ? 'Kirim Ulang OTP' : 'Kirim Kode OTP WA' }}
                                </span>
                                <span wire:loading wire:target="requestBookingOtp">Mengirim...</span>
                            </button>
                        @endif
                    </div>

                    @if($otpMessage)
                        <p class="text-xs text-emerald-700 dark:text-emerald-300 font-medium">
                            {{ $otpMessage }}
                        </p>
                    @endif

                    @if(!$otpVerified && $otpSent)
                        <div class="pt-2 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                            <div class="w-full sm:w-48">
                                <input
                                    type="text"
                                    wire:model.live="otpCode"
                                    maxlength="6"
                                    placeholder="6 Digit OTP"
                                    class="w-full text-center text-lg font-mono font-extrabold tracking-widest rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-teal-500 focus:border-teal-500"
                                />
                            </div>
                            <button
                                type="button"
                                wire:click="verifyBookingOtp"
                                class="px-4 py-2.5 rounded-xl font-bold text-xs bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition"
                            >
                                Verifikasi OTP ✓
                            </button>
                        </div>
                    @endif

                    @error('otpCode')
                        <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endif

        <!-- Navigation Buttons -->
        <div class="pt-6 border-t border-slate-100 dark:border-gray-700 flex items-center justify-between gap-4">
            @if($currentStep > 1)
                <button type="button" wire:click="prevStep"
                        class="px-5 py-2.5 rounded-xl font-semibold text-xs sm:text-sm text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 transition">
                    ← Sebelumnya
                </button>
            @else
                <div></div>
            @endif

            @if($currentStep < 4)
                <button type="button" wire:click="nextStep"
                        class="px-6 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20 transition">
                    Lanjutkan →
                </button>
            @else
                <button type="button" wire:click="confirmBooking"
                        class="px-6 py-3 rounded-xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 shadow-lg shadow-teal-600/25 transition">
                    Konfirmasi & Dapatkan Tiket Antrean ✓
                </button>
            @endif
        </div>
    </div>
</div>

