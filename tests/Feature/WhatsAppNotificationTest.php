<?php

use App\Enums\PaymentStatus;
use App\Enums\PrescriptionStatus;
use App\Enums\QueueStatus;
use App\Integrations\WhatsApp\FonnteProvider;
use App\Integrations\WhatsApp\WhatsAppProvider;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Polyclinic;
use App\Models\Prescription;
use App\Models\Queue;
use App\Models\Visit;
use App\Services\WhatsAppNotificationService;

test('fonnte provider normalizes indonesian phone numbers correctly', function () {
    $provider = new FonnteProvider('dummy-token');

    expect($provider->normalizePhone('081234567890'))->toBe('6281234567890');
    expect($provider->normalizePhone('+6281234567890'))->toBe('6281234567890');
    expect($provider->normalizePhone('6281234567890'))->toBe('6281234567890');
    expect($provider->normalizePhone('0812-3456-7890'))->toBe('6281234567890');
    expect($provider->normalizePhone('81234567890'))->toBe('6281234567890');
});

test('whatsapp service sends booking confirmation with ticket link and queue details', function () {
    $mockProvider = Mockery::mock(WhatsAppProvider::class);
    $mockProvider->shouldReceive('sendMessage')
        ->once()
        ->withArgs(function ($target, $message) {
            return $target === '081234567890'
                && str_contains($message, 'RS AISYAH MEDIKA — BUKTI PENDAFTARAN ANTREAN')
                && str_contains($message, 'POL-261114-001')
                && str_contains($message, 'tiket-antrean');
        })
        ->andReturn(['success' => true, 'id' => 'msg-123']);

    $service = new WhatsAppNotificationService($mockProvider);

    $patient = Patient::factory()->create([
        'name' => 'Ahmad Fauzi',
        'phone' => '081234567890',
    ]);
    $polyclinic = Polyclinic::factory()->create(['name' => 'Poli Penyakit Dalam']);
    $doctor = Doctor::factory()->create(['name' => 'dr. Budi Santoso, Sp.PD', 'polyclinic_id' => $polyclinic->id]);
    $schedule = DoctorSchedule::factory()->create([
        'doctor_id' => $doctor->id,
        'room_name' => 'Ruang 101',
        'start_time' => '08:00:00',
    ]);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
        'visit_date' => '2026-11-14',
    ]);

    $queue = Queue::create([
        'visit_id' => $visit->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
        'doctor_schedule_id' => $schedule->id,
        'queue_number' => 1,
        'queue_code' => 'POL-261114-001',
        'qr_token' => 'qr-token-test-12345',
        'status' => QueueStatus::Waiting,
    ]);

    $sent = $service->sendBookingConfirmation($visit, $queue);

    expect($sent)->toBeTrue();
});

test('whatsapp service sends queue called notification with clinic and room location', function () {
    $mockProvider = Mockery::mock(WhatsAppProvider::class);
    $mockProvider->shouldReceive('sendMessage')
        ->once()
        ->withArgs(function ($target, $message) {
            return $target === '085712345678'
                && str_contains($message, 'PANGGILAN ANTREAN')
                && str_contains($message, 'UMU-261114-005')
                && str_contains($message, 'Ruang Melati 2');
        })
        ->andReturn(['success' => true]);

    $service = new WhatsAppNotificationService($mockProvider);

    $patient = Patient::factory()->create([
        'name' => 'Siti Aminah',
        'phone' => '085712345678',
    ]);
    $polyclinic = Polyclinic::factory()->create(['name' => 'Poli Umum']);
    $doctor = Doctor::factory()->create(['name' => 'dr. Siti Rahma', 'polyclinic_id' => $polyclinic->id]);
    $schedule = DoctorSchedule::factory()->create([
        'doctor_id' => $doctor->id,
        'room_name' => 'Ruang Melati 2',
    ]);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
    ]);

    $queue = Queue::create([
        'visit_id' => $visit->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
        'doctor_schedule_id' => $schedule->id,
        'queue_number' => 5,
        'queue_code' => 'UMU-261114-005',
        'qr_token' => 'qr-token-555',
        'status' => QueueStatus::Called,
    ]);

    $sent = $service->sendQueueCalled($queue);

    expect($sent)->toBeTrue();
});

test('whatsapp service sends prescription ready notification', function () {
    $mockProvider = Mockery::mock(WhatsAppProvider::class);
    $mockProvider->shouldReceive('sendMessage')
        ->once()
        ->withArgs(function ($target, $message) {
            return $target === '081399887766'
                && str_contains($message, 'FARMASI RS AISYAH MEDIKA — RESEP OBAT SELESAI')
                && str_contains($message, 'RX-');
        })
        ->andReturn(['success' => true]);

    $service = new WhatsAppNotificationService($mockProvider);

    $patient = Patient::factory()->create([
        'name' => 'Hendra Gunawan',
        'phone' => '081399887766',
    ]);
    $doctor = Doctor::factory()->create(['name' => 'dr. Farhan, Sp.A']);
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $prescription = Prescription::create([
        'visit_id' => $visit->id,
        'doctor_id' => $doctor->id,
        'status' => PrescriptionStatus::Ready,
    ]);

    $sent = $service->sendPrescriptionReady($prescription);

    expect($sent)->toBeTrue();
});

test('whatsapp service sends payment success receipt with total and receipt link', function () {
    $mockProvider = Mockery::mock(WhatsAppProvider::class);
    $mockProvider->shouldReceive('sendMessage')
        ->once()
        ->withArgs(function ($target, $message) {
            return $target === '082100001111'
                && str_contains($message, 'BUKTI PEMBAYARAN LUNAS')
                && str_contains($message, 'INV-20261114-5555')
                && str_contains($message, 'Rp 175.000')
                && str_contains($message, 'kwitansi');
        })
        ->andReturn(['success' => true]);

    $service = new WhatsAppNotificationService($mockProvider);

    $patient = Patient::factory()->create([
        'name' => 'Dewi Lestari',
        'phone' => '082100001111',
    ]);
    $polyclinic = Polyclinic::factory()->create(['name' => 'Poli Gigi']);
    $doctor = Doctor::factory()->create(['name' => 'drg. Maya', 'polyclinic_id' => $polyclinic->id]);
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
    ]);

    $invoice = Invoice::create([
        'visit_id' => $visit->id,
        'patient_id' => $patient->id,
        'number' => 'INV-20261114-5555',
        'subtotal' => 175000,
        'discount' => 0,
        'total' => 175000,
        'status' => PaymentStatus::Paid,
        'payment_status' => 'paid',
    ]);

    $payment = Payment::create([
        'invoice_id' => $invoice->id,
        'provider' => 'midtrans',
        'provider_ref' => 'MID-QRIS-999',
        'method' => 'qris',
        'amount' => 175000,
        'status' => PaymentStatus::Paid,
        'paid_at' => now(),
    ]);

    $sent = $service->sendPaymentSuccess($invoice, $payment);

    expect($sent)->toBeTrue();
});

test('whatsapp service skips safely when patient has no phone number without error', function () {
    $mockProvider = Mockery::mock(WhatsAppProvider::class);
    $mockProvider->shouldNotReceive('sendMessage');

    $service = new WhatsAppNotificationService($mockProvider);

    $patient = Patient::factory()->create(['phone' => null]);
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);
    $queue = Queue::create([
        'visit_id' => $visit->id,
        'queue_number' => 1,
        'queue_code' => 'TEST-001',
        'qr_token' => 'test-token',
        'status' => QueueStatus::Waiting,
    ]);

    $sent = $service->sendBookingConfirmation($visit, $queue);

    expect($sent)->toBeFalse();
});
