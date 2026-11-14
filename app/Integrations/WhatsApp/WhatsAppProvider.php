<?php

namespace App\Integrations\WhatsApp;

interface WhatsAppProvider
{
    /**
     * Kirim pesan WhatsApp ke nomor tujuan.
     *
     * @param  string  $targetPhone  Nomor tujuan (format lokal 08xxx atau internasional 628xxx)
     * @param  string  $message  Teks pesan
     * @return array{success: bool, message: string, response: mixed}
     */
    public function sendMessage(string $targetPhone, string $message): array;
}
