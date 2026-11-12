<?php

namespace App\Services;

use App\Enums\QueueStatus;
use App\Enums\VisitStatus;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Queue;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class QueueBookingService
{
    /**
     * Ambil daftar jadwal dokter pada tanggal tertentu beserta informasi sisa kuota real-time.
     *
     * @return Collection<int, array{
     *     id: int,
     *     start_time: string,
     *     end_time: string,
     *     room_name: string,
     *     max_quota: int,
     *     booked: int,
     *     remaining: int,
     *     is_full: bool
     * }>
     */
    public function getSchedulesWithQuota(int $doctorId, string $date): Collection
    {
        $dayOfWeek = (int) date('N', strtotime($date));

        $schedules = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        return $schedules->map(function (DoctorSchedule $schedule) use ($date): array {
            $booked = $this->countBookedForSchedule($schedule->id, $date);
            $remaining = max(0, $schedule->max_quota - $booked);

            return [
                'id' => $schedule->id,
                'start_time' => substr($schedule->start_time, 0, 5),
                'end_time' => substr($schedule->end_time, 0, 5),
                'room_name' => $schedule->room_name ?? 'Ruang Poliklinik',
                'max_quota' => $schedule->max_quota,
                'booked' => $booked,
                'remaining' => $remaining,
                'is_full' => $remaining <= 0,
            ];
        });
    }

    /**
     * Hitung jumlah antrean aktif (tidak dibatalkan) pada jadwal dan tanggal tertentu.
     */
    public function countBookedForSchedule(int $scheduleId, string $date): int
    {
        return Queue::where('doctor_schedule_id', $scheduleId)
            ->whereHas('visit', fn ($q) => $q->whereDate('visit_date', $date)->where('status', '!=', VisitStatus::Cancelled))
            ->count();
    }

    /**
     * Periksa apakah pasien sudah memiliki kunjungan aktif pada dokter dan tanggal yang sama.
     */
    public function hasActiveVisitOnDate(int $patientId, int $doctorId, string $date): bool
    {
        return Visit::where('patient_id', $patientId)
            ->where('doctor_id', $doctorId)
            ->whereDate('visit_date', $date)
            ->where('status', '!=', VisitStatus::Cancelled)
            ->exists();
    }

    /**
     * Buat kunjungan rawat jalan dan nomor antrean secara atomik.
     *
     * @param  array{
     *     payment_type: string,
     *     bpjs_card_number?: ?string,
     *     referral_letter_path?: ?string,
     *     insurance_name?: ?string,
     *     insurance_card_number?: ?string,
     *     notes?: ?string
     * }  $paymentData
     * @return array{visit: Visit, queue: Queue}
     */
    public function createOutpatientBooking(
        Patient $patient,
        DoctorSchedule $schedule,
        string $visitDate,
        array $paymentData = ['payment_type' => 'general']
    ): array {
        return DB::transaction(function () use ($patient, $schedule, $visitDate, $paymentData): array {
            $schedule->loadMissing('doctor.polyclinic');

            $bookedCount = $this->countBookedForSchedule($schedule->id, $visitDate);

            if ($bookedCount >= $schedule->max_quota) {
                throw new RuntimeException('Kuota sesi dokter ini telah habis terisi.');
            }

            $visit = Visit::create([
                'visit_number' => Visit::generateVisitNumber(),
                'patient_id' => $patient->id,
                'doctor_id' => $schedule->doctor_id,
                'polyclinic_id' => $schedule->doctor->polyclinic_id,
                'visit_date' => $visitDate,
                'visit_type' => 'outpatient',
                'payment_type' => $paymentData['payment_type'] ?? 'general',
                'bpjs_card_number' => ($paymentData['payment_type'] ?? 'general') === 'bpjs' ? ($paymentData['bpjs_card_number'] ?? null) : null,
                'referral_letter_path' => $paymentData['referral_letter_path'] ?? null,
                'insurance_name' => ($paymentData['payment_type'] ?? 'general') === 'insurance' ? ($paymentData['insurance_name'] ?? null) : null,
                'insurance_card_number' => ($paymentData['payment_type'] ?? 'general') === 'insurance' ? ($paymentData['insurance_card_number'] ?? null) : null,
                'status' => VisitStatus::Registered,
                'notes' => $paymentData['notes'] ?? null,
            ]);

            $queueNumber = $bookedCount + 1;
            $poliCode = $schedule->doctor?->polyclinic?->code ?? 'POL';
            $queueCode = sprintf('%s-%s-%03d', $poliCode, date('ymd', strtotime($visitDate)), $queueNumber);

            $duration = $schedule->doctor?->polyclinic?->consultation_duration_minutes ?? 15;
            $startTime = Carbon::createFromFormat('H:i:s', $schedule->start_time);
            $estimatedTime = $startTime->copy()->addMinutes(($queueNumber - 1) * $duration)->format('H:i');

            $queue = Queue::create([
                'visit_id' => $visit->id,
                'doctor_schedule_id' => $schedule->id,
                'queue_number' => $queueNumber,
                'queue_code' => $queueCode,
                'qr_token' => Queue::generateQrToken(),
                'estimated_time' => $estimatedTime,
                'status' => QueueStatus::Waiting,
            ]);

            return [
                'visit' => $visit,
                'queue' => $queue,
            ];
        });
    }
}

