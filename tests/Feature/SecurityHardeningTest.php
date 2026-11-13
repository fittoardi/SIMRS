<?php

use App\Enums\PaymentStatus;
use App\Filament\Resources\MedicalRecordAccessLogs\MedicalRecordAccessLogResource;
use App\Filament\Resources\MedicalRecords\MedicalRecordResource;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordAccessLog;
use App\Models\Patient;
use App\Models\PatientFamilyMember;
use App\Models\Prescription;
use App\Models\Queue;
use App\Models\User;
use App\Models\Visit;
use App\Services\MedicalAuditLogger;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'nurse', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'pharmacist', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admission', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
});

test('http responses contain hardened security headers', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
});

test('medical audit logger records access log accurately for patient and doctor', function () {
    $doctor = User::factory()->create(['name' => 'dr. Hendra Sp.A']);
    $doctor->assignRole('doctor');

    $patient = Patient::factory()->create([
        'name' => 'Ananda Rifqi',
        'medical_record_number' => 'RM-202611-0099',
    ]);

    $visit = Visit::factory()->create(['patient_id' => $patient->id]);
    $medicalRecord = MedicalRecord::factory()->create(['visit_id' => $visit->id]);

    $log = MedicalAuditLogger::log(
        patient: $patient,
        action: 'view',
        medicalRecord: $medicalRecord,
        user: $doctor,
        notes: 'Pemeriksaan lanjutan poliklinik anak'
    );

    expect($log)->toBeInstanceOf(MedicalRecordAccessLog::class)
        ->and($log->user_id)->toBe($doctor->id)
        ->and($log->user_name)->toBe('dr. Hendra Sp.A')
        ->and($log->patient_id)->toBe($patient->id)
        ->and($log->medical_record_number)->toBe('RM-202611-0099')
        ->and($log->action)->toBe('view')
        ->and($log->notes)->toBe('Pemeriksaan lanjutan poliklinik anak');
});

test('super admin can access medical record audit logs in filament', function () {
    $admin = User::factory()->create();
    $admin->assignRole('super_admin');

    $response = $this->actingAs($admin)->get('/admin/medical-record-access-logs');

    $response->assertOk();
    $response->assertSee('Audit Log Akses Rekam Medis');
});

test('doctor or patient cannot access medical record audit logs in filament', function () {
    $doctor = User::factory()->create();
    $doctor->assignRole('doctor');

    $response = $this->actingAs($doctor)->get('/admin/medical-record-access-logs');

    expect($response->status())->toBeIn([403, 302]);
});

test('custom 404 page is displayed when navigating to invalid route', function () {
    $response = $this->get('/halaman-yang-tidak-pernah-ada-12345');

    $response->assertNotFound();
    $response->assertSee('Halaman Tidak Ditemukan');
    $response->assertSee('Ke Beranda Utama');
});

test('idor protection: patient A cannot view ticket of patient B', function () {
    $userA = User::factory()->create();
    $userA->assignRole('patient');
    $patientA = Patient::factory()->create(['user_id' => $userA->id]);

    $userB = User::factory()->create();
    $userB->assignRole('patient');
    $patientB = Patient::factory()->create(['user_id' => $userB->id]);

    $visitB = Visit::factory()->create(['patient_id' => $patientB->id]);
    Queue::factory()->create(['visit_id' => $visitB->id]);

    // Pasien A mencoba mengakses tiket Pasien B
    $response = $this->actingAs($userA)->get("/akun/tiket/{$visitB->id}");

    $response->assertForbidden();
});

test('patient can view their own ticket and family member ticket', function () {
    $user = User::factory()->create();
    $user->assignRole('patient');
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    $familyPatient = Patient::factory()->create(['user_id' => null]);
    PatientFamilyMember::create([
        'user_id' => $user->id,
        'patient_id' => $familyPatient->id,
        'relationship' => 'anak',
    ]);

    $ownVisit = Visit::factory()->create(['patient_id' => $patient->id]);
    Queue::factory()->create(['visit_id' => $ownVisit->id]);

    $familyVisit = Visit::factory()->create(['patient_id' => $familyPatient->id]);
    Queue::factory()->create(['visit_id' => $familyVisit->id]);

    // Tiket sendiri harus OK
    $this->actingAs($user)->get("/akun/tiket/{$ownVisit->id}")->assertOk();

    // Tiket keluarga harus OK
    $this->actingAs($user)->get("/akun/tiket/{$familyVisit->id}")->assertOk();
});

test('idor protection: patient A cannot view invoice of patient B', function () {
    $userA = User::factory()->create();
    $userA->assignRole('patient');
    $patientA = Patient::factory()->create(['user_id' => $userA->id]);

    $userB = User::factory()->create();
    $userB->assignRole('patient');
    $patientB = Patient::factory()->create(['user_id' => $userB->id]);

    $visitB = Visit::factory()->create(['patient_id' => $patientB->id]);
    $invoiceB = Invoice::create([
        'number' => 'INV-202611-TEST',
        'visit_id' => $visitB->id,
        'patient_id' => $patientB->id,
        'total' => 250000,
        'status' => PaymentStatus::Unpaid,
    ]);

    $response = $this->actingAs($userA)->get("/akun/tagihan/{$invoiceB->id}");

    $response->assertForbidden();
});

test('patient cannot access pharmacy etiket endpoint', function () {
    $patientUser = User::factory()->create();
    $patientUser->assignRole('patient');

    $patient = Patient::factory()->create(['user_id' => $patientUser->id]);
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);
    $doctor = Doctor::factory()->create();

    $prescription = Prescription::create([
        'visit_id' => $visit->id,
        'doctor_id' => $doctor->id,
        'prescription_number' => 'RX-202611-TEST',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($patientUser)->get("/farmasi/etiket/{$prescription->id}");

    $response->assertForbidden();
});

test('cashier cannot view medical records and audit log is immutable', function () {
    $cashier = User::factory()->create();
    $cashier->assignRole('cashier');

    $doctor = User::factory()->create();
    $doctor->assignRole('doctor');

    $this->actingAs($cashier);
    expect(MedicalRecordResource::canViewAny())->toBeFalse();

    $this->actingAs($doctor);
    expect(MedicalRecordResource::canViewAny())->toBeTrue();

    // Audit logs immutability check
    expect(MedicalRecordAccessLogResource::canCreate())->toBeFalse()
        ->and(MedicalRecordAccessLogResource::canEdit(new MedicalRecordAccessLog))->toBeFalse()
        ->and(MedicalRecordAccessLogResource::canDelete(new MedicalRecordAccessLog))->toBeFalse();
});
