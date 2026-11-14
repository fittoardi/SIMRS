<?php

namespace App\Integrations\WhatsApp;

use Illuminate\Support\Facades\Log;

class LogWhatsAppProvider implements WhatsAppProvider
{
    /**
     * Catat pengiriman pesan WhatsApp ke dalam log aplikasi (untuk lingkungan development/testing).
     */
    public function sendMessage(string $targetPhone, string $message): array
    {
        Log::info('WhatsApp Notification Dispatched [LOG DRIVER]', [
            'to' => $targetPhone,
            'message' => $message,
        ]);

        return [
            'success' => true,
            'message' => 'WhatsApp message logged successfully via LogWhatsAppProvider.',
            'response' => null,
        ];
    }
}
