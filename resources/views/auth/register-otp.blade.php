<x-guest-layout maxWidth="max-w-xl" :card="false">
    <x-slot name="title">Verifikasi WhatsApp OTP — RS Aisyah Medika</x-slot>

    <div class="rounded-3xl overflow-hidden shadow-2xl border border-white/40 dark:border-slate-700/60 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl p-8 sm:p-10">

        <!-- Header Icon & Title -->
        <div class="text-center space-y-3">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-tr from-teal-600 to-emerald-500 text-white flex items-center justify-center shadow-lg shadow-teal-500/30">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>

            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Verifikasi Nomor WhatsApp
            </h2>

            <p class="text-sm text-slate-600 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                Kami telah mengirimkan 6 digit kode OTP rahasia ke nomor WhatsApp Anda:
            </p>

            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-teal-50 dark:bg-teal-950/50 border border-teal-200 dark:border-teal-800 text-teal-800 dark:text-teal-300 font-mono font-bold text-sm">
                <span>💬</span>
                <span>{{ $maskedPhone }}</span>
            </div>
        </div>

        <!-- Alert Notification Messages -->
        @if (session('success'))
            <div class="mt-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('warning'))
            <div class="mt-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-xs flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        <!-- OTP Verification Form -->
        <form method="POST" action="{{ route('register.otp.verify') }}" class="mt-8 space-y-6">
            @csrf

            <div>
                <label for="otp_code" class="block text-center text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
                    Masukkan 6 Digit Kode OTP
                </label>

                <div class="relative max-w-xs mx-auto">
                    <input
                        id="otp_code"
                        type="text"
                        name="otp_code"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="6"
                        autocomplete="one-time-code"
                        autofocus
                        required
                        placeholder="••••••"
                        class="w-full text-center text-3xl font-mono font-extrabold tracking-[0.5em] px-4 py-3.5 rounded-2xl border-2 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:border-teal-500 focus:ring-4 focus:ring-teal-500/20 transition shadow-inner"
                    />
                </div>

                <x-input-error :messages="$errors->get('otp_code')" class="mt-2 text-center" />

                <p class="text-center text-xs text-slate-500 dark:text-slate-400 mt-2">
                    ⏱️ Kode berlaku selama 5 menit. Jangan berikan kode ini kepada siapa pun.
                </p>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 text-white font-bold text-sm shadow-xl shadow-teal-600/30 hover:shadow-teal-600/40 transform active:scale-[0.99] transition duration-200"
            >
                Verifikasi & Selesaikan Pendaftaran ✓
            </button>
        </form>

        <!-- Resend OTP & Change Info Action Footer -->
        <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 dark:text-slate-400">
            <!-- Resend Form -->
            <form method="POST" action="{{ route('register.otp.resend') }}" id="resend-form">
                @csrf
                <button
                    type="submit"
                    id="resend-btn"
                    class="font-semibold text-teal-600 dark:text-teal-400 hover:underline flex items-center gap-1 transition"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span id="resend-text">Kirim Ulang Kode OTP</span>
                </button>
            </form>

            <!-- Back to Register -->
            <a href="{{ route('register') }}" class="hover:text-slate-700 dark:hover:text-slate-200 transition">
                ← Ubah Data Pendaftaran
            </a>
        </div>
    </div>

    <!-- Countdown Script for Resend Button -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const resendBtn = document.getElementById('resend-btn');
            const resendText = document.getElementById('resend-text');
            let cooldown = 60;

            function startTimer() {
                resendBtn.disabled = true;
                resendBtn.classList.add('opacity-50', 'cursor-not-allowed');

                const interval = setInterval(function () {
                    if (cooldown <= 0) {
                        clearInterval(interval);
                        resendBtn.disabled = false;
                        resendBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        resendText.textContent = 'Kirim Ulang Kode OTP';
                    } else {
                        resendText.textContent = `Kirim Ulang OTP (${cooldown}s)`;
                        cooldown--;
                    }
                }, 1000);
            }

            startTimer();
        });
    </script>
</x-guest-layout>
