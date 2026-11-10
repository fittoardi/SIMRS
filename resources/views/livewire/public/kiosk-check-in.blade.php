<div class="max-w-2xl mx-auto space-y-6">
    <!-- State: Check-In Berhasil -->
    @if($checkedInQueue)
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-10 border-2 border-emerald-500 shadow-2xl text-center space-y-6 animate-in zoom-in-95 duration-200">
            <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <div>
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                    Kedatangan Terkonfirmasi
                </span>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mt-2">
                    Check-In Mandiri Berhasil!
                </h3>
                <p class="text-xs text-slate-500 mt-1">Kehadiran Anda telah dicatat di sistem antrean poliklinik RS Aisyah Medika.</p>
            </div>

            <!-- Kartu Nomor Antrean Besar -->
            <div class="p-6 rounded-3xl bg-slate-50 dark:bg-gray-700/60 border border-slate-200 dark:border-gray-600 space-y-2">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Nomor Antrean Anda</span>
                <div class="text-5xl sm:text-6xl font-black font-mono tracking-tight text-teal-700 dark:text-teal-300">
                    {{ $checkedInQueue->queue_code }}
                </div>
                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">
                    {{ $checkedInQueue->visit?->patient?->name }} (No. RM: {{ $checkedInQueue->visit?->patient?->medical_record_number }})
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4 text-left text-xs bg-teal-50/50 dark:bg-teal-950/20 p-4 rounded-2xl border border-teal-100 dark:border-teal-900">
                <div>
                    <span class="text-slate-500 block">Poliklinik Tujuan:</span>
                    <strong class="text-slate-900 dark:text-white">{{ $checkedInQueue->visit?->polyclinic?->name }}</strong>
                </div>
                <div>
                    <span class="text-slate-500 block">Dokter Spesialis:</span>
                    <strong class="text-teal-700 dark:text-teal-400">{{ $checkedInQueue->visit?->doctor?->name }}</strong>
                </div>
                <div>
                    <span class="text-slate-500 block">Ruangan:</span>
                    <strong class="text-slate-900 dark:text-white">{{ $checkedInQueue->doctorSchedule?->room_name ?? 'Lantai 2' }}</strong>
                </div>
                <div>
                    <span class="text-slate-500 block">Estimasi Waktu:</span>
                    <strong class="text-slate-900 dark:text-white font-mono">{{ $checkedInQueue->estimated_time ?? substr($checkedInQueue->doctorSchedule?->start_time, 0, 5) }} WIB</strong>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 text-xs">
                Silakan menuju <strong>Ruang Tunggu Poliklinik</strong>. Pantau layar display TV untuk panggilan nama dan nomor antrean Anda.
            </div>

            <div class="pt-4">
                <button type="button"
                        wire:click="resetKiosk"
                        class="w-full py-4 rounded-2xl font-bold text-sm bg-teal-600 hover:bg-teal-700 text-white shadow-lg shadow-teal-600/30 transition">
                    Selesai / Pasien Berikutnya →
                </button>
            </div>
        </div>
    @else
        <!-- State: Form Input Kiosk Check-In -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-10 border border-slate-200 dark:border-gray-700 shadow-xl space-y-6">
            <div class="text-center space-y-2">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-teal-600 to-emerald-500 text-white flex items-center justify-center mx-auto shadow-md shadow-teal-600/20">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white">Kiosk Mandiri Konfirmasi Kedatangan</h3>
                <p class="text-xs text-slate-500">Konfirmasi kehadiran Anda di RS Aisyah Medika untuk mengaktifkan panggilan antrean.</p>
            </div>

            <!-- Tab Switch Mode: Nomor Tiket vs NIK -->
            <div class="flex rounded-2xl bg-slate-100 dark:bg-gray-700/60 p-1.5">
                <button type="button"
                        wire:click="switchMode('code')"
                        class="flex-1 py-3 rounded-xl font-bold text-xs sm:text-sm transition flex items-center justify-center space-x-2
                            {{ $mode === 'code' ? 'bg-white dark:bg-gray-800 text-teal-700 dark:text-teal-300 shadow-md' : 'text-slate-600 dark:text-slate-300 hover:text-teal-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    <span>Nomor Tiket / Antrean</span>
                </button>
                <button type="button"
                        wire:click="switchMode('nik')"
                        class="flex-1 py-3 rounded-xl font-bold text-xs sm:text-sm transition flex items-center justify-center space-x-2
                            {{ $mode === 'nik' ? 'bg-white dark:bg-gray-800 text-teal-700 dark:text-teal-300 shadow-md' : 'text-slate-600 dark:text-slate-300 hover:text-teal-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                    </svg>
                    <span>NIK Pasien (16 Digit)</span>
                </button>
            </div>

            <!-- Error Banner -->
            @if($errorMessage)
                <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs flex items-center space-x-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ $errorMessage }}</span>
                </div>
            @endif

            <!-- Form Mode 1: Nomor Tiket / Kode Antrean -->
            @if($mode === 'code')
                <form wire:submit="checkInByCode" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Masukkan Nomor Antrean / Kode e-Tiket / Token QR
                        </label>
                        <input type="text"
                               wire:model="inputCode"
                               autofocus
                               placeholder="Contoh: POL-261110-001 atau TKT-..."
                               class="w-full text-center text-xl sm:text-2xl font-mono font-bold tracking-wider py-4 rounded-2xl border-2 border-slate-300 dark:border-gray-600 dark:bg-gray-700 focus:border-teal-500 focus:ring-teal-500 uppercase">
                    </div>

                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full py-4 rounded-2xl font-bold text-sm bg-teal-600 hover:bg-teal-700 text-white shadow-lg shadow-teal-600/30 transition disabled:opacity-50">
                        <span wire:loading.remove>Konfirmasi Check-In Sekarang →</span>
                        <span wire:loading>Memeriksa Data...</span>
                    </button>
                </form>
            @endif

            <!-- Form Mode 2: NIK -->
            @if($mode === 'nik')
                <form wire:submit="checkInByNik" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Masukkan 16 Digit NIK KTP Pasien
                        </label>
                        <input type="text"
                               wire:model="inputNik"
                               maxlength="16"
                               autofocus
                               placeholder="16 digit NIK"
                               class="w-full text-center text-xl sm:text-2xl font-mono font-bold tracking-wider py-4 rounded-2xl border-2 border-slate-300 dark:border-gray-600 dark:bg-gray-700 focus:border-teal-500 focus:ring-teal-500">
                    </div>

                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full py-4 rounded-2xl font-bold text-sm bg-teal-600 hover:bg-teal-700 text-white shadow-lg shadow-teal-600/30 transition disabled:opacity-50">
                        <span wire:loading.remove>Cari & Konfirmasi Check-In →</span>
                        <span wire:loading>Memeriksa NIK Pasien...</span>
                    </button>
                </form>
            @endif

            <!-- Petunjuk Kiosk -->
            <div class="pt-4 border-t border-slate-100 dark:border-gray-700 text-center">
                <p class="text-xs text-slate-400">
                    Butuh bantuan? Silakan hubungi <strong>Staf Informasi & Loket Pendaftaran</strong> terdekat.
                </p>
            </div>
        </div>
    @endif
</div>
