<?php

namespace App\Console\Commands;

use App\Enums\QueueStatus;
use App\Enums\VisitStatus;
use App\Models\Queue;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendVisitReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hospital:send-reminders
                            {--date= : Tanggal kunjungan yang akan diproses (format YYYY-MM-DD, default: besok)}
                            {--dry-run : Tampilkan daftar pasien antrean tanpa mengirim pesan WhatsApp}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pesan pengingat jadwal kunjungan H-1 via WhatsApp kepada pasien terdaftar';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppNotificationService $waService): int
    {
        $targetDate = $this->option('date') ?: Carbon::tomorrow()->toDateString();
        $isDryRun = (bool) $this->option('dry-run');

        $this->info("=== PENGINGAT JADWAL KUNJUNGAN PASIEN (H-1) ===");
        $this->info("Target Tanggal Kunjungan: {$targetDate}");

        if ($isDryRun) {
            $this->warn("MODE DRY-RUN: Pesan WhatsApp tidak akan dikirim secara riil.");
        }

        // Ambil semua antrean kunjungan di tanggal target yang belum dikirimi reminder dan tidak dibatalkan
        $queues = Queue::with([
            'visit.patient.user',
            'visit.doctor.polyclinic',
            'visit.polyclinic',
            'doctorSchedule',
        ])
            ->whereNull('reminder_sent_at')
            ->whereHas('visit', function ($query) use ($targetDate) {
                $query->whereDate('visit_date', $targetDate)
                    ->whereNotIn('status', [VisitStatus::Cancelled, 'cancelled']);
            })
            ->whereNotIn('status', [QueueStatus::Cancelled, 'cancelled'])
            ->orderBy('id', 'asc')
            ->get();

        if ($queues->isEmpty()) {
            $this->comment("Tidak ada antrean kunjungan untuk tanggal {$targetDate} yang memerlukan pengingat.");

            return self::SUCCESS;
        }

        $this->info("Ditemukan {$queues->count()} antrean kunjungan untuk tanggal {$targetDate}.");

        $sentCount = 0;
        $failedCount = 0;
        $skippedCount = 0;
        $tableRows = [];

        foreach ($queues as $queue) {
            $patient = $queue->visit?->patient;
            $phone = $patient?->phone ?: $patient?->user?->phone;
            $queueCode = $queue->queue_code;
            $poly = $queue->visit?->polyclinic?->name ?? '-';

            if (empty($phone)) {
                $skippedCount++;
                $tableRows[] = [
                    $queueCode,
                    $patient?->name ?? '-',
                    'TIDAK ADA NOMOR',
                    $poly,
                    'Dilewati (No Phone)',
                ];
                continue;
            }

            if ($isDryRun) {
                $sentCount++;
                $tableRows[] = [
                    $queueCode,
                    $patient?->name ?? '-',
                    $phone,
                    $poly,
                    'Siap Kirim (Dry-Run)',
                ];
                continue;
            }

            $success = $waService->sendVisitReminder($queue);

            if ($success) {
                $queue->update([
                    'reminder_sent_at' => now(),
                ]);
                $sentCount++;
                $statusText = 'Terkirim ✓';
            } else {
                $failedCount++;
                $statusText = 'Gagal ✗';
            }

            $tableRows[] = [
                $queueCode,
                $patient?->name ?? '-',
                $phone,
                $poly,
                $statusText,
            ];
        }

        $this->table(
            ['No. Antrean', 'Nama Pasien', 'No. WhatsApp', 'Poliklinik', 'Status'],
            $tableRows
        );

        $this->info("Selesai diproses. Terkirim: {$sentCount}, Gagal: {$failedCount}, Dilewati: {$skippedCount}.");

        return self::SUCCESS;
    }
}
