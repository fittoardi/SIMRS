<?php

namespace App\Services;

use App\Integrations\WhatsApp\WhatsAppProvider;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\Queue;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class WhatsAppNotificationService
{
    public function __construct(
        protected WhatsAppProvider $provider
    ) {}

    /**
     * Kirim konfirmasi pendaftaran antrean (Booking Online & Guest Booking).
     */
    public function sendBookingConfirmation(Visit $visit, Queue $queue): bool
    {
        $visit->loadMissing(['patient.user', 'doctor.polyclinic', 'polyclinic']);
        $queue->loadMissing(['doctorSchedule']);

        $patient = $visit->patient;
        $targetPhone = $patient?->phone ?: $patient?->user?->phone;

        if (empty($targetPhone)) {
            Log::info("WA Booking skipped: No phone number for patient #{$patient?->id}");

            return false;
        }

        $dateFormatted = $visit->visit_date
            ? Carbon::parse($visit->visit_date)->translatedFormat('l, d F Y')
            : date('d F Y');

        $ticketUrl = route('public.ticket', ['token' => $queue->qr_token]);
        $roomName = $queue->doctorSchedule?->room_name ?? 'Ruang Poliklinik';
        $estTime = $queue->estimated_time ?? substr($queue->doctorSchedule?->start_time ?? '', 0, 5).' WIB';

        $message = "🏥 *RS AISYAH MEDIKA — BUKTI PENDAFTARAN ANTREAN*\n\n"
            ."Yth. Bapak/Ibu *{$patient?->name}*,\n"
            ."Pendaftaran antrean rawat jalan Anda telah berhasil tercatat di sistem kami.\n\n"
            ."📋 *Nomor Antrean:* *{$queue->queue_code}*\n"
            ."🗓️ *Tanggal:* {$dateFormatted}\n"
            ."⏰ *Estimasi Pelayanan:* {$estTime}\n"
            ."🩺 *Poliklinik Tujuan:* {$visit->polyclinic?->name}\n"
            ."👨‍⚕️ *Dokter Spesialis:* {$visit->doctor?->name}\n"
            ."🚪 *Ruangan:* {$roomName}\n"
            ."🆔 *No. RM Pasien:* {$patient?->medical_record_number}\n\n"
            ."📲 *Tautan E-Tiket & QR Code Check-in:*\n"
            ."{$ticketUrl}\n\n"
            ."_Mohon tiba 15 menit sebelum estimasi waktu dan lakukan check-in mandiri di Kiosk lobi RS._\n"
            .'Bantuan & Hotline: (021) 8765-4321';

        try {
            $result = $this->provider->sendMessage($targetPhone, $message);

            return $result['success'] ?? true;
        } catch (Throwable $e) {
            Log::error('WA sendBookingConfirmation failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Kirim notifikasi panggilan antrean saat dipanggil perawat/dokter.
     */
    public function sendQueueCalled(Queue $queue): bool
    {
        $queue->loadMissing(['visit.patient', 'visit.doctor.polyclinic', 'doctorSchedule']);

        $visit = $queue->visit;
        $patient = $visit?->patient;
        $targetPhone = $patient?->phone ?: $patient?->user?->phone;

        if (empty($targetPhone)) {
            return false;
        }

        $roomName = $queue->doctorSchedule?->room_name ?? 'Ruang Pemeriksaan Poliklinik';
        $doctorName = $visit?->doctor?->name ?? 'Dokter Pemeriksa';

        $message = "🔔 *PANGGILAN ANTREAN — RS AISYAH MEDIKA*\n\n"
            ."Yth. Bapak/Ibu *{$patient?->name}*,\n"
            ."Nomor antrean Anda *{$queue->queue_code}* saat ini *TELAH DIPANGGIL*.\n\n"
            ."📍 *Silakan menuju ke:* *{$roomName}*\n"
            ."👨‍⚕️ *Dokter:* {$doctorName}\n"
            ."🩺 *Layanan:* {$visit?->polyclinic?->name}\n\n"
            .'Mohon segera masuk ke ruangan poliklinik untuk memulai pemeriksaan medis. Terima kasih.';

        try {
            $result = $this->provider->sendMessage($targetPhone, $message);

            return $result['success'] ?? true;
        } catch (Throwable $e) {
            Log::error('WA sendQueueCalled failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Kirim notifikasi bahwa racikan resep obat telah selesai dan siap diambil di farmasi/kasir.
     */
    public function sendPrescriptionReady(Prescription $prescription): bool
    {
        $prescription->loadMissing(['visit.patient', 'doctor']);

        $patient = $prescription->visit?->patient;
        $targetPhone = $patient?->phone ?: $patient?->user?->phone;

        if (empty($targetPhone)) {
            return false;
        }

        $rxNumber = $prescription->prescription_number ?? ('RX-'.str_pad((string) $prescription->id, 4, '0', STR_PAD_LEFT));

        $message = "💊 *FARMASI RS AISYAH MEDIKA — RESEP OBAT SELESAI*\n\n"
            ."Yth. Bapak/Ibu *{$patient?->name}*,\n"
            ."Resep obat Anda (No. Resep: *{$rxNumber}*) telah selesai diracik dan disiapkan oleh tim instalasi farmasi.\n\n"
            ."📍 *Lokasi Pengambilan:* Loket Farmasi & Kasir RS Aisyah Medika\n"
            ."👨‍⚕️ *Dokter Peresep:* {$prescription->doctor?->name}\n\n"
            ."Silakan menuju ke loket farmasi untuk serah terima obat dan penjelasan aturan minum obat dari apoteker kami.\n"
            .'_Semoga lekas sembuh!_';

        try {
            $result = $this->provider->sendMessage($targetPhone, $message);

            return $result['success'] ?? true;
        } catch (Throwable $e) {
            Log::error('WA sendPrescriptionReady failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Kirim bukti kwitansi resmi pembayaran tagihan kasir/online.
     */
    public function sendPaymentSuccess(Invoice $invoice, ?Payment $payment = null): bool
    {
        $invoice->loadMissing(['patient', 'visit.polyclinic', 'visit.doctor']);

        $patient = $invoice->patient;
        $targetPhone = $patient?->phone ?: $patient?->user?->phone;

        if (empty($targetPhone)) {
            return false;
        }

        $formattedTotal = 'Rp '.number_format((float) $invoice->total, 0, ',', '.');
        $methodLabel = strtoupper($payment?->method ?? 'PEMBAYARAN ONLINE');
        $receiptUrl = route('billing.kwitansi', $invoice->id);

        $message = "💳 *BUKTI PEMBAYARAN LUNAS — RS AISYAH MEDIKA*\n\n"
            ."Yth. Bapak/Ibu *{$patient?->name}*,\n"
            ."Pembayaran tagihan layanan rawat jalan Anda telah berhasil kami terima dan dinyatakan *LUNAS*.\n\n"
            ."🧾 *Nomor Tagihan:* *{$invoice->number}*\n"
            ."💰 *Total Pembayaran:* *{$formattedTotal}*\n"
            ."💳 *Kanal Pembayaran:* {$methodLabel}\n"
            ."🩺 *Layanan:* {$invoice->visit?->polyclinic?->name} ({$invoice->visit?->doctor?->name})\n"
            .'⏰ *Waktu:* '.date('d M Y, H:i')." WIB\n\n"
            ."📄 *Unduh / Buka Kwitansi Resmi:* \n"
            ."{$receiptUrl}\n\n"
            .'Terima kasih atas kepercayaan Anda kepada RS Aisyah Medika.';

        try {
            $result = $this->provider->sendMessage($targetPhone, $message);

            return $result['success'] ?? true;
        } catch (Throwable $e) {
            Log::error('WA sendPaymentSuccess failed: '.$e->getMessage());

            return false;
        }
    }
}
