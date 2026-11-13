<div class="space-y-6">
    <!-- Filter Status Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        <button wire:click="$set('statusFilter', 'all')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5
                    {{ $statusFilter === 'all' ? 'bg-teal-600 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 border border-slate-200 dark:border-gray-700' }}">
            <span>Semua Tagihan</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $statusFilter === 'all' ? 'bg-teal-700 text-white' : 'bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-slate-300' }}">
                {{ $unpaidCount + $paidCount }}
            </span>
        </button>

        <button wire:click="$set('statusFilter', 'unpaid')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5
                    {{ $statusFilter === 'unpaid' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-amber-700 dark:text-amber-400 hover:bg-amber-50 border border-amber-200 dark:border-amber-900/50' }}">
            <span>Menunggu Pembayaran</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $statusFilter === 'unpaid' ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-800' }}">
                {{ $unpaidCount }}
            </span>
        </button>

        <button wire:click="$set('statusFilter', 'paid')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5
                    {{ $statusFilter === 'paid' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 border border-emerald-200 dark:border-emerald-900/50' }}">
            <span>Lunas</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $statusFilter === 'paid' ? 'bg-emerald-700 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                {{ $paidCount }}
            </span>
        </button>
    </div>

    <!-- Daftar Tagihan: Adaptif Desktop Table & Mobile Stacked Cards -->
    <div class="space-y-4">
        @forelse($invoices as $inv)
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-5 sm:p-6 border border-slate-200 dark:border-gray-700 shadow-sm hover:border-teal-300 transition flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="space-y-2 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono font-bold text-sm text-teal-700 dark:text-teal-400">
                            #{{ $inv->number }}
                        </span>
                        <span class="text-xs text-slate-400">•</span>
                        <span class="text-xs text-slate-500">
                            {{ $inv->created_at->format('d M Y, H:i') }} WIB
                        </span>
                        @if($inv->status->value === 'paid')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                ✓ LUNAS
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                ⏳ MENUNGGU BAYAR
                            </span>
                        @endif
                    </div>

                    <div>
                        <h4 class="font-bold text-base text-slate-900 dark:text-white">
                            {{ $inv->patient?->name }}
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            No. RM: <strong class="font-mono">{{ $inv->patient?->medical_record_number }}</strong> — {{ $inv->visit?->polyclinic?->name }} ({{ $inv->visit?->doctor?->name }})
                        </p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row md:flex-col lg:flex-row items-start sm:items-center md:items-end lg:items-center gap-3 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-gray-700">
                    <div class="text-left md:text-right">
                        <span class="text-[11px] text-slate-400 block">Total Tagihan</span>
                        <span class="text-lg font-extrabold text-slate-900 dark:text-white">
                            Rp {{ number_format((float) $inv->total, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <a href="{{ route('patient.invoice-detail', $inv->id) }}"
                           class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-bold text-white transition
                                {{ $inv->status->value === 'paid' ? 'bg-slate-700 hover:bg-slate-800' : 'bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20' }}">
                            {{ $inv->status->value === 'paid' ? 'Lihat Rincian' : 'Bayar Sekarang →' }}
                        </a>

                        @if($inv->status->value === 'paid')
                            <a href="{{ route('billing.kwitansi', $inv->id) }}" target="_blank"
                               class="inline-flex items-center justify-center p-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 transition"
                               title="Cetak Kwitansi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-10 border border-slate-200 dark:border-gray-700 text-center space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 flex items-center justify-center mx-auto">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h4 class="font-bold text-base text-slate-900 dark:text-white">Belum Ada Tagihan</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    Tagihan biaya pendaftaran, tindakan medis, atau obat farmasi dari pemeriksaan Anda akan muncul di sini.
                </p>
            </div>
        @endforelse
    </div>
</div>

