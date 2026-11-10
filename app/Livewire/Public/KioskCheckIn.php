<?php

namespace App\Livewire\Public;

use App\Enums\QueueStatus;
use App\Models\Queue;
use Carbon\Carbon;
use Livewire\Component;

class KioskCheckIn extends Component
{
    public string $mode = 'code'; // code | nik

    public string $inputCode = '';

    public string $inputNik = '';

    public ?Queue $checkedInQueue = null;

    public ?string $errorMessage = null;

    public function switchMode(string $mode): void
    {
        $this->mode = $mode;
        $this->errorMessage = null;
    }

    public function checkInByCode(): void
    {
        $this->errorMessage = null;
        $this->checkedInQueue = null;

        $code = strtoupper(trim($this->inputCode));
        if (empty($code)) {
            $this->errorMessage = 'Silakan masukkan nomor antrean atau kode tiket Anda.';

            return;
        }

        $today = Carbon::today()->format('Y-m-d');

        $queue = Queue::with(['visit.patient', 'visit.doctor.polyclinic', 'visit.polyclinic', 'doctorSchedule'])
            ->where(function ($q) use ($code) {
                $q->where('queue_code', $code)
                    ->orWhere('qr_token', $code)
                    ->orWhereHas('visit', fn ($v) => $v->where('visit_number', $code));
            })
            ->whereHas('visit', fn ($v) => $v->whereDate('visit_date', $today))
            ->first();

        $this->processCheckIn($queue);
    }

    public function checkInByNik(): void
    {
        $this->errorMessage = null;
        $this->checkedInQueue = null;

        $nik = trim($this->inputNik);
        if (strlen($nik) !== 16) {
            $this->errorMessage = 'Nomor Induk Kependudukan (NIK) harus 16 digit angka.';

            return;
        }

        $today = Carbon::today()->format('Y-m-d');

        $queue = Queue::with(['visit.patient', 'visit.doctor.polyclinic', 'visit.polyclinic', 'doctorSchedule'])
            ->whereHas('visit', function ($v) use ($today, $nik) {
                $v->whereDate('visit_date', $today)
                    ->whereHas('patient', fn ($p) => $p->where('nik', $nik));
            })
            ->whereNotIn('status', [QueueStatus::Completed, QueueStatus::Cancelled])
            ->latest()
            ->first();

        $this->processCheckIn($queue);
    }

    protected function processCheckIn(?Queue $queue): void
    {
        if (! $queue) {
            $this->errorMessage = 'Jadwal kunjungan hari ini tidak ditemukan. Pastikan nomor tiket atau NIK Anda sudah benar dan dijadwalkan hari ini.';

            return;
        }

        if ($queue->status === QueueStatus::Cancelled) {
            $this->errorMessage = 'Antrean ini telah dibatalkan.';

            return;
        }

        if ($queue->status === QueueStatus::Completed) {
            $this->errorMessage = 'Pemeriksaan untuk antrean ini telah selesai.';

            return;
        }

        // Simpan timestamp check-in mandiri
        $isFirstCheckin = is_null($queue->checked_in_at);
        if ($isFirstCheckin) {
            $queue->update([
                'checked_in_at' => now(),
                'status' => QueueStatus::Waiting,
            ]);
        }

        $this->checkedInQueue = $queue;
        $this->reset(['inputCode', 'inputNik']);
    }

    public function resetKiosk(): void
    {
        $this->reset(['checkedInQueue', 'errorMessage', 'inputCode', 'inputNik']);
        $this->mode = 'code';
    }

    public function render()
    {
        return view('livewire.public.kiosk-check-in');
    }
}
