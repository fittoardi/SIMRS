<?php

namespace App\Livewire\Public;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Services\QueueBookingService;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use RuntimeException;

class GuestBooking extends Component
{
    public int $currentStep = 1;

    // Step 1: Data Pasien Tamu (Tanpa Akun)
    public string $name = '';

    public string $nik = '';

    public string $phone = '';

    public string $gender = 'L';

    public string $birthDate = '';

    public bool $existingPatientMatched = false;

    // Step 2: Poli & Dokter
    public ?int $selectedPolyclinicId = null;

    public ?int $selectedDoctorId = null;

    // Step 3: Tanggal & Sesi Jadwal
    public string $selectedDate = '';

    public ?int $selectedScheduleId = null;

    // Step 4: Pembayaran & Keluhan
    public string $paymentType = 'general';

    public string $bpjsCardNumber = '';

    public string $insuranceName = '';

    public string $insuranceCardNumber = '';

    public string $complaint = '';

    public function mount(): void
    {
        if (request()->has('doctor')) {
            $doc = Doctor::find((int) request()->query('doctor'));
            if ($doc && $doc->is_active) {
                $this->selectedPolyclinicId = $doc->polyclinic_id;
                $this->selectedDoctorId = $doc->id;
            }
        }

        $this->selectedDate = ((int) date('H')) >= 16
            ? Carbon::tomorrow()->format('Y-m-d')
            : Carbon::today()->format('Y-m-d');
    }

