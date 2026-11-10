<?php

namespace App\Livewire\Patient;

use App\Enums\QueueStatus;
use App\Enums\VisitStatus;
use App\Models\Queue;
use App\Models\Visit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class QueueHistory extends Component
{
    public string $tab = 'active'; // active | past

    public function cancelQueue(int $visitId): void
    {
        $user = Auth::user();
        $visit = Visit::with('queue')->findOrFail($visitId);

        // Validasi kepemilikan
        $allowed = false;
        if ($user->patient && $user->patient->id === $visit->patient_id) {
            $allowed = true;
        } else {
            $allowed = $user->familyMembers()->where('patient_id', $visit->patient_id)->exists();
        }

        if (! $allowed) {
            session()->flash('error', 'Akses ditolak.');

            return;
        }

        if (! $visit->queue || $visit->queue->status !== QueueStatus::Waiting) {
            session()->flash('error', 'Antrean tidak dapat dibatalkan.');

            return;
        }

        $visit->update(['status' => VisitStatus::Cancelled]);
        $visit->queue->update(['status' => QueueStatus::Cancelled]);

        session()->flash('success', 'Antrean berhasil dibatalkan.');
    }

    public function render()
    {
        $user = Auth::user();

        // Kumpulkan semua ID pasien yang terkait dengan user ini (diri sendiri + keluarga)
        $patientIds = [];
        if ($user->patient) {
            $patientIds[] = $user->patient->id;
        }
        $familyPatientIds = $user->familyMembers()->pluck('patient_id')->toArray();
        $allPatientIds = array_unique(array_merge($patientIds, $familyPatientIds));

        $query = Visit::with(['patient', 'doctor.polyclinic', 'polyclinic', 'queue.doctorSchedule'])
            ->whereIn('patient_id', $allPatientIds)
            ->whereHas('queue');

        if ($this->tab === 'active') {
            $query->whereHas('queue', function ($q) {
                $q->whereIn('status', [QueueStatus::Waiting, QueueStatus::Called, QueueStatus::Serving]);
            })->orderBy('visit_date', 'asc');
        } else {
            $query->whereHas('queue', function ($q) {
                $q->whereIn('status', [QueueStatus::Completed, QueueStatus::Cancelled]);
            })->orderBy('visit_date', 'desc');
        }

        $visits = $query->paginate(10);

        return view('livewire.patient.queue-history', [
            'visits' => $visits,
        ]);
    }
}
