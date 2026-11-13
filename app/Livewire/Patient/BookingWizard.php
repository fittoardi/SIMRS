<?php

namespace App\Livewire\Patient;

use App\Enums\QueueStatus;
use App\Enums\VisitStatus;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\PatientFamilyMember;
use App\Models\Polyclinic;
use App\Models\Queue;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class BookingWizard extends Component
{
    use WithFileUploads;

    public int $currentStep = 1;

    // Step 1: Pasien
    public ?int $selectedPatientId = null;

    // Step 2: Poli & Dokter
    public ?int $selectedPolyclinicId = null;

    public ?int $selectedDoctorId = null;

    // Step 3: Tanggal & Sesi Jadwal
    public string $selectedDate = '';

    public ?int $selectedScheduleId = null;

    // Step 4: Cara Pembayaran & Dokumen
    public string $paymentType = 'general'; // general, bpjs, insurance

    public string $bpjsCardNumber = '';

    public $referralLetter = null; // file

    public string $insuranceName = '';

    public string $insuranceCardNumber = '';

    public string $complaint = '';

    public function mount(): void
    {
        $user = Auth::user();

        // Default ke pasien utama user
        if ($user->patient) {
            $this->selectedPatientId = $user->patient->id;
            $this->bpjsCardNumber = $user->patient->bpjs_number ?? '';
            $this->insuranceName = $user->patient->insurance_name ?? '';
            $this->insuranceCardNumber = $user->patient->insurance_number ?? '';
        }

        // Cek query params jika datang dari halaman jadwal/dokter atau profil keluarga
        if (request()->has('patient')) {
            $pId = (int) request()->query('patient');
            if ($this->validatePatientAccess($pId)) {
                $this->selectedPatientId = $pId;
            }
        }

        if (request()->has('doctor')) {
            $docId = (int) request()->query('doctor');
            $doc = Doctor::find($docId);
            if ($doc) {
                $this->selectedPolyclinicId = $doc->polyclinic_id;
                $this->selectedDoctorId = $doc->id;
            }
        }

        // Default tanggal besok atau hari ini jika belum sore
        $this->selectedDate = date('H') >= 16 ? Carbon::tomorrow()->format('Y-m-d') : Carbon::today()->format('Y-m-d');
    }

    public function selectPatient(int $patientId): void
    {
        if ($this->validatePatientAccess($patientId)) {
            $this->selectedPatientId = $patientId;
            $patient = Patient::find($patientId);
            if ($patient) {
                $this->bpjsCardNumber = $patient->bpjs_number ?? '';
                $this->insuranceName = $patient->insurance_name ?? '';
                $this->insuranceCardNumber = $patient->insurance_number ?? '';
            }
        }
    }

    public function updatedSelectedPolyclinicId(): void
    {
        $this->selectedDoctorId = null;
        $this->selectedScheduleId = null;
    }

    public function updatedSelectedDoctorId(): void
    {
        $this->selectedScheduleId = null;
    }

    public function updatedSelectedDate(): void
    {
        $this->selectedScheduleId = null;
    }

    public function nextStep(): void
    {
        if ($this->currentStep === 1) {
            $this->validate([
                'selectedPatientId' => ['required', 'exists:patients,id'],
            ], [
                'selectedPatientId.required' => 'Silakan pilih pasien yang akan berobat.',
            ]);

            // Validasi kelengkapan data pasien
            $patient = Patient::find($this->selectedPatientId);
            if (! $patient || empty($patient->nik) || strlen($patient->nik) !== 16) {
                $this->addError('selectedPatientId', 'Pasien yang dipilih belum memiliki 16 digit NIK yang lengkap. Silakan lengkapi profil terlebih dahulu.');

                return;
            }
        } elseif ($this->currentStep === 2) {
            $this->validate([
                'selectedPolyclinicId' => ['required', 'exists:polyclinics,id'],
                'selectedDoctorId' => ['required', 'exists:doctors,id'],
            ], [
                'selectedPolyclinicId.required' => 'Silakan pilih poliklinik tujuan.',
                'selectedDoctorId.required' => 'Silakan pilih dokter spesialis yang dituju.',
            ]);
        } elseif ($this->currentStep === 3) {
            $this->validate([
                'selectedDate' => ['required', 'date', 'after_or_equal:today'],
                'selectedScheduleId' => ['required', 'exists:doctor_schedules,id'],
            ], [
                'selectedDate.required' => 'Silakan pilih tanggal kunjungan.',
                'selectedDate.after_or_equal' => 'Tanggal kunjungan tidak boleh di masa lalu.',
                'selectedScheduleId.required' => 'Silakan pilih sesi jam praktik dokter.',
            ]);

            // Cek ketersediaan kuota
            $schedule = DoctorSchedule::find($this->selectedScheduleId);
            if ($schedule) {
                $booked = Queue::where('doctor_schedule_id', $schedule->id)
                    ->whereHas('visit', fn ($q) => $q->where('visit_date', $this->selectedDate)->where('status', '!=', VisitStatus::Cancelled))
                    ->count();

                if ($booked >= $schedule->max_quota) {
                    $this->addError('selectedScheduleId', 'Mohon maaf, kuota untuk sesi ini pada tanggal tersebut telah penuh.');

                    return;
                }
            }
        } elseif ($this->currentStep === 4) {
            $rules = [
                'paymentType' => ['required', 'in:general,bpjs,insurance'],
                'complaint' => ['nullable', 'string', 'max:500'],
            ];

            if ($this->paymentType === 'bpjs') {
                $rules['bpjsCardNumber'] = ['required', 'string', 'min:8', 'max:30'];
                $rules['referralLetter'] = ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'];
            } elseif ($this->paymentType === 'insurance') {
                $rules['insuranceName'] = ['required', 'string', 'max:100'];
                $rules['insuranceCardNumber'] = ['required', 'string', 'max:50'];
            }

            $this->validate($rules, [
                'bpjsCardNumber.required' => 'Nomor kartu BPJS Kesehatan wajib diisi.',
                'referralLetter.max' => 'Ukuran surat rujukan maksimal 2 MB.',
                'insuranceName.required' => 'Nama asuransi wajib diisi.',
                'insuranceCardNumber.required' => 'Nomor polis/kartu asuransi wajib diisi.',
            ]);
        }

        if ($this->currentStep < 5) {
            $this->currentStep++;
        }
    }

    public function prevStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function confirmBooking(): void
    {
        // Validasi hak akses pasien terpilih (anti-IDOR & anti-tampering)
        if (! $this->selectedPatientId || ! $this->validatePatientAccess((int) $this->selectedPatientId)) {
            abort(403, 'Akses ke data pasien ini tidak diizinkan.');
        }

        // Upload surat rujukan jika ada
        $referralPath = null;
        if ($this->referralLetter) {
            $referralPath = $this->referralLetter->store('referrals', 'public');
        }

        try {
            $visit = DB::transaction(function () use ($referralPath) {
                // Re-verifikasi kuota terakhir kali dengan lock
                $schedule = DoctorSchedule::with('doctor.polyclinic')->lockForUpdate()->findOrFail($this->selectedScheduleId);
                $bookedCount = Queue::where('doctor_schedule_id', $schedule->id)
                    ->whereHas('visit', fn ($q) => $q->where('visit_date', $this->selectedDate)->where('status', '!=', VisitStatus::Cancelled))
                    ->count();

                if ($bookedCount >= $schedule->max_quota) {
                    throw new \RuntimeException('Mohon maaf, kuota sesi dokter ini telah habis terisi. Silakan pilih jadwal sesi lainnya.');
                }

                // 1. Buat data Visit
                $newVisit = Visit::create([
                    'visit_number' => Visit::generateVisitNumber(),
                    'patient_id' => $this->selectedPatientId,
                    'doctor_id' => $this->selectedDoctorId,
                    'polyclinic_id' => $this->selectedPolyclinicId,
                    'visit_date' => $this->selectedDate,
                    'visit_type' => 'outpatient',
                    'payment_type' => $this->paymentType,
                    'bpjs_card_number' => $this->paymentType === 'bpjs' ? $this->bpjsCardNumber : null,
                    'referral_letter_path' => $referralPath,
                    'insurance_name' => $this->paymentType === 'insurance' ? $this->insuranceName : null,
                    'insurance_card_number' => $this->paymentType === 'insurance' ? $this->insuranceCardNumber : null,
                    'status' => VisitStatus::Registered,
                    'notes' => $this->complaint ?: null,
                ]);

                // 2. Hitung nomor antrean berikutnya pada jadwal & tanggal ini
                $queueNumber = $bookedCount + 1;
                $poliCode = $schedule->doctor?->polyclinic?->code ?? 'POL';
                $queueCode = sprintf('%s-%s-%03d', $poliCode, date('ymd', strtotime($this->selectedDate)), $queueNumber);

                // 3. Estimasi jam pelayanan
                $duration = $schedule->doctor?->polyclinic?->consultation_duration_minutes ?? 15;
                $startTime = Carbon::createFromFormat('H:i:s', $schedule->start_time);
                $estimatedTime = $startTime->copy()->addMinutes(($queueNumber - 1) * $duration)->format('H:i');

                // 4. Buat data Queue
                Queue::create([
                    'visit_id' => $newVisit->id,
                    'doctor_schedule_id' => $schedule->id,
                    'queue_number' => $queueNumber,
                    'queue_code' => $queueCode,
                    'qr_token' => Queue::generateQrToken(),
                    'estimated_time' => $estimatedTime,
                    'status' => QueueStatus::Waiting,
                ]);

                return $newVisit;
            });

            session()->flash('success', 'Pendaftaran antrean berhasil!');
            $this->redirect(route('patient.ticket', $visit->id), navigate: true);
        } catch (\RuntimeException $e) {
            session()->flash('error', $e->getMessage());
            $this->currentStep = 3;
        }
    }

    protected function validatePatientAccess(int $patientId): bool
    {
        $user = Auth::user();
        if ($user->patient && $user->patient->id === $patientId) {
            return true;
        }

        return PatientFamilyMember::where('user_id', $user->id)
            ->where('patient_id', $patientId)
            ->exists();
    }

    public function getAvailablePatientsProperty(): Collection
    {
        $user = Auth::user();
        $patients = collect();

        if ($user->patient) {
            $patients->push([
                'id' => $user->patient->id,
                'name' => $user->patient->name,
                'relation' => 'Diri Sendiri (Utama)',
                'mrn' => $user->patient->medical_record_number,
                'nik' => $user->patient->nik,
                'allergies' => $user->patient->allergies,
            ]);
        }

        $family = PatientFamilyMember::with('patient')
            ->where('user_id', $user->id)
            ->get();

        foreach ($family as $f) {
            if ($f->patient) {
                $patients->push([
                    'id' => $f->patient->id,
                    'name' => $f->patient->name,
                    'relation' => ucfirst(str_replace('_', ' ', $f->relationship)),
                    'mrn' => $f->patient->medical_record_number,
                    'nik' => $f->patient->nik,
                    'allergies' => $f->patient->allergies,
                ]);
            }
        }

        return $patients;
    }

    public function getPolyclinicsProperty(): Collection
    {
        return Polyclinic::where('is_active', true)
            ->withCount(['doctors' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();
    }

    public function getDoctorsProperty(): Collection
    {
        if (! $this->selectedPolyclinicId) {
            return collect();
        }

        return Doctor::where('polyclinic_id', $this->selectedPolyclinicId)
            ->where('is_active', true)
            ->with('schedules')
            ->orderBy('name')
            ->get();
    }

    public function getDoctorSchedulesProperty(): Collection
    {
        if (! $this->selectedDoctorId || ! $this->selectedDate) {
            return collect();
        }

        $dayOfWeek = (int) date('N', strtotime($this->selectedDate)); // 1 = Senin, 7 = Minggu

        $schedules = DoctorSchedule::where('doctor_id', $this->selectedDoctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        // Hitung kuota real-time untuk setiap sesi
        return $schedules->map(function ($schedule) {
            $booked = Queue::where('doctor_schedule_id', $schedule->id)
                ->whereHas('visit', fn ($q) => $q->where('visit_date', $this->selectedDate)->where('status', '!=', VisitStatus::Cancelled))
                ->count();

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

    public function render()
    {
        $selectedPatient = $this->selectedPatientId ? Patient::find($this->selectedPatientId) : null;
        $selectedPolyclinic = $this->selectedPolyclinicId ? Polyclinic::find($this->selectedPolyclinicId) : null;
        $selectedDoctor = $this->selectedDoctorId ? Doctor::find($this->selectedDoctorId) : null;
        $selectedSchedule = $this->selectedScheduleId ? DoctorSchedule::find($this->selectedScheduleId) : null;

        return view('livewire.patient.booking-wizard', [
            'selectedPatient' => $selectedPatient,
            'selectedPolyclinic' => $selectedPolyclinic,
            'selectedDoctor' => $selectedDoctor,
            'selectedSchedule' => $selectedSchedule,
        ]);
    }
}
