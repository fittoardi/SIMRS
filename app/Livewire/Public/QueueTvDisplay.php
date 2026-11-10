<?php

namespace App\Livewire\Public;

use App\Enums\QueueStatus;
use App\Models\Polyclinic;
use App\Models\Queue;
use Carbon\Carbon;
use Livewire\Component;

class QueueTvDisplay extends Component
{
    public ?int $lastCalledQueueId = null;

    public ?string $lastCalledAt = null;

    public function render()
    {
        $today = Carbon::today()->format('Y-m-d');

        // 1. Antrean yang paling baru dipanggil hari ini
        $currentCall = Queue::with(['visit.patient', 'visit.doctor.polyclinic', 'visit.polyclinic', 'doctorSchedule'])
            ->whereHas('visit', fn ($v) => $v->whereDate('visit_date', $today))
            ->whereIn('status', [QueueStatus::Called, QueueStatus::Serving])
            ->orderByDesc('called_at')
            ->first();

        // Cek apakah ada panggilan baru untuk memicu audio browser event
        if ($currentCall && $currentCall->called_at) {
            $calledKey = $currentCall->id.'_'.$currentCall->called_at->timestamp.'_'.$currentCall->call_count;
            if ($this->lastCalledAt !== $calledKey) {
                $this->lastCalledAt = $calledKey;
                $this->lastCalledQueueId = $currentCall->id;

                $this->dispatch('play-queue-call', [
                    'code' => $currentCall->queue_code,
                    'poli' => $currentCall->visit?->polyclinic?->name ?? 'Poliklinik',
                    'room' => $currentCall->doctorSchedule?->room_name ?? 'Ruang Poliklinik',
                    'doctor' => $currentCall->visit?->doctor?->name ?? '',
                ]);
            }
        }

        // 2. Status seluruh poliklinik aktif untuk grid sisi kanan
        $polyclinics = Polyclinic::where('is_active', true)
            ->with(['doctors.schedules' => fn ($s) => $s->where('is_active', true)])
            ->get()
            ->map(function ($poli) use ($today) {
                // Antrean yang sedang dipanggil / dilayani di poli ini
                $activeQueue = Queue::whereHas('visit', fn ($v) => $v->whereDate('visit_date', $today)->where('polyclinic_id', $poli->id))
                    ->whereIn('status', [QueueStatus::Called, QueueStatus::Serving])
                    ->orderByDesc('called_at')
                    ->first();

                // Antrean berikutnya yang berstatus waiting
                $nextQueue = Queue::whereHas('visit', fn ($v) => $v->whereDate('visit_date', $today)->where('polyclinic_id', $poli->id))
                    ->where('status', QueueStatus::Waiting)
                    ->orderBy('queue_number', 'asc')
                    ->first();

                // Jumlah antrean menunggu
                $waitingCount = Queue::whereHas('visit', fn ($v) => $v->whereDate('visit_date', $today)->where('polyclinic_id', $poli->id))
                    ->where('status', QueueStatus::Waiting)
                    ->count();

                return [
                    'id' => $poli->id,
                    'code' => $poli->code,
                    'name' => $poli->name,
                    'location' => $poli->location ?? 'Gedung Utama',
                    'current_code' => $activeQueue?->queue_code ?? '-',
                    'current_status' => $activeQueue?->status?->label() ?? 'Menunggu Pasien',
                    'next_code' => $nextQueue?->queue_code ?? '-',
                    'waiting_count' => $waitingCount,
                ];
            });

        return view('livewire.public.queue-tv-display', [
            'currentCall' => $currentCall,
            'polyclinics' => $polyclinics,
        ]);
    }
}
