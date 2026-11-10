<?php

use App\Enums\QueueStatus;
use App\Enums\VisitStatus;
use App\Filament\Pages\LoketPanggilan;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Models\Queue;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admission', 'guard_name' => 'web']);
});

test('admission staff can open loket panggilan page in filament', function () {
    $staff = User::factory()->create();
    $staff->assignRole('admission');

    $response = $this->actingAs($staff)->get('/admin/loket-panggilan');

    $response->assertOk();
    $response->assertSee('Loket Panggilan Antrean');
});

test('staff can call, serve, and complete a queue', function () {
    $staff = User::factory()->create();
    $staff->assignRole('super_admin');

    $today = Carbon::today()->format('Y-m-d');
    $polyclinic = Polyclinic::factory()->create(['name' => 'Poli Penyakit Dalam', 'code' => 'PPD']);
    $doctor = Doctor::factory()->create(['polyclinic_id' => $polyclinic->id]);
    $patient = Patient::factory()->create(['name' => 'Hasan Basri']);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
        'visit_date' => $today,
        'status' => VisitStatus::Registered,
    ]);

    $queue = Queue::factory()->create([
        'visit_id' => $visit->id,
        'queue_code' => 'PPD-261110-001',
        'status' => QueueStatus::Waiting,
        'call_count' => 0,
    ]);

    // 1. Panggil
    Livewire::actingAs($staff)
        ->test(LoketPanggilan::class)
        ->set('selectedPolyclinicId', $polyclinic->id)
        ->call('callQueue', $queue->id);

    $queue->refresh();
    expect($queue->status)->toBe(QueueStatus::Called);
    expect($queue->call_count)->toBe(1);
    expect($queue->called_at)->not->toBeNull();

    // 2. Mulai Layani
    Livewire::actingAs($staff)
        ->test(LoketPanggilan::class)
        ->call('serveQueue', $queue->id);

    $queue->refresh();
    expect($queue->status)->toBe(QueueStatus::Serving);
    expect($queue->served_at)->not->toBeNull();
    expect($visit->fresh()->status)->toBe(VisitStatus::InExamination);

    // 3. Selesai
    Livewire::actingAs($staff)
        ->test(LoketPanggilan::class)
        ->call('completeQueue', $queue->id);

    $queue->refresh();
    expect($queue->status)->toBe(QueueStatus::Completed);
    expect($queue->completed_at)->not->toBeNull();
    expect($visit->fresh()->status)->toBe(VisitStatus::Completed);
});

test('staff can register walk-in patient at admission counter', function () {
    $staff = User::factory()->create();
    $staff->assignRole('admission');

    $polyclinic = Polyclinic::factory()->create(['name' => 'Poli Umum', 'code' => 'PUM']);
    $doctor = Doctor::factory()->create(['polyclinic_id' => $polyclinic->id]);

    $today = Carbon::today();
    DoctorSchedule::factory()->create([
        'doctor_id' => $doctor->id,
        'day_of_week' => (int) $today->format('N'),
        'is_active' => true,
    ]);

    Livewire::actingAs($staff)
        ->test(LoketPanggilan::class)
        ->set('walkInPolyclinicId', $polyclinic->id)
        ->set('walkInDoctorId', $doctor->id)
        ->set('walkInName', 'Joko Susanto')
        ->set('walkInNik', '3271012345678999')
        ->set('walkInGender', 'L')
        ->set('walkInBirthDate', '1985-06-12')
        ->set('walkInPhone', '081298765432')
        ->call('registerWalkInPatient')
        ->assertHasNoErrors();

    $patient = Patient::where('nik', '3271012345678999')->first();
    expect($patient)->not->toBeNull();
    expect($patient->name)->toBe('Joko Susanto');

    $visit = Visit::where('patient_id', $patient->id)->first();
    expect($visit)->not->toBeNull();
    expect($visit->polyclinic_id)->toBe($polyclinic->id);

    $queue = Queue::where('visit_id', $visit->id)->first();
    expect($queue)->not->toBeNull();
    expect($queue->queue_code)->toContain('PUM-');
    expect($queue->checked_in_at)->not->toBeNull();
});
