<?php

use App\Enums\PaymentStatus;
use App\Enums\VisitStatus;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Models\Visit;

beforeEach(function () {
    config(['services.midtrans.server_key' => 'midtrans-test-secret-key-123']);
    config(['services.midtrans.callback_token' => 'xendit-generic-secret-token-abc']);
});

test('webhook midtrans with valid signature successfully settles invoice and completes visit', function () {
    $patient = Patient::factory()->create(['phone' => '081234567890']);
    $polyclinic = Polyclinic::factory()->create();
    $doctor = Doctor::factory()->create(['polyclinic_id' => $polyclinic->id]);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
        'status' => VisitStatus::Billing,
    ]);

    $invoice = Invoice::create([
        'visit_id' => $visit->id,
        'patient_id' => $patient->id,
        'number' => 'INV-'.date('Ymd').'-0999',
        'subtotal' => 150000,
        'discount' => 0,
        'total' => 150000,
        'status' => PaymentStatus::Unpaid,
        'payment_status' => 'unpaid',
    ]);

    $orderId = $invoice->number;
    $statusCode = '200';
    $grossAmount = number_format((float) $invoice->total, 2, '.', '');
    $serverKey = config('services.midtrans.server_key');
    $signature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

    $payload = [
        'order_id' => $orderId,
        'status_code' => $statusCode,
        'gross_amount' => $grossAmount,
        'signature_key' => $signature,
        'transaction_status' => 'settlement',
        'payment_type' => 'qris',
        'transaction_id' => 'MID-TRX-'.uniqid(),
        'fraud_status' => 'accept',
    ];

    $response = $this->postJson('/webhooks/payment', $payload);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => "Invoice {$orderId} settled successfully.",
        ]);

    $invoice->refresh();
    expect($invoice->status)->toBe(PaymentStatus::Paid);
    expect($invoice->payments()->count())->toBe(1);

    $visit->refresh();
    expect($visit->status)->toBe(VisitStatus::Completed);
});

test('webhook midtrans is idempotent and returns 200 already_processed without duplicate payments', function () {
    $patient = Patient::factory()->create(['phone' => '081234567890']);
    $polyclinic = Polyclinic::factory()->create();
    $doctor = Doctor::factory()->create(['polyclinic_id' => $polyclinic->id]);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
        'status' => VisitStatus::Completed,
    ]);

    $invoice = Invoice::create([
        'visit_id' => $visit->id,
        'patient_id' => $patient->id,
        'number' => 'INV-'.date('Ymd').'-0888',
        'subtotal' => 200000,
        'discount' => 0,
        'total' => 200000,
        'status' => PaymentStatus::Paid,
        'payment_status' => 'paid',
    ]);

    // Create 1 initial payment
    $invoice->payments()->create([
        'provider' => 'midtrans',
        'provider_ref' => 'MID-INITIAL-1',
        'method' => 'qris',
        'amount' => 200000,
        'status' => PaymentStatus::Paid,
        'paid_at' => now(),
    ]);

    $orderId = $invoice->number;
    $statusCode = '200';
    $grossAmount = number_format((float) $invoice->total, 2, '.', '');
    $serverKey = config('services.midtrans.server_key');
    $signature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

    $payload = [
        'order_id' => $orderId,
        'status_code' => $statusCode,
        'gross_amount' => $grossAmount,
        'signature_key' => $signature,
        'transaction_status' => 'settlement',
        'payment_type' => 'qris',
        'transaction_id' => 'MID-TRX-RETRY',
        'fraud_status' => 'accept',
    ];

    $response = $this->postJson('/webhooks/payment', $payload);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'already_processed',
            'message' => "Invoice {$orderId} is already marked as PAID.",
        ]);

    // Payment record must remain exactly 1 (no duplicate charge recorded)
    expect($invoice->payments()->count())->toBe(1);
});

test('webhook midtrans with invalid signature key is rejected with 403', function () {
    $response = $this->postJson('/webhooks/payment', [
        'order_id' => 'INV-20261114-0001',
        'status_code' => '200',
        'gross_amount' => '100000.00',
        'signature_key' => 'invalid-fabricated-signature-hash',
        'transaction_status' => 'settlement',
    ]);

    $response->assertStatus(403)
        ->assertJson([
            'status' => 'error',
            'message' => 'Invalid signature key.',
        ]);
});

test('webhook with missing order_id returns 400 bad request', function () {
    $serverKey = config('services.midtrans.server_key');
    $signature = hash('sha512', ''.'200'.'100000.00'.$serverKey);

    $response = $this->postJson('/webhooks/payment', [
        'status_code' => '200',
        'gross_amount' => '100000.00',
        'signature_key' => $signature,
        'transaction_status' => 'settlement',
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'status' => 'error',
            'message' => 'Missing order_id / external_id in payload.',
        ]);
});

test('webhook with generic callback token header successfully authenticates', function () {
    $patient = Patient::factory()->create();
    $polyclinic = Polyclinic::factory()->create();
    $doctor = Doctor::factory()->create(['polyclinic_id' => $polyclinic->id]);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
        'status' => VisitStatus::Billing,
    ]);

    $invoice = Invoice::create([
        'visit_id' => $visit->id,
        'patient_id' => $patient->id,
        'number' => 'INV-'.date('Ymd').'-0777',
        'subtotal' => 75000,
        'discount' => 0,
        'total' => 75000,
        'status' => PaymentStatus::Unpaid,
        'payment_status' => 'unpaid',
    ]);

    $response = $this->withHeaders([
        'X-Callback-Token' => 'xendit-generic-secret-token-abc',
    ])->postJson('/webhooks/payment', [
        'order_id' => $invoice->number,
        'transaction_status' => 'paid',
        'payment_type' => 'bank_transfer',
        'transaction_id' => 'GENERIC-TRX-101',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
        ]);

    $invoice->refresh();
    expect($invoice->status)->toBe(PaymentStatus::Paid);
});