    public function updatedNik(string $value): void
    {
        $cleanNik = preg_replace('/\D/', '', $value) ?? '';
        $this->nik = substr($cleanNik, 0, 16);
        $this->existingPatientMatched = strlen($this->nik) === 16
            && Patient::where('nik', $this->nik)->exists();
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

    public function nextStep(QueueBookingService $bookingService): void
    {
        if ($this->currentStep === 1) {
            $this->validate([
                'name' => ['required', 'string', 'min:2', 'max:255'],
                'nik' => ['required', 'digits:16'],
                'phone' => ['required', 'string', 'min:9', 'max:20'],
                'gender' => ['required', 'in:L,P'],
                'birthDate' => ['required', 'date', 'before_or_equal:today'],
            ], [
                'name.required' => 'Nama lengkap sesuai KTP wajib diisi.',
                'nik.required' => 'NIK 16 digit wajib diisi.',
                'nik.digits' => 'NIK harus tepat 16 digit angka.',
                'phone.required' => 'Nomor HP/WhatsApp wajib diisi untuk informasi antrean.',
                'phone.min' => 'Nomor HP/WhatsApp minimal 9 karakter.',
                'birthDate.required' => 'Tanggal lahir wajib diisi.',
                'birthDate.before_or_equal' => 'Tanggal lahir tidak valid.',
            ]);
        } elseif ($this->currentStep === 2) {
            $this->validate([
                'selectedPolyclinicId' => ['required', 'exists:polyclinics,id'],
                'selectedDoctorId' => ['required', 'exists:doctors,id'],
            ], [
                'selectedPolyclinicId.required' => 'Silakan pilih poliklinik tujuan.',
                'selectedDoctorId.required' => 'Silakan pilih dokter spesialis.',
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

            $schedule = DoctorSchedule::find($this->selectedScheduleId);
            if ($schedule && $bookingService->countBookedForSchedule($schedule->id, $this->selectedDate) >= $schedule->max_quota) {
                $this->addError('selectedScheduleId', 'Mohon maaf, kuota untuk sesi ini pada tanggal tersebut telah penuh.');

                return;
            }

            $existingPatient = Patient::where('nik', $this->nik)->first();
            if ($existingPatient && $bookingService->hasActiveVisitOnDate($existingPatient->id, (int) $this->selectedDoctorId, $this->selectedDate)) {
                $this->addError('selectedScheduleId', 'Pasien dengan NIK ini sudah memiliki antrean aktif pada dokter dan tanggal yang dipilih.');

                return;
            }
        }

        if ($this->currentStep < 4) {
            $this->currentStep++;
        }
    }

    public function prevStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function confirmBooking(QueueBookingService $bookingService): void
    {
        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'nik' => ['required', 'digits:16'],
            'phone' => ['required', 'string', 'min:9', 'max:20'],
            'gender' => ['required', 'in:L,P'],
            'birthDate' => ['required', 'date', 'before_or_equal:today'],
            'selectedPolyclinicId' => ['required', 'exists:polyclinics,id'],
            'selectedDoctorId' => ['required', 'exists:doctors,id'],
            'selectedDate' => ['required', 'date', 'after_or_equal:today'],
            'selectedScheduleId' => ['required', 'exists:doctor_schedules,id'],
            'paymentType' => ['required', 'in:general,bpjs,insurance'],
            'complaint' => ['nullable', 'string', 'max:500'],
        ];

        if ($this->paymentType === 'bpjs') {
            $rules['bpjsCardNumber'] = ['required', 'string', 'min:8', 'max:30'];
        } elseif ($this->paymentType === 'insurance') {
            $rules['insuranceName'] = ['required', 'string', 'max:100'];
            $rules['insuranceCardNumber'] = ['required', 'string', 'max:50'];
        }

        $this->validate($rules, [
            'bpjsCardNumber.required' => 'Nomor kartu BPJS Kesehatan wajib diisi.',
            'insuranceName.required' => 'Nama asuransi wajib diisi.',
            'insuranceCardNumber.required' => 'Nomor polis/kartu asuransi wajib diisi.',
        ]);

        // Cari pasien berdasarkan NIK atau buat data rekam medis baru tanpa akun user
        $patient = Patient::where('nik', $this->nik)->first();

        if (! $patient) {
            $patient = Patient::create([
                'user_id' => null,
                'medical_record_number' => Patient::generateMedicalRecordNumber(),
                'nik' => $this->nik,
                'name' => $this->name,
                'gender' => $this->gender,
                'birth_date' => $this->birthDate,
                'phone' => $this->phone,
                'bpjs_number' => $this->paymentType === 'bpjs' ? $this->bpjsCardNumber : null,
                'insurance_name' => $this->paymentType === 'insurance' ? $this->insuranceName : null,
                'insurance_number' => $this->paymentType === 'insurance' ? $this->insuranceCardNumber : null,
                'address' => 'Pendaftaran Online (Tanpa Akun)',
            ]);
        } elseif (empty($patient->phone)) {
            $patient->update(['phone' => $this->phone]);
        }

        if ($bookingService->hasActiveVisitOnDate($patient->id, (int) $this->selectedDoctorId, $this->selectedDate)) {
            session()->flash('error', 'Pasien dengan NIK ini sudah memiliki antrean aktif pada dokter dan tanggal tersebut.');
            $this->currentStep = 3;

            return;
        }

        $schedule = DoctorSchedule::with('doctor.polyclinic')->findOrFail($this->selectedScheduleId);

        try {
            $result = $bookingService->createOutpatientBooking($patient, $schedule, $this->selectedDate, [
                'payment_type' => $this->paymentType,
                'bpjs_card_number' => $this->bpjsCardNumber,
                'insurance_name' => $this->insuranceName,
                'insurance_card_number' => $this->insuranceCardNumber,
                'notes' => $this->complaint ?: null,
            ]);

            // Kirim notifikasi WhatsApp e-tiket & konfirmasi pendaftaran guest
            try {
                app(WhatsAppNotificationService::class)->sendBookingConfirmation($result['visit'], $result['queue']);
            } catch (\Throwable $e) {
                Log::warning('Gagal kirim notifikasi WhatsApp guest booking: '.$e->getMessage());
            }
        } catch (RuntimeException $e) {
            session()->flash('error', $e->getMessage());
            $this->currentStep = 3;

            return;
        }

        session()->flash('success', "Booking berhasil! Nomor Antrean Anda: {$result['queue']->queue_code}");

        $this->redirect(route('public.ticket', ['token' => $result['queue']->qr_token]), navigate: true);
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

        return app(QueueBookingService::class)->getSchedulesWithQuota(
            (int) $this->selectedDoctorId,
            $this->selectedDate
        );
    }

    public function render()
    {
        return view('livewire.public.guest-booking', [
            'selectedPolyclinic' => $this->selectedPolyclinicId ? Polyclinic::find($this->selectedPolyclinicId) : null,
            'selectedDoctor' => $this->selectedDoctorId ? Doctor::find($this->selectedDoctorId) : null,
            'selectedSchedule' => $this->selectedScheduleId ? DoctorSchedule::find($this->selectedScheduleId) : null,
        ]);
    }
}
