<?php

use App\Enums\QueueStatus;
use App\Livewire\Public\KioskCheckIn;
use App\Models\Patient;
use App\Models\Queue;
use App\Models\Visit;
use Carbon\Carbon;
use Livewire\Livewire;

test('public user can view kiosk check-in page', function () {
    $response = $this->get('/check-in');

    $response->assertOk();
    $response->assertSeeLivewire(KioskCheckIn::class);
});

test('patient can check-in by queue code and update checked_in_at', function () {
    $today = Carbon::today()->format('Y-m-d');
    $patient = Patient::factory()->create([
        'name' => 'Siti Nurhaliza',
        'nik' => '3271099988887777',
    ]);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'visit_date' => $today,
    ]);

    $queue = Queue::factory()->create([
        'visit_id' => $visit->id,
        'queue_code' => 'POL-261110-099',
        'qr_token' => 'TKT-CHECKIN-TOKEN',
        'status' => QueueStatus::Waiting,
        'checked_in_at' => null,
    ]);

    Livewire::test(KioskCheckIn::class)
        ->set('inputCode', 'POL-261110-099')
        ->call('checkInByCode')
        ->assertHasNoErrors()
        ->assertSee('Check-In Mandiri Berhasil!')
        ->assertSee('POL-261110-099')
        ->assertSee('Siti Nurhaliza');

    $queue->refresh();
    expect($queue->checked_in_at)->not->toBeNull();
    expect($queue->status)->toBe(QueueStatus::Waiting);
});

test('patient can check-in by 16 digit NIK', function () {
    $today = Carbon::today()->format('Y-m-d');
    $patient = Patient::factory()->create([
        'name' => 'Bambang Sudirman',
        'nik' => '3171011122223333',
    ]);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'visit_date' => $today,
    ]);

    $queue = Queue::factory()->create([
        'visit_id' => $visit->id,
        'queue_code' => 'PPD-261110-005',
        'status' => QueueStatus::Waiting,
        'checked_in_at' => null,
    ]);

    Livewire::test(KioskCheckIn::class)
        ->set('mode', 'nik')
        ->set('inputNik', '3171011122223333')
        ->call('checkInByNik')
        ->assertHasNoErrors()
        ->assertSee('Check-In Mandiri Berhasil!')
        ->assertSee('PPD-261110-005')
        ->assertSee('Bambang Sudirman');

    $queue->refresh();
    expect($queue->checked_in_at)->not->toBeNull();
});

test('kiosk shows error message if ticket is not found for today', function () {
    Livewire::test(KioskCheckIn::class)
        ->set('inputCode', 'INVALID-CODE-999')
        ->call('checkInByCode')
        ->assertSee('Jadwal kunjungan hari ini tidak ditemukan');
});

