<div class="max-w-4xl mx-auto space-y-6">
    <!-- Step Indicator Progress Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-4 sm:p-6 border border-slate-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between">
            @php
                $steps = [
                    1 => ['title' => 'Pasien', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                    2 => ['title' => 'Dokter', 'icon' => 'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    3 => ['title' => 'Jadwal', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    4 => ['title' => 'Bayar', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                    5 => ['title' => 'Selesai', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
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

    <!-- Error Session Banner -->
    @if (session()->has('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm flex items-center space-x-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Content Container -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-gray-700 shadow-sm space-y-6">

        <!-- ==============================================
             LANGKAH 1: PILIH PASIEN
             ============================================== -->
        @if($currentStep === 1)
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-4 border-b border-slate-100 dark:border-gray-700">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Langkah 1: Siapa yang Akan Berobat?</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pilih profil diri sendiri atau anggota keluarga yang terdaftar di akun Anda.</p>
                    </div>
                    <a href="{{ route('patient.family') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                        + Tambah Keluarga Baru
                    </a>
                </div>

                @error('selectedPatientId')
                    <div class="p-3 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 text-xs">
                        {{ $message }}
                    </div>
                @enderror

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($this->availablePatients as $pt)
                        <div wire:click="selectPatient({{ $pt['id'] }})"
                             class="cursor-pointer rounded-2xl p-5 border-2 transition relative flex flex-col justify-between space-y-3
                                {{ $selectedPatientId === $pt['id'] ? 'border-teal-600 bg-teal-50/60 dark:bg-teal-900/30 dark:border-teal-400 ring-2 ring-teal-600/20' : 'border-slate-200 dark:border-gray-700 hover:border-teal-300' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-xl {{ $selectedPatientId === $pt['id'] ? 'bg-teal-600 text-white' : 'bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-300' }} flex items-center justify-center font-bold text-sm">
                                        {{ strtoupper(substr($pt['name'], 0, 2)) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ $pt['name'] }}</h4>
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-slate-300 mt-0.5">
                                            {{ $pt['relation'] }}
                                        </span>
                                    </div>
                                </div>
                                <div class="w-5 h-5 rounded-full border flex items-center justify-center {{ $selectedPatientId === $pt['id'] ? 'border-teal-600 bg-teal-600 text-white' : 'border-slate-300' }}">
                                    @if($selectedPatientId === $pt['id'])
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-100 dark:border-gray-700/60 text-xs space-y-1">
                                <div class="flex justify-between text-slate-500">
                                    <span>No. RM:</span>
                                    <span class="font-mono font-bold text-teal-700 dark:text-teal-300">{{ $pt['mrn'] ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between text-slate-500">
                                    <span>NIK:</span>
                                    <span class="font-mono font-semibold">{{ $pt['nik'] ?? 'Belum Lengkap' }}</span>
                                </div>
                            </div>

                            @if($pt['allergies'])
                                <div class="p-2 rounded-lg bg-rose-100/70 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 text-[11px] font-medium flex items-center">
                                    <span class="font-bold mr-1">Alergi:</span> {{ $pt['allergies'] }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-8">
                            <p class="text-sm text-slate-500">Profil pasien belum lengkap. Silakan lengkapi data pasien Anda.</p>
                            <a href="{{ route('patient.profile') }}" class="inline-block mt-3 px-4 py-2 bg-teal-600 text-white text-xs font-semibold rounded-xl">
                                Lengkapi Profil Pasien
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

        <!-- ==============================================
             LANGKAH 2: POLIKLINIK & DOKTER
             ============================================== -->
        @elseif($currentStep === 2)
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Langkah 2: Pilih Poliklinik & Dokter</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Tentukan poliklinik tujuan dan dokter spesialis yang akan memeriksa.</p>
                </div>

                <!-- Pilihan Poliklinik -->
                <div>
                    <label class="block text-xs uppercase font-bold tracking-wider text-teal-700 dark:text-teal-400 mb-2">
                        1. Pilih Poliklinik
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($this->polyclinics as $poli)
                            <button type="button"
                                    wire:click="$set('selectedPolyclinicId', {{ $poli->id }})"
                                    class="p-4 rounded-2xl border-2 text-left transition flex flex-col justify-between space-y-2
                                        {{ $selectedPolyclinicId === $poli->id ? 'border-teal-600 bg-teal-50/60 dark:bg-teal-900/30 dark:border-teal-400 ring-2 ring-teal-600/20' : 'border-slate-200 dark:border-gray-700 hover:border-teal-300' }}">
                                <div class="w-8 h-8 rounded-xl bg-teal-100 dark:bg-teal-900/50 text-teal-700 dark:text-teal-300 flex items-center justify-center font-bold text-xs">
                                    {{ $poli->code }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white">{{ $poli->name }}</h4>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">{{ $poli->doctors_count }} Dokter</span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                    @error('selectedPolyclinicId') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Pilihan Dokter -->
                @if($selectedPolyclinicId)
                    <div class="pt-4 border-t border-slate-100 dark:border-gray-700">
                        <label class="block text-xs uppercase font-bold tracking-wider text-teal-700 dark:text-teal-400 mb-3">
                            2. Pilih Dokter Spesialis
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @forelse($this->doctors as $doc)
                                <div wire:click="$set('selectedDoctorId', {{ $doc->id }})"
                                     class="cursor-pointer rounded-2xl p-4 border-2 transition flex items-center space-x-4
                                        {{ $selectedDoctorId === $doc->id ? 'border-teal-600 bg-teal-50/60 dark:bg-teal-900/30 dark:border-teal-400 ring-2 ring-teal-600/20' : 'border-slate-200 dark:border-gray-700 hover:border-teal-300' }}">
                                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-teal-600 to-emerald-500 text-white flex items-center justify-center font-bold text-base shadow-sm shrink-0">
                                        {{ strtoupper(substr(str_replace('dr. ', '', $doc->name), 0, 2)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ $doc->name }}</h4>
                                        <p class="text-xs text-teal-600 dark:text-teal-400">{{ $doc->specialization }}</p>
                                        <div class="mt-1 flex items-center justify-between text-xs">
                                            <span class="text-slate-500 font-mono">Rp {{ number_format($doc->consultation_fee, 0, ',', '.') }}</span>
                                            <span class="text-[11px] font-semibold text-teal-700 dark:text-teal-300">{{ $doc->schedules->count() }} Sesi Praktik</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-2 text-center py-6 text-xs text-slate-400 italic">
                                    Belum ada dokter aktif pada poliklinik ini.
                                </div>
                            @endforelse
                        </div>
                        @error('selectedDoctorId') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif
            </div>

        <!-- ==============================================
             LANGKAH 3: TANGGAL & SESI JADWAL
             ============================================== -->
        @elseif($currentStep === 3)
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Langkah 3: Pilih Tanggal & Sesi Jam Praktik</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Dokter: <strong class="text-teal-700 dark:text-teal-300">{{ $selectedDoctor?->name }}</strong> ({{ $selectedPolyclinic?->name }})
                    </p>
                </div>

                <!-- Input Tanggal -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-gray-700/50 border border-slate-200 dark:border-gray-700 space-y-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Tanggal Rencana Kunjungan
                    </label>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <input type="date"
                               wire:model.live="selectedDate"
                               min="{{ date('Y-m-d') }}"
                               max="{{ date('Y-m-d', strtotime('+14 days')) }}"
                               class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-800 text-sm font-semibold focus:ring-2 focus:ring-teal-500">
                        <span class="text-xs text-slate-500">
                            Hari: <strong class="text-teal-700 dark:text-teal-300 font-bold">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</strong>
                        </span>
                    </div>
                    @error('selectedDate') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Pilihan Sesi Jam Praktik Dokter -->
                <div>
                    <label class="block text-xs uppercase font-bold tracking-wider text-teal-700 dark:text-teal-400 mb-3">
                        Sesi Praktik Tersedia & Sisa Kuota Real-Time
                    </label>

                    <div class="space-y-3">
                        @forelse($this->doctorSchedules as $session)
                            <div wire:click="{{ !$session['is_full'] ? '$set(\'selectedScheduleId\', ' . $session['id'] . ')' : '' }}"
                                 class="p-4 rounded-2xl border-2 transition flex items-center justify-between
                                    {{ $session['is_full'] ? 'opacity-60 bg-slate-50 dark:bg-gray-800/40 border-slate-200 dark:border-gray-700 cursor-not-allowed' : ($selectedScheduleId === $session['id'] ? 'border-teal-600 bg-teal-50/60 dark:bg-teal-900/30 dark:border-teal-400 ring-2 ring-teal-600/20 cursor-pointer' : 'border-slate-200 dark:border-gray-700 hover:border-teal-300 cursor-pointer') }}">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-900/50 text-teal-700 dark:text-teal-300 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-sm text-slate-900 dark:text-white font-mono">
                                            {{ $session['start_time'] }} - {{ $session['end_time'] }} WIB
                                        </h4>
                                        <p class="text-xs text-slate-400">{{ $session['room_name'] }}</p>
                                    </div>
                                </div>

                                <div class="text-right">
                                    @if($session['is_full'])
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">
                                            Kuota Penuh
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                            Tersisa {{ $session['remaining'] }} Kuota (dari {{ $session['max_quota'] }})
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-6 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-center space-y-1">
                                <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">Tidak ada sesi praktik dokter pada hari ini.</p>
                                <p class="text-xs text-amber-600 dark:text-amber-400">Silakan pilih tanggal lain yang sesuai dengan hari kerja dokter.</p>
                            </div>
                        @endforelse
                    </div>
                    @error('selectedScheduleId') <p class="text-xs text-rose-500 mt-2">{{ $message }}</p> @enderror
                </div>
            </div>

        <!-- ==============================================
             LANGKAH 4: CARA PEMBAYARAN & DOKUMEN
             ============================================== -->
        @elseif($currentStep === 4)
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Langkah 4: Cara Pembayaran & Dokumen</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih metode penjaminan pembiayaan kunjungan berobat Anda.</p>
                </div>

                <!-- Tipe Pembayaran -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Umum / Mandiri -->
                    <div wire:click="$set('paymentType', 'general')"
                         class="cursor-pointer rounded-2xl p-5 border-2 transition text-left flex flex-col justify-between space-y-3
                            {{ $paymentType === 'general' ? 'border-teal-600 bg-teal-50/60 dark:bg-teal-900/30 dark:border-teal-400 ring-2 ring-teal-600/20' : 'border-slate-200 dark:border-gray-700 hover:border-teal-300' }}">
                        <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-900/50 text-teal-700 dark:text-teal-300 flex items-center justify-center font-bold">
                            Rp
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-slate-900 dark:text-white">Pasien Umum / Tunai</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Biaya konsultasi & obat ditanggung mandiri di kasir.</p>
                        </div>
                    </div>

                    <!-- BPJS Kesehatan -->
                    <div wire:click="$set('paymentType', 'bpjs')"
                         class="cursor-pointer rounded-2xl p-5 border-2 transition text-left flex flex-col justify-between space-y-3
                            {{ $paymentType === 'bpjs' ? 'border-teal-600 bg-teal-50/60 dark:bg-teal-900/30 dark:border-teal-400 ring-2 ring-teal-600/20' : 'border-slate-200 dark:border-gray-700 hover:border-teal-300' }}">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold">
                            BPJS
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-slate-900 dark:text-white">BPJS Kesehatan</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Wajib memiliki surat rujukan Faskes 1 yang masih aktif.</p>
                        </div>
                    </div>

                    <!-- Asuransi Swasta -->
                    <div wire:click="$set('paymentType', 'insurance')"
                         class="cursor-pointer rounded-2xl p-5 border-2 transition text-left flex flex-col justify-between space-y-3
                            {{ $paymentType === 'insurance' ? 'border-teal-600 bg-teal-50/60 dark:bg-teal-900/30 dark:border-teal-400 ring-2 ring-teal-600/20' : 'border-slate-200 dark:border-gray-700 hover:border-teal-300' }}">
                        <div class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-900/50 text-sky-700 dark:text-sky-300 flex items-center justify-center font-bold">
                            ASR
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-slate-900 dark:text-white">Asuransi Swasta</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Klaim cashless atau reimbursement asuransi rekanan.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Dokumen Spesifik BPJS -->
                @if($paymentType === 'bpjs')
                    <div class="p-5 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800 space-y-4">
                        <h4 class="font-bold text-sm text-emerald-900 dark:text-emerald-200">Data Penjaminan BPJS Kesehatan</h4>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Nomor Kartu BPJS Kesehatan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   wire:model="bpjsCardNumber"
                                   placeholder="Contoh: 0001234567890"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-800 text-sm font-mono focus:ring-2 focus:ring-emerald-500">
                            @error('bpjsCardNumber') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Upload Foto / Scan Surat Rujukan Faskes 1 (Opsional / Dianjurkan)
                            </label>
                            <input type="file"
                                   wire:model="referralLetter"
                                   accept=".jpg,.jpeg,.png,.pdf"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700">
                            <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, atau PDF (maks. 2 MB)</p>
                            @error('referralLetter') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                @endif

                <!-- Form Dokumen Spesifik Asuransi Swasta -->
                @if($paymentType === 'insurance')
                    <div class="p-5 rounded-2xl bg-sky-50/60 dark:bg-sky-950/20 border border-sky-200 dark:border-sky-800 space-y-4">
                        <h4 class="font-bold text-sm text-sky-900 dark:text-sky-200">Data Penjaminan Asuransi Swasta</h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Nama Perusahaan Asuransi <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model="insuranceName"
                                       placeholder="Contoh: Prudential / Allianz / Sinarmas"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-800 text-sm focus:ring-2 focus:ring-sky-500">
                                @error('insuranceName') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Nomor Polis / Kartu Asuransi <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model="insuranceCardNumber"
                                       placeholder="Nomor polis asuransi"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-800 text-sm font-mono focus:ring-2 focus:ring-sky-500">
                                @error('insuranceCardNumber') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Keluhan Singkat Pasien -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Keluhan Singkat / Gejala (Opsional)
                    </label>
                    <textarea wire:model="complaint"
                              rows="2"
                              placeholder="Contoh: Batuk demam sejak 3 hari, nyeri ulu hati"
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500"></textarea>
                </div>
            </div>

        <!-- ==============================================
             LANGKAH 5: KONFIRMASI RINGKASAN & TERBITKAN TIKET
             ============================================== -->
        @elseif($currentStep === 5)
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Langkah 5: Konfirmasi Ringkasan Janji Antrean</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Periksa kembali data pendaftaran sebelum menerbitkan tiket antrean digital Anda.</p>
                </div>

                <div class="bg-gradient-to-tr from-slate-50 to-teal-50/30 dark:from-gray-800 dark:to-gray-800/80 rounded-3xl p-6 sm:p-8 border border-teal-200 dark:border-gray-700 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Info Pasien -->
                        <div class="space-y-3">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-teal-700 dark:text-teal-400 block">
                                Pasien yang Berobat
                            </span>
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-2xl bg-teal-600 text-white font-bold flex items-center justify-center text-lg">
                                    {{ strtoupper(substr($selectedPatient?->name ?? 'P', 0, 2)) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-base text-slate-900 dark:text-white">{{ $selectedPatient?->name }}</h4>
                                    <p class="text-xs text-slate-500 font-mono">No. RM: {{ $selectedPatient?->medical_record_number }}</p>
                                    <p class="text-xs text-slate-500 font-mono">NIK: {{ $selectedPatient?->nik }}</p>
                                </div>
                            </div>
                            @if($selectedPatient?->allergies)
                                <div class="p-2.5 rounded-xl bg-rose-100/80 dark:bg-rose-950/40 text-rose-800 dark:text-rose-200 text-xs font-semibold flex items-center">
                                    <span class="mr-1 font-bold">Alergi:</span> {{ $selectedPatient->allergies }}
                                </div>
                            @endif
                        </div>

                        <!-- Info Dokter & Jadwal -->
                        <div class="space-y-3">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-teal-700 dark:text-teal-400 block">
                                Layanan & Jadwal Kunjungan
                            </span>
                            <div class="space-y-1.5 text-xs">
                                <div class="flex justify-between py-1 border-b border-slate-200 dark:border-gray-700">
                                    <span class="text-slate-500">Poliklinik:</span>
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $selectedPolyclinic?->name }}</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-slate-200 dark:border-gray-700">
                                    <span class="text-slate-500">Dokter Spesialis:</span>
                                    <span class="font-bold text-teal-700 dark:text-teal-300">{{ $selectedDoctor?->name }}</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-slate-200 dark:border-gray-700">
                                    <span class="text-slate-500">Hari & Tanggal:</span>
                                    <span class="font-bold text-slate-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}
                                    </span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-slate-200 dark:border-gray-700">
                                    <span class="text-slate-500">Sesi Jam Praktik:</span>
                                    <span class="font-bold font-mono text-slate-900 dark:text-white">
                                        {{ substr($selectedSchedule?->start_time, 0, 5) }} - {{ substr($selectedSchedule?->end_time, 0, 5) }} WIB
                                    </span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-slate-500">Penjamin / Bayar:</span>
                                    <span class="font-bold uppercase text-slate-900 dark:text-white">
                                        {{ $paymentType === 'bpjs' ? 'BPJS Kesehatan (' . $bpjsCardNumber . ')' : ($paymentType === 'insurance' ? 'Asuransi (' . $insuranceName . ')' : 'Pasien Umum / Tunai') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-teal-50 dark:bg-teal-900/40 border border-teal-200 dark:border-teal-700/60 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-teal-700 dark:text-teal-300">Estimasi Biaya Konsultasi Dokter</span>
                            <p class="text-lg font-bold text-teal-800 dark:text-teal-200">
                                Rp {{ number_format($selectedDoctor?->consultation_fee, 0, ',', '.') }}
                            </p>
                        </div>
                        <span class="text-xs font-semibold text-teal-600 dark:text-teal-400">
                            {{ $paymentType === 'bpjs' ? 'Dijamin BPJS Kesehatan' : ($paymentType === 'insurance' ? 'Klaim Asuransi Rekanan' : 'Bayar di Kasir RS') }}
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Navigation Buttons (Prev / Next / Confirm) -->
        <div class="pt-6 border-t border-slate-100 dark:border-gray-700 flex items-center justify-between">
            @if($currentStep > 1)
                <button type="button"
                        wire:click="prevStep"
                        class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-gray-700 transition">
                    ← Kembali
                </button>
            @else
                <div></div>
            @endif

            @if($currentStep < 5)
                <button type="button"
                        wire:click="nextStep"
                        class="px-6 py-2.5 rounded-xl font-bold text-xs text-white bg-teal-600 hover:bg-teal-700 shadow-sm transition">
                    Lanjutkan ke Langkah Berikutnya →
                </button>
            @else
                <button type="button"
                        wire:click="confirmBooking"
                        wire:loading.attr="disabled"
                        class="px-8 py-3 rounded-xl font-bold text-sm text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-600/20 transition disabled:opacity-50">
                    <span wire:loading.remove>Konfirmasi & Ambil Antrean Sekarang</span>
                    <span wire:loading>Memproses Tiket Antrean...</span>
                </button>
            @endif
        </div>
    </div>
</div>
