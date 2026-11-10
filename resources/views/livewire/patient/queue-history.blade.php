<div class="space-y-6">
    <!-- Header & Tab Controls -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-gray-700 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Daftar Antrean & Riwayat Kunjungan</h3>
            <p class="text-xs text-slate-500 mt-0.5">Pantau status antrean aktif dan riwayat berobat Anda serta keluarga di RS Aisyah Medika.</p>
        </div>

        <div class="flex items-center space-x-2 bg-slate-100 dark:bg-gray-700/60 p-1.5 rounded-2xl shrink-0">
            <button wire:click="$set('tab', 'active')"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition
                        {{ $tab === 'active' ? 'bg-white dark:bg-gray-800 text-teal-700 dark:text-teal-300 shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-teal-600' }}">
                Antrean Aktif
            </button>
            <button wire:click="$set('tab', 'past')"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition
                        {{ $tab === 'past' ? 'bg-white dark:bg-gray-800 text-teal-700 dark:text-teal-300 shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-teal-600' }}">
                Riwayat Selesai & Batal
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center space-x-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm flex items-center space-x-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Cards List of Queues -->
    <div class="space-y-4">
        @forelse($visits as $v)
            @php
                $q = $v->queue;
                $statusVal = $q?->status?->value ?? 'waiting';
                $badge = match($statusVal) {
                    'waiting' => ['label' => 'Menunggu Giliran', 'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200'],
                    'called' => ['label' => 'Dipanggil ke Ruangan', 'class' => 'bg-amber-500 text-white animate-pulse font-bold'],
                    'serving' => ['label' => 'Sedang Dilayani', 'class' => 'bg-teal-500 text-white font-bold'],
                    'completed' => ['label' => 'Selesai', 'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200'],
                    'cancelled' => ['label' => 'Dibatalkan', 'class' => 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-200'],
                    default => ['label' => 'Menunggu', 'class' => 'bg-slate-100 text-slate-800'],
                };
            @endphp

            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-slate-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6 hover:border-teal-300 transition">
                <div class="flex items-start space-x-4">
                    <!-- Queue Number Box -->
                    <div class="w-20 h-20 rounded-2xl bg-teal-50 dark:bg-teal-900/30 border border-teal-200 dark:border-teal-800 flex flex-col items-center justify-center shrink-0">
                        <span class="text-[10px] uppercase font-bold text-teal-600 dark:text-teal-400">Antrean</span>
                        <span class="text-xl font-black font-mono text-teal-800 dark:text-teal-200">{{ $q?->queue_code }}</span>
                    </div>

                    <!-- Detail Info -->
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="font-bold text-base text-slate-900 dark:text-white">{{ $v->patient?->name }}</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $badge['class'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </div>
                        <p class="text-xs font-semibold text-teal-700 dark:text-teal-400">
                            {{ $v->polyclinic?->name }} • {{ $v->doctor?->name }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ \Carbon\Carbon::parse($v->visit_date)->translatedFormat('l, d F Y') }}
                            • Sesi: <strong class="font-mono text-slate-700 dark:text-slate-300">{{ substr($q?->doctorSchedule?->start_time, 0, 5) }} - {{ substr($q?->doctorSchedule?->end_time, 0, 5) }}</strong>
                        </p>
                        <p class="text-[11px] text-slate-400">
                            Ruangan: {{ $q?->doctorSchedule?->room_name ?? 'Poliklinik' }} • No. RM: <span class="font-mono">{{ $v->patient?->medical_record_number }}</span>
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 shrink-0 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-gray-700">
                    <a href="{{ route('patient.ticket', $v->id) }}"
                       class="px-5 py-2.5 rounded-xl font-bold text-xs bg-teal-600 hover:bg-teal-700 text-white text-center shadow-sm transition">
                        Buka e-Tiket & QR
                    </a>

                    @if($statusVal === 'waiting')
                        <button wire:click="cancelQueue({{ $v->id }})"
                                wire:confirm="Yakin ingin membatalkan antrean ini?"
                                class="px-4 py-2.5 rounded-xl font-semibold text-xs text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 border border-rose-200 dark:border-rose-800 transition">
                            Batalkan
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-12 text-center border border-slate-200 dark:border-gray-700 space-y-3">
                <div class="w-16 h-16 rounded-2xl bg-teal-50 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400 mx-auto flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h4 class="font-bold text-base text-slate-800 dark:text-slate-200">
                    {{ $tab === 'active' ? 'Belum Ada Antrean Aktif' : 'Belum Ada Riwayat Antrean Selesai' }}
                </h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    {{ $tab === 'active' ? 'Anda belum memiliki janji antrean berobat yang sedang berjalan saat ini.' : 'Semua riwayat kunjungan yang telah selesai atau dibatalkan akan tersimpan di sini.' }}
                </p>
                @if($tab === 'active')
                    <div class="pt-2">
                        <a href="{{ route('patient.booking') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 shadow-sm transition">
                            + Ambil Antrean Baru Sekarang
                        </a>
                    </div>
                @endif
            </div>
        @endforelse

        <div class="pt-4">
            {{ $visits->links() }}
        </div>
    </div>
</div>
