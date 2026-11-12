<?php

namespace App\Livewire\Public;

use App\Enums\QueueStatus;
use App\Enums\VisitStatus;
use App\Models\Queue;
use App\Services\QrCodeService;
use Livewire\Component;

class PublicTicket extends Component
{
    public string $token;

    public ?string $qrSvg = null;

    public function mount(string $token): void
    {
        $this->token = $token;
        $queue = $this->findQueueByToken();

        if ($queue->qr_token) {
            $this->qrSvg = QrCodeService::svg($queue->qr_token, 180, 2);
        }
    }

    public function cancelQueue(): void
    {
        $queue = $this->findQueueByToken();

        if ($queue->status !== QueueStatus::Waiting) {
            session()->flash('error', 'Antrean tidak dapat dibatalkan karena sudah dipanggil atau selesai.');

            return;
        }

        $queue->update(['status' => QueueStatus::Cancelled]);
        $queue->visit?->update(['status' => VisitStatus::Cancelled]);

        session()->flash('success', 'Antrean berhasil dibatalkan.');
    }

    protected function findQueueByToken(): Queue
    {
        return Queue::with(['visit.patient', 'visit.doctor.polyclinic', 'visit.polyclinic', 'doctorSchedule'])
            ->where('qr_token', $this->token)
            ->firstOrFail();
    }

    public function render()
    {
        $queue = $this->findQueueByToken();
        $visit = $queue->visit;

        $queuesAhead = 0;
        if ($queue->status === QueueStatus::Waiting && $queue->doctor_schedule_id && $visit) {
            $queuesAhead = Queue::where('doctor_schedule_id', $queue->doctor_schedule_id)
                ->where('status', QueueStatus::Waiting)
                ->where('queue_number', '<', $queue->queue_number)
                ->whereHas('visit', fn ($q) => $q->whereDate('visit_date', $visit->visit_date))
                ->count();
        }

        return view('livewire.public.public-ticket', [
            'queue' => $queue,
            'visit' => $visit,
            'queuesAhead' => $queuesAhead,
        ]);
    }
}

