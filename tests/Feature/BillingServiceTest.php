<?php

use App\Enums\PaymentStatus;
use App\Enums\VisitStatus;
use App\Models\Bed;
use App\Models\Doctor;
use App\Models\MedicalAction;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Models\Prescription;
use App\Models\Room;
use App\Models\Visit;
use App\Services\BillingService;

beforeEach(function () {
    $this->billingService = app(BillingService::class);
});

test('billing service creates invoice calculating doctor fee, actions, medicines, and room', function () {
    $polyclinic = Polyclinic::factory()->create(['name' => 'Poli Umum']);
    $doctor = Doctor::factory()->create([
        'polyclinic_id' => $polyclinic->id,
        'consultation_fee' => 75000,
    ]);
    $patient = Patient::factory()->create();

    $room = Room::create([
        'code' => 'ROOM-TEST-'.uniqid(),
        'name' => 'Kamar Mawar Test',
        'ward_name' => 'Bangsal Melati',
        'class_type' => 'Kelas 1',
        'price_per_day' => 250000,
        'is_active' => true,
    ]);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
        'visit_type' => 'inpatient',
        'status' => VisitStatus::InExamination,
    ]);

    $bed = Bed::create([
        'room_id' => $room->id,
        'bed_number' => 'BED-01',
        'status' => 'occupied',
        'current_visit_id' => $visit->id,
    ]);

    // Add Medical Record with 2 actions
    $action1 = MedicalAction::factory()->create(['name' => 'Nebulizer', 'price' => 50000]);
    $action2 = MedicalAction::factory()->create(['name' => 'Injeksi Vitamin', 'price' => 35000]);

    $medicalRecord = MedicalRecord::factory()->create(['visit_id' => $visit->id, 'doctor_id' => $doctor->id]);
    $medicalRecord->actions()->create([
        'medical_action_id' => $action1->id,
        'action_name_snapshot' => $action1->name,
        'price_snapshot' => $action1->price,
        'qty' => 1,
        'subtotal' => 50000,
    ]);
    $medicalRecord->actions()->create([
        'medical_action_id' => $action2->id,
        'action_name_snapshot' => $action2->name,
        'price_snapshot' => $action2->price,
        'qty' => 2,
        'subtotal' => 70000,
    ]);

    // Add Prescription with medicine item
    $medicine = Medicine::factory()->create(['name' => 'Amoxicillin 500mg', 'price' => 15000]);
    $prescription = Prescription::factory()->create([
        'visit_id' => $visit->id,
        'doctor_id' => $doctor->id,
    ]);
    $prescription->items()->create([
        'medicine_id' => $medicine->id,
        'medicine_name_snapshot' => $medicine->name,
        'price_snapshot' => $medicine->price,
        'qty' => 10,
        'subtotal' => 150000,
        'dosage' => '3x1 sesudah makan',
    ]);

    // Expected subtotal:
    // Consultation: 75,000
    // Actions: 50,000 + 70,000 = 120,000
    // Medicine: 150,000
    // Room: 250,000
    // Total subtotal: 595,000
    // Discount: 20,000 -> Total: 575,000

    $invoice = $this->billingService->createInvoiceForVisit($visit, discount: 20000, notes: 'Diskon Hari Kesehatan');

    expect($invoice)->not->toBeNull()
        ->and((float) $invoice->subtotal)->toBe(595000.0)
        ->and((float) $invoice->discount)->toBe(20000.0)
        ->and((float) $invoice->total)->toBe(575000.0)
        ->and($invoice->status)->toBe(PaymentStatus::Unpaid)
        ->and($invoice->items)->toHaveCount(5);

    expect($visit->fresh()->status)->toBe(VisitStatus::Billing);
});

test('billing service reuses existing unpaid invoice for the visit', function () {
    $patient = Patient::factory()->create();
    $doctor = Doctor::factory()->create(['consultation_fee' => 50000]);
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $invoice1 = $this->billingService->createInvoiceForVisit($visit);
    $invoice2 = $this->billingService->createInvoiceForVisit($visit);

    expect($invoice2->id)->toBe($invoice1->id);
});

test('payWithCash validates tendered amount and rejects underpayment', function () {
    $patient = Patient::factory()->create();
    $doctor = Doctor::factory()->create(['consultation_fee' => 100000]);
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $invoice = $this->billingService->createInvoiceForVisit($visit);

    expect(fn () => $this->billingService->payWithCash($invoice, 50000))
        ->toThrow(InvalidArgumentException::class, 'Nominal uang diterima kurang dari total tagihan');
});

test('payWithCash completes payment, calculates change, and marks visit completed', function () {
    $patient = Patient::factory()->create();
    $doctor = Doctor::factory()->create(['consultation_fee' => 100000]);
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $invoice = $this->billingService->createInvoiceForVisit($visit);

    $result = $this->billingService->payWithCash(
        invoice: $invoice,
        amountTendered: 150000,
        notes: 'Uang pas diterima kasir utama',
        cashierUserId: 1
    );

    expect($result['change'])->toBe(50000.0)
        ->and($result['payment']->provider)->toBe('cash')
        ->and($result['payment']->status)->toBe(PaymentStatus::Paid)
        ->and($invoice->fresh()->status)->toBe(PaymentStatus::Paid)
        ->and($visit->fresh()->status)->toBe(VisitStatus::Completed);
});

test('payWithCash prevents double payment on paid invoice', function () {
    $patient = Patient::factory()->create();
    $doctor = Doctor::factory()->create(['consultation_fee' => 100000]);
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $invoice = $this->billingService->createInvoiceForVisit($visit);
    $this->billingService->payWithCash($invoice, 100000);

    expect(fn () => $this->billingService->payWithCash($invoice, 100000))
        ->toThrow(InvalidArgumentException::class, 'sudah berstatus LUNAS');
});

test('payWithOnlineGateway processes digital qris payment and marks invoice paid', function () {
    $patient = Patient::factory()->create();
    $doctor = Doctor::factory()->create(['consultation_fee' => 75000]);
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $invoice = $this->billingService->createInvoiceForVisit($visit);

    $payment = $this->billingService->payWithOnlineGateway(
        invoice: $invoice,
        method: 'qris',
        providerRef: 'QRIS-DEMO-998877',
        extraPayload: ['issuer' => 'BCA']
    );

    expect($payment->method)->toBe('qris')
        ->and($payment->provider_ref)->toBe('QRIS-DEMO-998877')
        ->and($payment->status)->toBe(PaymentStatus::Paid)
        ->and($invoice->fresh()->status)->toBe(PaymentStatus::Paid)
        ->and($visit->fresh()->status)->toBe(VisitStatus::Completed);
});
