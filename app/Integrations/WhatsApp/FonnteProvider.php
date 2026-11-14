<?php

namespace App\Integrations\WhatsApp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FonnteProvider implements WhatsAppProvider
{
    public function __construct(
        protected string $token,
        protected string $endpoint = 'https://api.fonnte.com/send'
    ) {}

    /**
     * Kirim pesan WhatsApp melalui Fonnte Gateway API.
     */
    public function sendMessage(string $targetPhone, string $message): array
    {
        $normalizedPhone = $this->normalizePhone($targetPhone);

        if (empty($this->token)) {
            Log::warning("Fonnte token is not configured. Falling back to log driver for {$normalizedPhone}.");

            return [
                'success' => false,
                'message' => 'Fonnte token is missing.',
                'response' => null,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->timeout(10)->post($this->endpoint, [
                'target' => $normalizedPhone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            $json = $response->json();
            $status = $response->successful() && ($json['status'] ?? false);

            return [
                'success' => $status,
                'message' => $json['reason'] ?? ($status ? 'Message delivered to WhatsApp Gateway.' : 'Failed to deliver message.'),
                'response' => $json,
            ];
        } catch (Throwable $e) {
            Log::error('Fonnte WhatsApp API Exception', [
                'phone' => $normalizedPhone,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'response' => null,
            ];
        }
    }

    /**
     * Normalisasi nomor telepon lokal Indonesia ke format standar E.164 (628xxx).
     */
    public function normalizePhone(string $phone): string
    {
        $clean = preg_replace('/\D/', '', $phone) ?? '';

        if (str_starts_with($clean, '0')) {
            return '62'.substr($clean, 1);
        }

        if (str_starts_with($clean, '8')) {
            return '62'.$clean;
        }

        return $clean;
    }
}
