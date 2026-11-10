<?php

use App\Enums\QueueStatus;
use App\Enums\VisitStatus;
use App\Livewire\Patient\BookingWizard;
use App\Livewire\Patient\DigitalTicket;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Models\Queue;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Livewire\Livewire;

test('guest cannot access booking page', function () {
    $response = $this->get('/akun/booking');

    $response->assertRedirect('/login');
});

test('user can open booking wizard page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/akun/booking');

    $response->assertOk();
    $response->assertSeeLivewire(BookingWizard::class);
});

test('patient can complete 5-step booking wizard and receive digital ticket', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create([
        'user_id' => $user->id,
        'nik' => '3271012345678901',
        'name' => 'Budi Santoso',
    ]);

    $polyclinic = Polyclinic::factory()->create([
        'code' => 'POL',
        'name' => 'Poli Penyakit Dalam',
        'is_active' => true,
    ]);

    $doctor = Doctor::factory()->create([
        'polyclinic_id' => $polyclinic->id,
        'name' => 'dr. Andi Pratama, Sp.PD',
        'is_active' => true,
        'consultation_fee' => 150000,
    ]);

    // Hari ini
    $today = Carbon::today();
    $dayOfWeek = (int) $today->format('N');

    $schedule = DoctorSchedule::factory()->create([
        'doctor_id' => $doctor->id,
        'day_of_week' => $dayOfWeek,
        'start_time' => '08:00:00',
        'end_time' => '12:00:00',
        'max_quota' => 20,
        'is_active' => true,
    ]);

    $component = Livewire::actingAs($user)
        ->test(BookingWizard::class)
        // Step 1: Pasien
        ->assertSet('currentStep', 1)
        ->set('selectedPatientId', $patient->id)
        ->call('nextStep')
        // Step 2: Poli & Dokter
        ->assertSet('currentStep', 2)
        ->set('selectedPolyclinicId', $polyclinic->id)
        ->set('selectedDoctorId', $doctor->id)
        ->call('nextStep')
        // Step 3: Tanggal & Sesi
        ->assertSet('currentStep', 3)
        ->set('selectedDate', $today->format('Y-m-d'))
        ->set('selectedScheduleId', $schedule->id)
        ->call('nextStep')
        // Step 4: Cara Pembayaran
        ->assertSet('currentStep', 4)
        ->set('paymentType', 'general')
        ->set('complaint', 'Keluhan demam 3 hari')
        ->call('nextStep')
        // Step 5: Konfirmasi Ringkasan
        ->assertSet('currentStep', 5)
        ->call('confirmBooking');

    // Cek bahwa visit & queue terbentuk di database
    $visit = Visit::where('patient_id', $patient->id)->latest()->first();
    expect($visit)->not->toBeNull();
    expect($visit->status)->toBe(VisitStatus::Registered);
    expect($visit->payment_type)->toBe('general');

    $queue = Queue::where('visit_id', $visit->id)->first();
    expect($queue)->not->toBeNull();
    expect($queue->queue_number)->toBe(1);
    expect($queue->status)->toBe(QueueStatus::Waiting);
    expect($queue->queue_code)->toContain('POL-');
    expect($queue->qr_token)->not->toBeEmpty();
});

test('patient can view their digital ticket and see queue status', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create([
        'user_id' => $user->id,
        'nik' => '3271012345678901',
    ]);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
    ]);

    $queue = Queue::factory()->create([
        'visit_id' => $visit->id,
        'queue_code' => 'POL-261110-001',
        'status' => QueueStatus::Waiting,
        'qr_token' => 'TKT-TEST-TOKEN',
    ]);

    $response = $this->actingAs($user)->get("/akun/tiket/{$visit->id}");
    $response->assertOk();
    $response->assertSee('POL-261110-001');
    $response->assertSeeLivewire(DigitalTicket::class);
});

test('patient can cancel their waiting queue from ticket or history', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create([
        'user_id' => $user->id,
    ]);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'status' => VisitStatus::Registered,
    ]);

    $queue = Queue::factory()->create([
        'visit_id' => $visit->id,
        'status' => QueueStatus::Waiting,
    ]);

    Livewire::actingAs($user)
        ->test(DigitalTicket::class, ['visitId' => $visit->id])
        ->call('cancelQueue')
        ->assertSee('Antrean berhasil dibatalkan');

    expect($queue->fresh()->status)->toBe(QueueStatus::Cancelled);
    expect($visit->fresh()->status)->toBe(VisitStatus::Cancelled);
});
