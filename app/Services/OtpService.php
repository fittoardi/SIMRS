<?php

namespace App\Services;

use App\Integrations\WhatsApp\WhatsAppProvider;
use App\Models\OtpVerification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

class OtpService
{
    public const int OTP_LENGTH = 6;

    public const int EXPIRY_MINUTES = 5;

    public const int RESEND_COOLDOWN_SECONDS = 60;

    public const int MAX_ATTEMPTS = 5;

    public function __construct(
        protected WhatsAppProvider $waProvider
    ) {}

    /**
     * Kirim kode OTP 6-digit ke nomor WhatsApp pengguna.
     *
     * @return array{success: bool, message: string, cooldown: int}
     */
    public function sendOtp(string $phone, string $action = 'register'): array
    {
        $normalizedPhone = $this->normalizePhone($phone);

        if (empty($normalizedPhone) || strlen($normalizedPhone) < 9) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp tidak valid.',
                'cooldown' => 0,
            ];
        }

        // Cek cooldown pengiriman ulang (anti-spam 60 detik)
        $latestOtp = OtpVerification::where('phone', $normalizedPhone)
            ->where('action', $action)
            ->latest('id')
            ->first();

        if ($latestOtp && $latestOtp->created_at) {
            $secondsSinceCreation = Carbon::now()->diffInSeconds($latestOtp->created_at);
            if ($secondsSinceCreation < self::RESEND_COOLDOWN_SECONDS) {
                $remaining = self::RESEND_COOLDOWN_SECONDS - $secondsSinceCreation;

                return [
                    'success' => false,
                    'message' => "Mohon tunggu {$remaining} detik sebelum meminta kode OTP baru.",
                    'cooldown' => $remaining,
                ];
            }
        }

        // Generate 6 digit angka aman
        $otpCode = str_pad((string) random_int(100000, 999999), self::OTP_LENGTH, '0', STR_PAD_LEFT);

        // Bersihkan OTP yang belum diverifikasi sebelumnya
        OtpVerification::where('phone', $normalizedPhone)
            ->where('action', $action)
            ->whereNull('verified_at')
            ->delete();

        // Simpan data OTP dengan hash aman
        OtpVerification::create([
            'phone' => $normalizedPhone,
            'otp_hash' => Hash::make($otpCode),
            'action' => $action,
            'attempts' => 0,
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
        ]);

        $actionLabel = match ($action) {
            'register' => 'Pendaftaran Akun Pasien Baru',
            'guest_booking' => 'Booking Antrean Rawat Jalan',
            'login' => 'Masuk Akun Pasien',
            default => 'Verifikasi Akun',
        };

        $message = "🏥 *RS AISYAH MEDIKA — KODE VERIFIKASI OTP*\n\n"
            . "Yth. Pasien / Calon Pasien,\n"
            . "Kode OTP untuk *{$actionLabel}* Anda adalah:\n\n"
            . "🔐 *{$otpCode}*\n\n"
            . "⏳ Berlaku selama *5 menit*.\n"
            . "⚠️ _PENTING: Jangan berikan kode ini kepada siapa pun demi keamanan data rekam medis Anda._\n"
            . "Bantuan & Hotline RS: (021) 8765-4321";

        try {
            $result = $this->waProvider->sendMessage($normalizedPhone, $message);

            return [
                'success' => $result['success'] ?? true,
                'message' => 'Kode OTP telah berhasil dikirimkan ke WhatsApp Anda.',
                'cooldown' => self::RESEND_COOLDOWN_SECONDS,
            ];
        } catch (Throwable $e) {
            Log::error('Gagal mengirim WhatsApp OTP: ' . $e->getMessage(), [
                'phone' => $normalizedPhone,
                'action' => $action,
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kendala saat mengirimkan pesan WhatsApp. Pastikan nomor aktif.',
                'cooldown' => 0,
            ];
        }
    }

    /**
     * Verifikasi kode OTP yang dimasukkan pengguna.
     *
     * @return array{valid: bool, message: string}
     */
    public function verifyOtp(string $phone, string $code, string $action = 'register'): array
    {
        $normalizedPhone = $this->normalizePhone($phone);
        $cleanCode = preg_replace('/\D/', '', $code) ?? '';

        if (strlen($cleanCode) !== self::OTP_LENGTH) {
            return [
                'valid' => false,
                'message' => 'Kode OTP harus berupa 6 digit angka.',
            ];
        }

        $otpRecord = OtpVerification::where('phone', $normalizedPhone)
            ->where('action', $action)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        if (! $otpRecord) {
            return [
                'valid' => false,
                'message' => 'Kode OTP tidak ditemukan atau telah kadaluarsa. Silakan minta kode baru.',
            ];
        }

        if ($otpRecord->attempts >= self::MAX_ATTEMPTS) {
            $otpRecord->delete();

            return [
                'valid' => false,
                'message' => 'Batas percobaan verifikasi telah habis. Silakan kirim ulang kode baru.',
            ];
        }

        if (! Hash::check($cleanCode, $otpRecord->otp_hash)) {
            $otpRecord->increment('attempts');
            $remaining = self::MAX_ATTEMPTS - $otpRecord->attempts;

            return [
                'valid' => false,
                'message' => "Kode OTP salah. Sisa kesempatan: {$remaining} kali.",
            ];
        }

        // Tandai terverifikasi
        $otpRecord->update([
            'verified_at' => now(),
        ]);

        return [
            'valid' => true,
            'message' => 'Nomor WhatsApp berhasil diverifikasi.',
        ];
    }

    /**
     * Cek apakah nomor telepon telah terverifikasi untuk aksi tertentu dalam kurun waktu validitas.
     */
    public function isRecentlyVerified(string $phone, string $action = 'register', int $validMinutes = 15): bool
    {
        $normalizedPhone = $this->normalizePhone($phone);

        return OtpVerification::where('phone', $normalizedPhone)
            ->where('action', $action)
            ->whereNotNull('verified_at')
            ->where('verified_at', '>=', now()->subMinutes($validMinutes))
            ->exists();
    }

    /**
     * Normalisasi nomor telepon ke format E.164 Indonesia (628xxx).
     */
    public function normalizePhone(string $phone): string
    {
        $clean = preg_replace('/\D/', '', $phone) ?? '';

        if (str_starts_with($clean, '0')) {
            return '62' . substr($clean, 1);
        }

        if (str_starts_with($clean, '8')) {
            return '62' . $clean;
        }

        return $clean;
    }
}
