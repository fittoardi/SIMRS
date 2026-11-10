<x-filament-panels::page>
    <div wire:poll.5s class="space-y-6">
        <!-- Filter Bar: Poli & Dokter + Tombol Pasien Walk-In -->
        <div class="p-6 rounded-3xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                        Pilih Poliklinik
                    </label>
                    <select wire:model.live="selectedPolyclinicId" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:border-teal-500 focus:ring-teal-500">
                        @foreach($polyclinics as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                        Pilih Dokter Praktik
                    </label>
                    <select wire:model.live="selectedDoctorId" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:border-teal-500 focus:ring-teal-500">
                        <option value="">Semua Dokter di Poli Ini</option>
                        @foreach($doctors as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center space-x-2 pt-2 md:pt-0">
                <button type="button"
                        wire:click="openWalkInModal"
                        class="inline-flex items-center px-4 py-2.5 rounded-xl font-bold text-xs text-white bg-teal-600 hover:bg-teal-700 shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    + Pasien Walk-in (Loket)
                </button>
            </div>
        </div>

        <!-- MAIN CALLER CONSOLE -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Box Pemanggil Utama (Kiri 7 col) -->
            <div class="lg:col-span-7 bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 border border-gray-200 dark:border-gray-700 shadow-sm space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-base text-gray-900 dark:text-white flex items-center">
                        <span class="w-3 h-3 rounded-full bg-teal-500 mr-2 animate-ping"></span>
                        Konsol Panggilan Aktif
                    </h3>
                    @if($activeQueue)
                        @php
                            $badge = match($activeQueue->status->value) {
                                'called' => 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200',
                                'serving' => 'bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-200',
                                'waiting' => 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-200',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $badge }}">
                            {{ $activeQueue->status->label() }}
                        </span>
                    @endif
                </div>

                @if($activeQueue)
                    <div class="text-center py-6 bg-slate-50 dark:bg-gray-900/50 rounded-3xl border border-slate-200/80 dark:border-gray-700 space-y-3">
                        <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Nomor Antrean Pasien</span>
                        <div class="text-6xl sm:text-7xl font-black font-mono tracking-tight text-teal-600 dark:text-teal-400">
                            {{ $activeQueue->queue_code }}
                        </div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $activeQueue->visit?->patient?->name }}
                        </div>
                        <p class="text-xs text-gray-500 font-mono">
                            No. RM: {{ $activeQueue->visit?->patient?->medical_record_number }} • NIK: {{ $activeQueue->visit?->patient?->nik }}
                        </p>
                        @if($activeQueue->call_count > 0)
                            <span class="inline-block px-3 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                Sudah dipanggil {{ $activeQueue->call_count }} kali
                            </span>
                        @endif
                    </div>

                    <!-- Tombol Aksi Cepat Panggilan -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <!-- Panggil / Panggil Ulang -->
                        <button type="button"
                                wire:click="callQueue"
                                class="py-3 px-4 rounded-2xl font-bold text-xs bg-amber-500 hover:bg-amber-600 text-white shadow-md shadow-amber-500/20 flex flex-col items-center justify-center space-y-1 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                            </svg>
                            <span>{{ $activeQueue->status->value === 'called' ? 'Panggil Ulang' : 'Panggil Pasien' }}</span>
                        </button>

                        <!-- Mulai Layani -->
                        <button type="button"
                                wire:click="serveQueue"
                                class="py-3 px-4 rounded-2xl font-bold text-xs bg-teal-600 hover:bg-teal-700 text-white shadow-md shadow-teal-600/20 flex flex-col items-center justify-center space-y-1 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Mulai Layani</span>
                        </button>

                        <!-- Selesai -->
                        <button type="button"
                                wire:click="completeQueue"
                                class="py-3 px-4 rounded-2xl font-bold text-xs bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/20 flex flex-col items-center justify-center space-y-1 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Selesai</span>
                        </button>

                        <!-- Lewati -->
                        <button type="button"
                                wire:click="skipQueue"
                                class="py-3 px-4 rounded-2xl font-bold text-xs bg-rose-500 hover:bg-rose-600 text-white shadow-md shadow-rose-500/20 flex flex-col items-center justify-center space-y-1 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                            </svg>
                            <span>Lewati</span>
                        </button>
                    </div>
                @else
                    <div class="text-center py-12 text-gray-400">
                        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm font-semibold">Tidak ada antrean menunggu untuk poli & dokter ini.</p>
                        <p class="text-xs text-gray-500 mt-1">Gunakan tombol "+ Pasien Walk-in" jika ada pasien yang mendaftar langsung di loket.</p>
                    </div>
                @endif
            </div>

            <!-- Ringkasan Statistik Antrean Hari Ini (Kanan 5 col) -->
            <div class="lg:col-span-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-5 rounded-3xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
                        <span class="text-xs font-bold uppercase text-gray-400 block">Menunggu Giliran</span>
                        <span class="text-3xl font-black font-mono text-teal-600 dark:text-teal-400">{{ $waitingQueues->where('status.value', 'waiting')->count() }}</span>
                        <p class="text-[11px] text-gray-400 mt-1">Pasien hadir di RS</p>
                    </div>
                    <div class="p-5 rounded-3xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
                        <span class="text-xs font-bold uppercase text-gray-400 block">Selesai Hari Ini</span>
                        <span class="text-3xl font-black font-mono text-emerald-600 dark:text-emerald-400">{{ $completedCount }}</span>
                        <p class="text-[11px] text-gray-400 mt-1">Telah diperiksa</p>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-teal-50 dark:bg-teal-950/30 border border-teal-200 dark:border-teal-800 flex items-center justify-between text-xs">
                    <span class="text-teal-800 dark:text-teal-200 font-semibold">Layar Antrean TV Aktif</span>
                    <a href="{{ url('/layar-antrean') }}" target="_blank" class="font-bold text-teal-600 dark:text-teal-400 hover:underline">
                        Buka Display TV ↗
                    </a>
                </div>
            </div>
        </div>

        <!-- DAFTAR ANTREAN MENUNGGU (Sub-Tabel) -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="font-bold text-sm text-gray-900 dark:text-white uppercase tracking-wider">
                    Daftar Antrean Menunggu Hari Ini ({{ $waitingQueues->count() }})
                </h4>
                <span class="text-xs text-gray-400">Auto-refresh setiap 5 detik</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 uppercase font-bold text-[10px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4 rounded-l-xl">No. Antrean</th>
                            <th class="py-3 px-4">Nama Pasien</th>
                            <th class="py-3 px-4">Poliklinik & Dokter</th>
                            <th class="py-3 px-4">Status & Waktu Check-In</th>
                            <th class="py-3 px-4 text-right rounded-r-xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($waitingQueues as $wq)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                <td class="py-3 px-4 font-mono font-bold text-sm text-teal-700 dark:text-teal-400">
                                    {{ $wq->queue_code }}
                                </td>
                                <td class="py-3 px-4 font-semibold text-gray-900 dark:text-white">
                                    {{ $wq->visit?->patient?->name }}
                                    <span class="block text-[11px] text-gray-400 font-mono font-normal">RM: {{ $wq->visit?->patient?->medical_record_number }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-medium text-gray-800 dark:text-gray-200 block">{{ $wq->visit?->polyclinic?->name }}</span>
                                    <span class="text-[11px] text-gray-400 block">{{ $wq->visit?->doctor?->name }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-300">
                                        {{ $wq->status->label() }}
                                    </span>
                                    <span class="block text-[10px] text-gray-400 mt-0.5">
                                        {{ $wq->checked_in_at ? 'Hadir ' . $wq->checked_in_at->format('H:i') : 'Booking Online' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button type="button"
                                            wire:click="callQueue({{ $wq->id }})"
                                            class="px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white transition shadow-sm">
                                        Panggil
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-xs text-gray-400 italic">
                                    Belum ada antrean terdaftar hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL PENDAFTARAN WALK-IN LOKET ADMISSION -->
        @if($showWalkInModal)
            <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6 animate-in zoom-in-95 duration-150">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
                        <h4 class="font-bold text-lg text-gray-900 dark:text-white">Pendaftaran Pasien Walk-in (Loket)</h4>
                        <button wire:click="closeWalkInModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="registerWalkInPatient" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                NIK Pasien (16 Digit) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   wire:model="walkInNik"
                                   maxlength="16"
                                   placeholder="16 digit NIK KTP"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm font-mono focus:ring-2 focus:ring-teal-500">
                            @error('walkInNik') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                Nama Lengkap Pasien <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   wire:model="walkInName"
                                   placeholder="Nama pasien"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500">
                            @error('walkInName') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Jenis Kelamin</label>
                                <select wire:model="walkInGender" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Tanggal Lahir <span class="text-rose-500">*</span></label>
                                <input type="date" wire:model="walkInBirthDate" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                                @error('walkInBirthDate') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Poliklinik Tujuan</label>
                                <select wire:model="walkInPolyclinicId" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                                    @foreach($polyclinics as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Dokter</label>
                                <select wire:model="walkInDoctorId" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                                    @foreach($doctors as $d)
                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="pt-4 flex items-center justify-end space-x-3">
                            <button type="button" wire:click="closeWalkInModal" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100">
                                Batal
                            </button>
                            <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 shadow-sm transition">
                                Terbitkan Nomor Antrean
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
