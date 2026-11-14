<?php

use App\Enums\QueueStatus;
use App\Enums\VisitStatus;
use App\Integrations\WhatsApp\WhatsAppProvider;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Models\Queue;
use App\Models\Visit;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;

test('artisan hospital:send-reminders sends whatsapp reminders for tomorrow visits', function () {
    $mockProvider = Mockery::mock(WhatsAppProvider::class);
    $mockProvider->shouldReceive('sendMessage')
        ->once()
        ->withArgs(function ($phone, $message) {
            return $phone === '085608543495'
                && str_contains($message, 'PENGINGAT JADWAL KUNJUNGAN (H-1)')
                && str_contains($message, 'RS AISYAH MEDIKA');
        })
        ->andReturn(['success' => true]);

    $this->app->instance(WhatsAppNotificationService::class, new WhatsAppNotificationService($mockProvider));

    $tomorrow = Carbon::tomorrow()->toDateString();
    $patient = Patient::factory()->create([
        'name' => 'Budi Santoso',
        'phone' => '085608543495',
    ]);

    $polyclinic = Polyclinic::factory()->create(['name' => 'Poli Penyakit Dalam']);
    $doctor = Doctor::factory()->create(['polyclinic_id' => $polyclinic->id]);
    $schedule = DoctorSchedule::factory()->create(['doctor_id' => $doctor->id]);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
        'visit_date' => $tomorrow,
        'status' => VisitStatus::Registered,
    ]);

    $queue = Queue::factory()->create([
        'visit_id' => $visit->id,
        'doctor_schedule_id' => $schedule->id,
        'queue_code' => 'DAL-261115-001',
        'status' => QueueStatus::Waiting,
        'reminder_sent_at' => null,
    ]);

    $this->artisan('hospital:send-reminders')
        ->expectsOutputToContain('Ditemukan 1 antrean kunjungan')
        ->expectsOutputToContain('Terkirim: 1')
        ->assertSuccessful();

    $queue->refresh();
    expect($queue->reminder_sent_at)->not->toBeNull();
});

test('artisan hospital:send-reminders skips visits that already received reminders', function () {
    $mockProvider = Mockery::mock(WhatsAppProvider::class);
    $mockProvider->shouldNotReceive('sendMessage');

    $this->app->instance(WhatsAppNotificationService::class, new WhatsAppNotificationService($mockProvider));

    $tomorrow = Carbon::tomorrow()->toDateString();
    $patient = Patient::factory()->create(['phone' => '085608543495']);
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'visit_date' => $tomorrow]);

    Queue::factory()->create([
        'visit_id' => $visit->id,
        'reminder_sent_at' => now()->subHours(2), // Already sent!
    ]);

    $this->artisan('hospital:send-reminders')
        ->expectsOutputToContain('Tidak ada antrean kunjungan')
        ->assertSuccessful();
});

test('artisan hospital:send-reminders skips cancelled visits and queues', function () {
    $mockProvider = Mockery::mock(WhatsAppProvider::class);
    $mockProvider->shouldNotReceive('sendMessage');

    $this->app->instance(WhatsAppNotificationService::class, new WhatsAppNotificationService($mockProvider));

    $tomorrow = Carbon::tomorrow()->toDateString();
    $patient = Patient::factory()->create(['phone' => '085608543495']);

    $cancelledVisit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'visit_date' => $tomorrow,
        'status' => VisitStatus::Cancelled,
    ]);

    Queue::factory()->create([
        'visit_id' => $cancelledVisit->id,
        'status' => QueueStatus::Waiting,
        'reminder_sent_at' => null,
    ]);

    $this->artisan('hospital:send-reminders')
        ->expectsOutputToContain('Tidak ada antrean kunjungan')
        ->assertSuccessful();
});

test('artisan hospital:send-reminders supports dry run without modifying database', function () {
    $mockProvider = Mockery::mock(WhatsAppProvider::class);
    $mockProvider->shouldNotReceive('sendMessage');

    $this->app->instance(WhatsAppNotificationService::class, new WhatsAppNotificationService($mockProvider));

    $tomorrow = Carbon::tomorrow()->toDateString();
    $patient = Patient::factory()->create(['phone' => '085608543495']);
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'visit_date' => $tomorrow]);

    $queue = Queue::factory()->create([
        'visit_id' => $visit->id,
        'reminder_sent_at' => null,
    ]);

    $this->artisan('hospital:send-reminders --dry-run')
        ->expectsOutputToContain('MODE DRY-RUN: Pesan WhatsApp tidak akan dikirim secara riil.')
        ->expectsOutputToContain('Siap Kirim (Dry-Run)')
        ->assertSuccessful();

    $queue->refresh();
    expect($queue->reminder_sent_at)->toBeNull();
});

test('artisan hospital:send-reminders handles custom date option', function () {
    $mockProvider = Mockery::mock(WhatsAppProvider::class);
    $mockProvider->shouldReceive('sendMessage')->once()->andReturn(['success' => true]);

    $this->app->instance(WhatsAppNotificationService::class, new WhatsAppNotificationService($mockProvider));

    $customDate = Carbon::today()->addDays(3)->toDateString();
    $patient = Patient::factory()->create(['phone' => '085608543495']);
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'visit_date' => $customDate]);

    $queue = Queue::factory()->create([
        'visit_id' => $visit->id,
        'reminder_sent_at' => null,
    ]);

    $this->artisan("hospital:send-reminders --date={$customDate}")
        ->expectsOutputToContain("Target Tanggal Kunjungan: {$customDate}")
        ->expectsOutputToContain('Terkirim: 1')
        ->assertSuccessful();

    $queue->refresh();
    expect($queue->reminder_sent_at)->not->toBeNull();
});
