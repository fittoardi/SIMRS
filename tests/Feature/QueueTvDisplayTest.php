<?php

use App\Enums\QueueStatus;
use App\Livewire\Public\QueueTvDisplay;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Models\Queue;
use App\Models\Visit;
use Carbon\Carbon;
use Livewire\Livewire;

test('public user can view TV queue display screen', function () {
    $response = $this->get('/layar-antrean');

    $response->assertOk();
    $response->assertSeeLivewire(QueueTvDisplay::class);
    $response->assertSee('RS Aisyah Medika');
});

test('TV display renders active polyclinics and current called queue', function () {
    $today = Carbon::today()->format('Y-m-d');

    $polyclinic = Polyclinic::factory()->create([
        'name' => 'Poli Anak',
        'code' => 'PAN',
        'is_active' => true,
    ]);

    $doctor = Doctor::factory()->create([
        'polyclinic_id' => $polyclinic->id,
        'name' => 'dr. Maya Sp.A',
        'is_active' => true,
    ]);

    $patient = Patient::factory()->create([
        'name' => 'Balita Rizky',
    ]);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
        'visit_date' => $today,
    ]);

    $queue = Queue::factory()->create([
        'visit_id' => $visit->id,
        'queue_code' => 'PAN-261110-001',
        'status' => QueueStatus::Called,
        'called_at' => now(),
        'call_count' => 1,
    ]);

    Livewire::test(QueueTvDisplay::class)
        ->assertSee('PAN-261110-001')
        ->assertSee('Balita Rizky')
        ->assertSee('Poli Anak')
        ->assertDispatched('play-queue-call');
});
