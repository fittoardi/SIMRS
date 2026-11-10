<?php

namespace App\Livewire\Patient;

use App\Enums\QueueStatus;
use App\Enums\VisitStatus;
use App\Models\Queue;
use App\Models\Visit;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DigitalTicket extends Component
{
    public int $visitId;
    public ?string $qrSvg = null;

    public function mount(int $visitId): void
    {
        $this->visitId = $visitId;
        $this->authorizeAccess();
        $this->generateQr();
    }

    protected function authorizeAccess(): Visit
    {
        $user = Auth::user();
        $visit = Visit::with(['patient', 'doctor.polyclinic', 'polyclinic', 'queue.doctorSchedule'])
            ->findOrFail($this->visitId);

        // Pastikan tiket ini milik user atau anggota keluarga user
        $isOwner = false;
        if ($user->patient && $user->patient->id === $visit->patient_id) {
            $isOwner = true;
        } else {
            $isFamily = $user->familyMembers()->where('patient_id', $visit->patient_id)->exists();
            if ($isFamily) {
                $isOwner = true;
            }
        }

        // Atau jika user adalah staf medis admin
        if (! $isOwner && ! $user->hasAnyRole(['super_admin', 'doctor', 'nurse', 'admission', 'pharmacist', 'cashier'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat tiket antrean ini.');
        }

        return $visit;
    }

    public function generateQr(): void
    {
        $visit = Visit::with('queue')->find($this->visitId);
        if ($visit && $visit->queue && $visit->queue->qr_token) {
            $this->qrSvg = QrCodeService::svg($visit->queue->qr_token, 180, 2);
        }
    }

    public function cancelQueue(): void
    {
        $visit = $this->authorizeAccess();

        if (! $visit->queue || $visit->queue->status !== QueueStatus::Waiting) {
            session()->flash('error', 'Antrean tidak dapat dibatalkan karena sudah dipanggil atau selesai.');

            return;
        }

        $visit->update(['status' => VisitStatus::Cancelled]);
        $visit->queue->update(['status' => QueueStatus::Cancelled]);

        session()->flash('success', 'Antrean berhasil dibatalkan.');
    }

    public function render()
    {
        $visit = Visit::with(['patient', 'doctor.polyclinic', 'polyclinic', 'queue.doctorSchedule'])
            ->findOrFail($this->visitId);

        // Hitung jumlah antrean yang sedang menunggu di depan pasien ini
        $queuesAhead = 0;
        if ($visit->queue && $visit->queue->status === QueueStatus::Waiting && $visit->queue->doctor_schedule_id) {
            $queuesAhead = Queue::where('doctor_schedule_id', $visit->queue->doctor_schedule_id)
                ->where('status', QueueStatus::Waiting)
                ->where('queue_number', '<', $visit->queue->queue_number)
                ->whereHas('visit', fn ($q) => $q->where('visit_date', $visit->visit_date))
                ->count();
        }

        return view('livewire.patient.digital-ticket', [
            'visit' => $visit,
            'queue' => $visit->queue,
            'queuesAhead' => $queuesAhead,
        ]);
    }
}

