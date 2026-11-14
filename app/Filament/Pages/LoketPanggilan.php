<?php

namespace App\Filament\Pages;

use App\Enums\QueueStatus;
use App\Enums\VisitStatus;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Models\Queue;
use App\Models\Visit;
use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class LoketPanggilan extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSpeakerWave;

    protected static string|UnitEnum|null $navigationGroup = 'Pelayanan Pasien';

    protected static ?string $navigationLabel = 'Loket Panggilan Antrean';

    protected static ?string $title = 'Loket Panggilan Antrean & Admission';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.loket-panggilan';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admission', 'nurse', 'doctor']) ?? false;
    }

    // State Filter & Active Caller
    public ?int $selectedPolyclinicId = null;

    public ?int $selectedDoctorId = null;

    public ?Queue $activeQueue = null;

    // State Walk-In Registration
    public bool $showWalkInModal = false;

    public string $walkInName = '';

    public string $walkInNik = '';

    public string $walkInGender = 'L';

    public string $walkInBirthDate = '';

    public string $walkInPhone = '';

    public string $walkInPaymentType = 'general';

    public ?int $walkInPolyclinicId = null;

    public ?int $walkInDoctorId = null;

    public function mount(): void
    {
        $firstPoli = Polyclinic::where('is_active', true)->first();
        if ($firstPoli) {
            $this->selectedPolyclinicId = $firstPoli->id;
            $firstDoc = Doctor::where('polyclinic_id', $firstPoli->id)->where('is_active', true)->first();
            if ($firstDoc) {
                $this->selectedDoctorId = $firstDoc->id;
            }
        }

        $this->refreshActiveQueue();
    }

    public function updatedSelectedPolyclinicId(): void
    {
        $firstDoc = Doctor::where('polyclinic_id', $this->selectedPolyclinicId)->where('is_active', true)->first();
        $this->selectedDoctorId = $firstDoc?->id;
        $this->refreshActiveQueue();
    }

    public function updatedSelectedDoctorId(): void
    {
        $this->refreshActiveQueue();
    }

    public function refreshActiveQueue(): void
    {
        $today = Carbon::today()->format('Y-m-d');

        $query = Queue::with(['visit.patient', 'visit.doctor.polyclinic', 'visit.polyclinic', 'doctorSchedule'])
            ->whereHas('visit', function ($v) use ($today) {
                $v->whereDate('visit_date', $today);
                if ($this->selectedPolyclinicId) {
                    $v->where('polyclinic_id', $this->selectedPolyclinicId);
                }
                if ($this->selectedDoctorId) {
                    $v->where('doctor_id', $this->selectedDoctorId);
                }
            });

        // Utamakan yang sedang dipanggil atau dilayani
        $this->activeQueue = (clone $query)
            ->whereIn('status', [QueueStatus::Called, QueueStatus::Serving])
            ->orderByDesc('called_at')
            ->first();

        // Jika belum ada yang dipanggil, ambil antrean pertama yang berstatus waiting
        if (! $this->activeQueue) {
            $this->activeQueue = (clone $query)
                ->where('status', QueueStatus::Waiting)
                ->orderBy('queue_number', 'asc')
                ->first();
        }
    }

    public function callQueue(?int $queueId = null): void
    {
        $queue = $queueId ? Queue::find($queueId) : $this->activeQueue;

        if (! $queue) {
            Notification::make()->warning()->title('Tidak ada antrean untuk dipanggil.')->send();

            return;
        }

        $queue->update([
            'status' => QueueStatus::Called,
            'called_at' => now(),
            'call_count' => ($queue->call_count ?? 0) + 1,
        ]);

        Notification::make()
            ->success()
            ->title("Panggilan Nomor {$queue->queue_code}")
            ->body("Pasien: {$queue->visit?->patient?->name} (Panggilan ke-{$queue->call_count})")
            ->send();

        $this->refreshActiveQueue();
    }

    public function serveQueue(?int $queueId = null): void
    {
        $queue = $queueId ? Queue::find($queueId) : $this->activeQueue;

        if (! $queue) {
            return;
        }

        $queue->update([
            'status' => QueueStatus::Serving,
            'served_at' => now(),
        ]);

        $queue->visit?->update(['status' => VisitStatus::InExamination]);

        Notification::make()
            ->info()
            ->title("Memulai Pelayanan {$queue->queue_code}")
            ->body("Pasien {$queue->visit?->patient?->name} sedang berkonsultasi dengan dokter.")
            ->send();

        $this->refreshActiveQueue();
    }

    public function completeQueue(?int $queueId = null): void
    {
        $queue = $queueId ? Queue::find($queueId) : $this->activeQueue;

        if (! $queue) {
            return;
        }

        $queue->update([
            'status' => QueueStatus::Completed,
            'completed_at' => now(),
        ]);

        $queue->visit?->update(['status' => VisitStatus::Completed]);

        Notification::make()
            ->success()
            ->title('Pelayanan Selesai')
            ->body("Antrean {$queue->queue_code} telah selesai.")
            ->send();

        $this->refreshActiveQueue();
    }

    public function skipQueue(?int $queueId = null): void
    {
        $queue = $queueId ? Queue::find($queueId) : $this->activeQueue;

        if (! $queue) {
            return;
        }

        $queue->update([
            'status' => QueueStatus::Skipped,
        ]);

        Notification::make()
            ->warning()
            ->title('Antrean Dilewati')
            ->body("Nomor {$queue->queue_code} dilewati karena tidak hadir di ruang tunggu.")
            ->send();

        $this->refreshActiveQueue();
    }

    // Modal Pendaftaran Pasien Langsung di Loket (Walk-in)
    public function openWalkInModal(): void
    {
        $this->walkInPolyclinicId = $this->selectedPolyclinicId;
        $this->walkInDoctorId = $this->selectedDoctorId;
        $this->showWalkInModal = true;
    }

    public function closeWalkInModal(): void
    {
        $this->showWalkInModal = false;
    }

    public function registerWalkInPatient(): void
    {
        $this->validate([
            'walkInName' => ['required', 'string', 'max:255'],
            'walkInNik' => ['required', 'digits:16'],
            'walkInGender' => ['required', 'in:L,P'],
            'walkInBirthDate' => ['required', 'date', 'before_or_equal:today'],
            'walkInPhone' => ['nullable', 'string', 'max:20'],
            'walkInPolyclinicId' => ['required', 'exists:polyclinics,id'],
            'walkInDoctorId' => ['required', 'exists:doctors,id'],
        ], [
            'walkInNik.digits' => 'NIK harus 16 digit angka.',
            'walkInName.required' => 'Nama pasien wajib diisi.',
            'walkInBirthDate.required' => 'Tanggal lahir wajib diisi.',
        ]);

        // Cari atau buat pasien baru
        $patient = Patient::firstOrCreate(
            ['nik' => $this->walkInNik],
            [
                'medical_record_number' => Patient::generateMedicalRecordNumber(),
                'name' => $this->walkInName,
                'gender' => $this->walkInGender,
                'birth_date' => $this->walkInBirthDate,
                'phone' => $this->walkInPhone ?: null,
                'address' => 'Pendaftaran Loket Langsung',
            ]
        );

        $today = Carbon::today()->format('Y-m-d');
        $dayOfWeek = (int) date('N');

        // Cari sesi jadwal dokter hari ini
        $schedule = DoctorSchedule::where('doctor_id', $this->walkInDoctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        // Buat Visit
        $visit = Visit::create([
            'visit_number' => Visit::generateVisitNumber(),
            'patient_id' => $patient->id,
            'doctor_id' => $this->walkInDoctorId,
            'polyclinic_id' => $this->walkInPolyclinicId,
            'visit_date' => $today,
            'visit_type' => 'outpatient',
            'payment_type' => $this->walkInPaymentType,
            'status' => VisitStatus::Registered,
            'notes' => 'Pendaftaran Loket Admission (Walk-in)',
        ]);

        // Hitung nomor antrean berikutnya
        $bookedCount = Queue::whereHas('visit', fn ($v) => $v->whereDate('visit_date', $today)->where('polyclinic_id', $this->walkInPolyclinicId))->count();
        $queueNumber = $bookedCount + 1;
        $poli = Polyclinic::find($this->walkInPolyclinicId);
        $queueCode = sprintf('%s-%s-%03d', $poli->code, date('ymd'), $queueNumber);

        // Buat Queue langsung hadir (checked_in_at terisi)
        $queue = Queue::create([
            'visit_id' => $visit->id,
            'doctor_schedule_id' => $schedule?->id,
            'queue_number' => $queueNumber,
            'queue_code' => $queueCode,
            'qr_token' => Queue::generateQrToken(),
            'checked_in_at' => now(),
            'status' => QueueStatus::Waiting,
        ]);

        $this->showWalkInModal = false;
        $this->reset(['walkInName', 'walkInNik', 'walkInPhone', 'walkInBirthDate']);

        Notification::make()
            ->success()
            ->title("Pendaftaran Berhasil: {$queueCode}")
            ->body("Pasien {$patient->name} berhasil didaftarkan dan masuk dalam antrean menunggu.")
            ->send();

        $this->refreshActiveQueue();
    }

    public function getViewData(): array
    {
        $today = Carbon::today()->format('Y-m-d');

        $waitingQueues = Queue::with(['visit.patient', 'visit.doctor', 'visit.polyclinic'])
            ->whereHas('visit', function ($v) use ($today) {
                $v->whereDate('visit_date', $today);
                if ($this->selectedPolyclinicId) {
                    $v->where('polyclinic_id', $this->selectedPolyclinicId);
                }
                if ($this->selectedDoctorId) {
                    $v->where('doctor_id', $this->selectedDoctorId);
                }
            })
            ->whereIn('status', [QueueStatus::Waiting, QueueStatus::Called, QueueStatus::Serving])
            ->orderByRaw("CASE status WHEN 'serving' THEN 1 WHEN 'called' THEN 2 WHEN 'waiting' THEN 3 ELSE 4 END")
            ->orderBy('queue_number', 'asc')
            ->get();

        $completedCount = Queue::whereHas('visit', function ($v) use ($today) {
            $v->whereDate('visit_date', $today);
            if ($this->selectedPolyclinicId) {
                $v->where('polyclinic_id', $this->selectedPolyclinicId);
            }
        })->where('status', QueueStatus::Completed)->count();

        return [
            'polyclinics' => Polyclinic::where('is_active', true)->get(),
            'doctors' => $this->selectedPolyclinicId ? Doctor::where('polyclinic_id', $this->selectedPolyclinicId)->where('is_active', true)->get() : collect(),
            'waitingQueues' => $waitingQueues,
            'completedCount' => $completedCount,
        ];
    }
}
