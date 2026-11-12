<?php

use App\Enums\QueueStatus;
use App\Enums\VisitStatus;
use App\Livewire\Public\GuestBooking;
use App\Livewire\Public\PublicTicket;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Models\Queue;
use App\Models\Visit;
use Carbon\Carbon;
use Livewire\Livewire;

test('guest can access public booking online page without authentication', function () {
    $response = $this->get('/booking-online');

    $response->assertOk();
    $response->assertSee('Booking Antrean Online');
});

test('guest patient can complete 4-step booking without login and receives public qr ticket', function () {
    $polyclinic = Polyclinic::factory()->create(['name' => 'Poli Umum', 'code' => 'PU']);
    $doctor = Doctor::factory()->create(['polyclinic_id' => $polyclinic->id, 'name' => 'dr. Aisyah Putri']);

    $tomorrow = Carbon::tomorrow();
    $dayOfWeek = $tomorrow->dayOfWeekIso;

    $schedule = DoctorSchedule::factory()->create([
        'doctor_id' => $doctor->id,
        'day_of_week' => $dayOfWeek,
        'start_time' => '08:00:00',
        'end_time' => '12:00:00',
        'max_quota' => 20,
        'is_active' => true,
    ]);

    Livewire::test(GuestBooking::class)
        ->set('name', 'Rina Wijaya')
        ->set('nik', '3171012345678888')
        ->set('phone', '081234567890')
        ->set('gender', 'P')
        ->set('birthDate', '1995-05-12')
        ->call('nextStep')
        ->assertSet('currentStep', 2)
        ->set('selectedPolyclinicId', $polyclinic->id)
        ->set('selectedDoctorId', $doctor->id)
        ->call('nextStep')
        ->assertSet('currentStep', 3)
        ->set('selectedDate', $tomorrow->format('Y-m-d'))
        ->set('selectedScheduleId', $schedule->id)
        ->call('nextStep')
        ->assertSet('currentStep', 4)
        ->set('paymentType', 'general')
        ->set('complaint', 'Demam sejak kemarin')
        ->call('confirmBooking')
        ->assertHasNoErrors();

    $patient = Patient::where('nik', '3171012345678888')->first();
    expect($patient)->not->toBeNull();
    expect($patient->user_id)->toBeNull();
    expect($patient->medical_record_number)->toStartWith('RM-');

    $visit = Visit::where('patient_id', $patient->id)->first();
    expect($visit)->not->toBeNull();
    expect($visit->status)->toBe(VisitStatus::Registered);

    $queue = Queue::where('visit_id', $visit->id)->first();
    expect($queue)->not->toBeNull();
    expect($queue->queue_code)->toContain('PU-');
    expect($queue->status)->toBe(QueueStatus::Waiting);
});

test('guest booking reuses existing patient record when nik already exists', function () {
    $existingPatient = Patient::factory()->create([
        'user_id' => null,
        'nik' => '3171012345677777',
        'medical_record_number' => 'RM-202611-0099',
        'name' => 'Budi Santoso',
    ]);

    $polyclinic = Polyclinic::factory()->create(['code' => 'PD']);
    $doctor = Doctor::factory()->create(['polyclinic_id' => $polyclinic->id]);
    $tomorrow = Carbon::tomorrow();

    $schedule = DoctorSchedule::factory()->create([
        'doctor_id' => $doctor->id,
        'day_of_week' => $tomorrow->dayOfWeekIso,
        'max_quota' => 10,
        'is_active' => true,
    ]);

    Livewire::test(GuestBooking::class)
        ->set('name', 'Budi Santoso')
        ->set('nik', '3171012345677777')
        ->set('phone', '081299990000')
        ->set('gender', 'L')
        ->set('birthDate', '1990-01-01')
        ->call('nextStep')
        ->set('selectedPolyclinicId', $polyclinic->id)
        ->set('selectedDoctorId', $doctor->id)
        ->call('nextStep')
        ->set('selectedDate', $tomorrow->format('Y-m-d'))
        ->set('selectedScheduleId', $schedule->id)
        ->call('nextStep')
        ->call('confirmBooking');

    expect(Patient::where('nik', '3171012345677777')->count())->toBe(1);
    $visit = Visit::where('patient_id', $existingPatient->id)->first();
    expect($visit)->not->toBeNull();
});

test('guest booking prevents duplicate active booking for same nik, doctor, and date', function () {
    $patient = Patient::factory()->create(['nik' => '3171012345676666']);
    $polyclinic = Polyclinic::factory()->create();
    $doctor = Doctor::factory()->create(['polyclinic_id' => $polyclinic->id]);
    $tomorrow = Carbon::tomorrow();

    $schedule = DoctorSchedule::factory()->create([
        'doctor_id' => $doctor->id,
        'day_of_week' => $tomorrow->dayOfWeekIso,
        'max_quota' => 10,
        'is_active' => true,
    ]);

    Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
        'visit_date' => $tomorrow->format('Y-m-d'),
        'status' => VisitStatus::Registered,
    ]);

    Livewire::test(GuestBooking::class)
        ->set('name', $patient->name)
        ->set('nik', '3171012345676666')
        ->set('phone', '081234567890')
        ->set('gender', 'L')
        ->set('birthDate', '1990-01-01')
        ->call('nextStep')
        ->set('selectedPolyclinicId', $polyclinic->id)
        ->set('selectedDoctorId', $doctor->id)
        ->call('nextStep')
        ->set('selectedDate', $tomorrow->format('Y-m-d'))
        ->set('selectedScheduleId', $schedule->id)
        ->call('nextStep')
        ->assertHasErrors(['selectedScheduleId']);
});

test('public ticket page displays masked nik, queue code, and allows cancelling while waiting', function () {
    $patient = Patient::factory()->create([
        'name' => 'Dewi Sartika',
        'nik' => '3171012345671234',
    ]);
    $polyclinic = Polyclinic::factory()->create();
    $doctor = Doctor::factory()->create(['polyclinic_id' => $polyclinic->id]);
    $schedule = DoctorSchedule::factory()->create(['doctor_id' => $doctor->id]);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
        'visit_date' => Carbon::today()->format('Y-m-d'),
        'status' => VisitStatus::Registered,
    ]);

    $queue = Queue::factory()->create([
        'visit_id' => $visit->id,
        'doctor_schedule_id' => $schedule->id,
        'queue_code' => 'PU-261112-001',
        'qr_token' => 'TKT-TESTTOKEN1234567890ABCDEF',
        'status' => QueueStatus::Waiting,
    ]);

    $response = $this->get("/tiket-antrean/{$queue->qr_token}");
    $response->assertOk();
    $response->assertSee('Dewi Sartika');
    $response->assertSee('PU-261112-001');
    $response->assertSee('3171••••••••1234');
    $response->assertDontSee('3171012345671234');

    Livewire::test(PublicTicket::class, ['token' => $queue->qr_token])
        ->call('cancelQueue');

    expect($queue->fresh()->status)->toBe(QueueStatus::Cancelled);
    expect($visit->fresh()->status)->toBe(VisitStatus::Cancelled);
});

test('public ticket page returns 404 for unknown token', function () {
    $this->get('/tiket-antrean/INVALID-TOKEN-XYZ')->assertNotFound();
});

