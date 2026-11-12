<div wire:poll.10s class="max-w-xl mx-auto space-y-6">
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

    @if($queue->status?->value === 'called')
        <div class="p-5 rounded-3xl bg-amber-500 text-white shadow-xl animate-bounce flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-lg">Giliran Anda Telah Dipanggil!</h4>
                <p class="text-xs text-amber-100">Silakan langsung menuju ke ruang {{ $queue->doctorSchedule?->room_name ?? 'Poliklinik' }} sekarang.</p>
            </div>
        </div>
    @endif

    <!-- Info Simpan Link Tiket -->
    <div x-data="{ copied: false }" class="p-4 rounded-2xl bg-sky-50 dark:bg-sky-950/30 border border-sky-200 dark:border-sky-800 text-sky-900 dark:text-sky-200 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 print:hidden">
        <div>
            <p class="font-bold">Simpan atau Screenshot Tiket Ini</p>
            <p class="text-sky-700 dark:text-sky-300 mt-0.5">Gunakan halaman ini untuk memantau giliran dan check-in di Kiosk RS.</p>
        </div>
        <button
            type="button"
            @click="navigator.clipboard?.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2000)"
            class="px-3.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold shrink-0 transition"
        >
            <span x-show="!copied">Salin Link Tiket</span>
            <span x-show="copied" x-cloak>✓ Link Tersalin</span>
        </button>
    </div>

    <!-- Kartu Tiket Digital -->
    <div id="printable-ticket" class="bg-white dark:bg-gray-800 rounded-3xl border border-slate-200 dark:border-gray-700 shadow-xl overflow-hidden relative print:border-none print:shadow-none">
        <div class="bg-gradient-to-r from-teal-700 via-teal-800 to-emerald-800 text-white p-6 sm:p-8 text-center relative">
            <div class="flex items-center justify-center space-x-2 mb-1">
                <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center text-white font-bold text-sm">
                    +
                </div>
                <h3 class="font-extrabold text-xl tracking-tight">RS Aisyah Medika</h3>
            </div>
            <p class="text-xs text-teal-200">Tiket Antrean Poliklinik Rawat Jalan</p>
            <p class="text-[10px] text-teal-300 font-mono mt-1">Kode Kunjungan: {{ $visit?->visit_number }}</p>

            <div class="mt-4">
                @php
                    $statusBadge = match($queue->status?->value ?? 'waiting') {
                        'waiting' => ['label' => 'Menunggu Giliran', 'bg' => 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200'],
                        'called' => ['label' => 'Sedang Dipanggil', 'bg' => 'bg-amber-500 text-white font-bold animate-pulse'],
                        'serving' => ['label' => 'Sedang Dilayani', 'bg' => 'bg-teal-500 text-white font-bold'],
                        'completed' => ['label' => 'Pelayanan Selesai', 'bg' => 'bg-emerald-500 text-white font-bold'],
                        'cancelled' => ['label' => 'Dibatalkan', 'bg' => 'bg-rose-500 text-white font-bold'],
                        default => ['label' => 'Menunggu', 'bg' => 'bg-slate-200 text-slate-800'],
                    };
                @endphp
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $statusBadge['bg'] }}">
                    {{ $statusBadge['label'] }}
                </span>
            </div>
        </div>

        <div class="p-6 sm:p-8 text-center bg-slate-50 dark:bg-gray-800/80 border-b-2 border-dashed border-slate-200 dark:border-gray-700">
            <span class="text-xs uppercase tracking-widest font-bold text-slate-400">Nomor Antrean Anda</span>
            <div class="text-4xl sm:text-5xl font-black font-mono tracking-tight text-teal-700 dark:text-teal-400 my-2">
                {{ $queue->queue_code }}
            </div>
            <div class="flex flex-wrap items-center justify-center gap-3 text-xs mt-3">
                <span class="text-slate-500">
                    Estimasi Jam: <strong class="text-slate-900 dark:text-white font-mono font-bold">{{ $queue->estimated_time ?? substr($queue->doctorSchedule?->start_time ?? '', 0, 5) }} WIB</strong>
                </span>
                @if($queuesAhead > 0)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-200">
                        {{ $queuesAhead }} Antrean di Depan Anda
                    </span>
                @elseif($queue->status?->value === 'waiting')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-200">
                        Giliran Pertama Berikutnya
                    </span>
                @endif
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-5 text-sm">
            @php
                $rawNik = (string) ($visit?->patient?->nik ?? '');
                $maskedNik = strlen($rawNik) >= 8
                    ? substr($rawNik, 0, 4) . '••••••••' . substr($rawNik, -4)
                    : $rawNik;
            @endphp

            <div class="grid grid-cols-2 gap-4 pb-4 border-b border-slate-100 dark:border-gray-700">
                <div>
                    <span class="text-xs text-slate-400 block">Nama Pasien</span>
                    <strong class="text-slate-900 dark:text-white">{{ $visit?->patient?->name }}</strong>
                    <span class="text-xs text-teal-600 block font-mono">No. RM: {{ $visit?->patient?->medical_record_number }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">NIK (Terlindungi)</span>
                    <span class="font-mono text-slate-800 dark:text-slate-200 font-semibold">{{ $maskedNik }}</span>
                </div>
            </div>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-100 dark:border-gray-700">
                    <span class="text-slate-500">Poliklinik Tujuan:</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $visit?->polyclinic?->name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100 dark:border-gray-700">
                    <span class="text-slate-500">Dokter Spesialis:</span>
                    <span class="font-bold text-teal-700 dark:text-teal-400">{{ $visit?->doctor?->name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100 dark:border-gray-700">
                    <span class="text-slate-500">Tanggal Kunjungan:</span>
                    <span class="font-bold text-slate-900 dark:text-white">
                        {{ $visit?->visit_date ? \Carbon\Carbon::parse($visit->visit_date)->translatedFormat('l, d F Y') : '-' }}
                    </span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100 dark:border-gray-700">
                    <span class="text-slate-500">Sesi Jam Praktik:</span>
                    <span class="font-bold font-mono text-slate-900 dark:text-white">
                        {{ substr($queue->doctorSchedule?->start_time ?? '', 0, 5) }} - {{ substr($queue->doctorSchedule?->end_time ?? '', 0, 5) }} WIB
                    </span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100 dark:border-gray-700">
                    <span class="text-slate-500">Ruangan:</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $queue->doctorSchedule?->room_name ?? 'Ruang Poliklinik' }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">Metode Pembayaran:</span>
                    <span class="font-bold uppercase text-slate-900 dark:text-white">
                        {{ $visit?->payment_type === 'bpjs' ? 'BPJS Kesehatan' : ($visit?->payment_type === 'insurance' ? 'Asuransi (' . $visit?->insurance_name . ')' : 'Umum / Mandiri') }}
                    </span>
                </div>
            </div>

            <!-- QR Code Check-in Kiosk -->
            <div class="pt-4 border-t-2 border-dashed border-slate-200 dark:border-gray-700 text-center flex flex-col items-center">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">QR Code Check-in Mandiri di RS</span>
                <div class="p-3 bg-white rounded-2xl border border-slate-200 shadow-inner">
                    {!! $qrSvg !!}
                </div>
                <p class="text-[10px] text-slate-400 font-mono mt-2">Token: {{ $queue->qr_token }}</p>
                <p class="text-[11px] text-slate-500 mt-1">Scan kode ini atau masukkan Kode Kunjungan <strong>{{ $visit?->visit_number }}</strong> di Kiosk lobi RS saat tiba.</p>
            </div>
        </div>

        <div class="p-4 bg-slate-50 dark:bg-gray-800/60 border-t border-slate-100 dark:border-gray-700 text-center text-xs text-slate-500">
            Harap tiba minimal 15 menit sebelum estimasi waktu pemeriksaan.
        </div>
    </div>

    <!-- Tombol Aksi -->
    <div class="flex flex-col sm:flex-row items-center gap-3 print:hidden">
        <button onclick="window.print()"
                class="w-full sm:flex-1 py-3 px-6 rounded-2xl font-bold text-xs bg-teal-600 hover:bg-teal-700 text-white shadow-md shadow-teal-600/20 flex items-center justify-center transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak / Simpan e-Tiket
        </button>

        @if($queue->status?->value === 'waiting')
            <button wire:click="cancelQueue"
                    wire:confirm="Apakah Anda yakin ingin membatalkan antrean ini? Tindakan ini tidak dapat dibatalkan."
                    class="w-full sm:w-auto py-3 px-6 rounded-2xl font-semibold text-xs text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 border border-rose-200 dark:border-rose-800 transition">
                Batalkan Antrean
            </button>
        @endif
    </div>
</div>

