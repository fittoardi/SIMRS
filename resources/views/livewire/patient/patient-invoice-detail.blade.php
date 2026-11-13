<div class="space-y-6 max-w-3xl mx-auto">
    <!-- Back Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('patient.invoices') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 flex items-center gap-1">
            ← Kembali ke Daftar Tagihan
        </a>
        <span class="text-xs text-slate-400">SIMRS RS Aisyah Medika</span>
    </div>

    <!-- Flash Notifications -->
    @if (session()->has('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Kartu Rincian Tagihan -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-slate-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <!-- Header RS -->
        <div class="bg-gradient-to-r from-teal-700 via-teal-800 to-emerald-800 text-white p-6 sm:p-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs uppercase tracking-wider text-teal-200 font-bold block mb-1">Tagihan Layanan Pasien</span>
                <h3 class="text-2xl font-black font-mono tracking-tight">#{{ $invoice->number }}</h3>
                <p class="text-xs text-teal-100 mt-1">Diterbitkan: {{ $invoice->created_at->format('d F Y, H:i') }} WIB</p>
            </div>

            <div>
                @if($invoice->status->value === 'paid')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-extrabold bg-emerald-500 text-white shadow-md shadow-emerald-900/30">
                        ✓ LUNAS
                    </span>
                @else
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-extrabold bg-amber-500 text-white shadow-md shadow-amber-900/30 animate-pulse">
                        ⏳ MENUNGGU PEMBAYARAN
                    </span>
                @endif
            </div>
        </div>

        <!-- Identitas Pasien & Pemeriksaan -->
        <div class="p-6 sm:p-7 border-b border-slate-100 dark:border-gray-700 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block mb-0.5">Nama Pasien:</span>
                <strong class="text-sm font-bold text-slate-900 dark:text-white block">{{ $invoice->patient?->name }}</strong>
                <span class="text-teal-600 font-mono">No. RM: {{ $invoice->patient?->medical_record_number }}</span>
            </div>
            <div>
                <span class="text-slate-400 block mb-0.5">Poliklinik & Dokter:</span>
                <strong class="text-sm font-bold text-slate-900 dark:text-white block">
                    {{ $invoice->visit?->polyclinic?->name ?? 'Poliklinik' }}
                </strong>
                <span class="text-slate-500">{{ $invoice->visit?->doctor?->name ?? '-' }}</span>
            </div>
        </div>

        <!-- Rincian Item Tagihan -->
        <div class="p-6 sm:p-7 space-y-4">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Rincian Tindakan & Obat</h4>

            <div class="space-y-3">
                @forelse($invoice->items as $item)
                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 dark:bg-gray-700/50 border border-slate-100 dark:border-gray-700 text-xs">
                        <div class="space-y-0.5 flex-1 pr-3">
                            <span class="font-bold text-slate-900 dark:text-white block">{{ $item->name_snapshot }}</span>
                            <span class="text-slate-400">
                                {{ $item->qty }}x @ Rp {{ number_format((float) $item->price_snapshot, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-slate-900 dark:text-white font-mono">
                                Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic">Tidak ada rincian item tercatat.</p>
                @endforelse
            </div>

            <!-- Subtotal & Diskon -->
            <div class="pt-4 border-t border-slate-200 dark:border-gray-700 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600 dark:text-slate-400">
                    <span>Subtotal Biaya</span>
                    <span>Rp {{ number_format((float) $invoice->subtotal, 0, ',', '.') }}</span>
                </div>

                @if((float) $invoice->discount > 0)
                    <div class="flex justify-between text-rose-600 font-semibold">
                        <span>Potongan / Diskon</span>
                        <span>- Rp {{ number_format((float) $invoice->discount, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="flex justify-between items-center pt-2 border-t border-slate-100 dark:border-gray-700">
                    <span class="text-sm font-bold text-slate-900 dark:text-white">TOTAL TAGIHAN</span>
                    <span class="text-xl font-extrabold text-teal-700 dark:text-teal-400 font-mono">
                        Rp {{ number_format((float) $invoice->total, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel Status Lunas / Pembayaran Online -->
    @if($invoice->status->value === 'paid')
        <div class="bg-emerald-50 dark:bg-emerald-950/30 rounded-3xl p-6 sm:p-7 border border-emerald-200 dark:border-emerald-800 text-center space-y-4">
            <div class="w-12 h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center mx-auto shadow-md">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-lg text-emerald-900 dark:text-emerald-200">Pembayaran Telah Selesai</h4>
                <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-1">
                    Tagihan ini telah dibayar lunas pada {{ $latestPayment?->paid_at ? $latestPayment->paid_at->format('d M Y, H:i') : $invoice->updated_at->format('d M Y, H:i') }} WIB.
                </p>
            </div>
            <div>
                <a href="{{ route('billing.kwitansi', $invoice->id) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak Kwitansi Pembayaran Resmi</span>
                </a>
            </div>
        </div>
    @else
        <!-- Panel Pembayaran Online Mandiri -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-7 border border-slate-200 dark:border-gray-700 shadow-sm space-y-6">
            <div class="border-b border-slate-100 dark:border-gray-700 pb-3">
                <h4 class="text-base font-bold text-slate-900 dark:text-white">Pilih Metode Pembayaran Online</h4>
                <p class="text-xs text-slate-500 mt-0.5">Selesaikan pembayaran langsung melalui smartphone Anda</p>
            </div>

            <!-- Pilihan Kanal -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <button type="button" wire:click="$set('selectedMethod', 'qris')"
                        class="p-4 rounded-2xl border-2 text-center transition
                            {{ $selectedMethod === 'qris' ? 'border-teal-600 bg-teal-50 dark:bg-teal-900/30' : 'border-slate-200 dark:border-gray-700 hover:border-teal-300' }}">
                    <span class="font-extrabold text-sm block text-slate-900 dark:text-white">QRIS</span>
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Semua E-Wallet</span>
                </button>

                <button type="button" wire:click="$set('selectedMethod', 'bca_va')"
                        class="p-4 rounded-2xl border-2 text-center transition
                            {{ $selectedMethod === 'bca_va' ? 'border-teal-600 bg-teal-50 dark:bg-teal-900/30' : 'border-slate-200 dark:border-gray-700 hover:border-teal-300' }}">
                    <span class="font-extrabold text-sm block text-slate-900 dark:text-white">BCA VA</span>
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Virtual Account</span>
                </button>

                <button type="button" wire:click="$set('selectedMethod', 'bni_va')"
                        class="p-4 rounded-2xl border-2 text-center transition
                            {{ $selectedMethod === 'bni_va' ? 'border-teal-600 bg-teal-50 dark:bg-teal-900/30' : 'border-slate-200 dark:border-gray-700 hover:border-teal-300' }}">
                    <span class="font-extrabold text-sm block text-slate-900 dark:text-white">BNI VA</span>
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Virtual Account</span>
                </button>

                <button type="button" wire:click="$set('selectedMethod', 'mandiri_va')"
                        class="p-4 rounded-2xl border-2 text-center transition
                            {{ $selectedMethod === 'mandiri_va' ? 'border-teal-600 bg-teal-50 dark:bg-teal-900/30' : 'border-slate-200 dark:border-gray-700 hover:border-teal-300' }}">
                    <span class="font-extrabold text-sm block text-slate-900 dark:text-white">Mandiri VA</span>
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Virtual Account</span>
                </button>
            </div>

            <!-- Visual Kanal QRIS / VA -->
            @if($selectedMethod === 'qris')
                <div class="text-center p-6 bg-slate-50 dark:bg-gray-700/40 rounded-2xl border border-slate-200 dark:border-gray-700 space-y-3">
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider block">
                        Scan QRIS dengan Aplikasi Apapun
                    </span>
                    <div class="inline-block p-3 bg-white rounded-2xl border border-slate-200 shadow-inner">
                        {!! $qrisSvg !!}
                    </div>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">
                        Mendukung BCA Mobile, Livin Mandiri, GoPay, OVO, Dana, ShopeePay, dan LinkAja.
                    </p>
                </div>
            @else
                <div class="p-5 bg-slate-50 dark:bg-gray-700/40 rounded-2xl border border-slate-200 dark:border-gray-700 space-y-2 text-xs">
                    <span class="text-slate-400 block">Nomor Rekening Virtual Account:</span>
                    <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-xl border border-slate-200 dark:border-gray-700 font-mono text-base font-bold text-slate-900 dark:text-white">
                        <span>88019{{ str_pad((string) $invoice->id, 8, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-xs text-teal-600 font-sans font-semibold">Salin</span>
                    </div>
                    <p class="text-[11px] text-slate-500">
                        Transfer persis sesuai total tagihan: <strong>Rp {{ number_format((float) $invoice->total, 0, ',', '.') }}</strong>
                    </p>
                </div>
            @endif

            <!-- Tombol Konfirmasi Bayar Online -->
            <div>
                <button type="button" wire:click="payNow"
                        class="w-full py-3.5 px-6 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 shadow-lg shadow-teal-600/25 transition">
                    Konfirmasi Bayar Online (Rp {{ number_format((float) $invoice->total, 0, ',', '.') }})
                </button>
                <p class="text-[11px] text-slate-400 text-center mt-2">
                    Atau tunjukkan No. Tagihan <strong>#{{ $invoice->number }}</strong> ke petugas Loket Kasir RS Aisyah Medika untuk pembayaran tunai.
                </p>
            </div>
        </div>
    @endif
</div>

