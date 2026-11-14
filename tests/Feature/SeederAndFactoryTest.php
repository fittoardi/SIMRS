<?php

use App\Enums\PaymentStatus;
use App\Enums\PrescriptionStatus;
use App\Enums\QueueStatus;
use App\Enums\VisitStatus;
use App\Models\Bed;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordAction;
use App\Models\OtpVerification;
use App\Models\Patient;
use App\Models\PatientFamilyMember;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Queue;
use App\Models\Room;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('database seeder successfully populates roles, master data, and demo transactions', function () {
    $this->seed(DatabaseSeeder::class);

    // 1. Verify Demo Staff & Patient Accounts
    $expectedAccounts = [
        ['email' => 'superadmin@aisyahmedika.id', 'role' => 'super_admin'],
        ['email' => 'dokter@aisyahmedika.id', 'role' => 'doctor'],
        ['email' => 'perawat@aisyahmedika.id', 'role' => 'nurse'],
        ['email' => 'admisi@aisyahmedika.id', 'role' => 'admission'],
        ['email' => 'farmasi@aisyahmedika.id', 'role' => 'pharmacist'],
        ['email' => 'kasir@aisyahmedika.id', 'role' => 'cashier'],
        ['email' => 'pasien@aisyahmedika.id', 'role' => 'patient'],
    ];

    foreach ($expectedAccounts as $acc) {
        $user = User::where('email', $acc['email'])->first();
        expect($user)->not->toBeNull("User with email {$acc['email']} should exist.");
        expect($user->hasRole($acc['role']))->toBeTrue("User {$acc['email']} should have role {$acc['role']}.");
    }

    // 2. Verify Demo Patient Profile & Family Members
    $budi = Patient::where('medical_record_number', 'RM-202611-0001')->first();
    expect($budi)->not->toBeNull();
    expect($budi->phone)->toBe('085608543495');

    $patientUser = User::where('email', 'pasien@aisyahmedika.id')->first();
    expect(PatientFamilyMember::where('user_id', $patientUser->id)->count())->toBeGreaterThanOrEqual(3);

    // 3. Verify Active Demo Transactions
    // Scenario 1: Waiting Queue
    $queueWaiting = Queue::where('status', QueueStatus::Waiting)->first();
    expect($queueWaiting)->not->toBeNull();

    // Scenario 2: In-Examination Medical Record
    $medRecord = MedicalRecord::whereHas('visit', fn ($q) => $q->where('status', VisitStatus::InExamination))->first();
    expect($medRecord)->not->toBeNull();
    expect($medRecord->icd10_code)->toBe('K30');

    // Scenario 3: Pharmacy Preparing
    $prescription = Prescription::where('status', PrescriptionStatus::Preparing)->first();
    expect($prescription)->not->toBeNull();
    expect($prescription->items()->count())->toBeGreaterThanOrEqual(2);

    // Scenario 4: Unpaid Billing Invoice
    $invoiceUnpaid = Invoice::where('status', PaymentStatus::Unpaid)->first();
    expect($invoiceUnpaid)->not->toBeNull();
    expect((float) $invoiceUnpaid->total)->toBe(300000.0);

    // Scenario 5: Completed & Paid
    $invoicePaid = Invoice::where('status', PaymentStatus::Paid)->first();
    expect($invoicePaid)->not->toBeNull();
    expect($invoicePaid->payments()->count())->toBeGreaterThanOrEqual(1);
});

test('all models can be created using their factories', function () {
    // Room & Bed Factory
    $room = Room::factory()->create();
    expect($room->id)->not->toBeNull();

    $bed = Bed::factory()->create(['room_id' => $room->id]);
    expect($bed->id)->not->toBeNull();

    // Invoice & InvoiceItem Factory
    $invoice = Invoice::factory()->create();
    $invoiceItem = InvoiceItem::factory()->create(['invoice_id' => $invoice->id]);
    expect($invoiceItem->id)->not->toBeNull();

    // Prescription & PrescriptionItem Factory
    $prescription = Prescription::factory()->create();
    $prescriptionItem = PrescriptionItem::factory()->create(['prescription_id' => $prescription->id]);
    expect($prescriptionItem->id)->not->toBeNull();

    // PatientFamilyMember Factory
    $familyMember = PatientFamilyMember::factory()->create();
    expect($familyMember->id)->not->toBeNull();

    // MedicalRecordAction Factory
    $action = MedicalRecordAction::factory()->create();
    expect($action->id)->not->toBeNull();

    // OtpVerification Factory
    $otp = OtpVerification::factory()->create();
    expect($otp->id)->not->toBeNull();
});
