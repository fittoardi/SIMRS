<?php

use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Models\SickLeaveCertificate;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
});

test('certificate number and verification token are generated correctly', function () {
    $certNumber = SickLeaveCertificate::generateCertificateNumber();
    $token = SickLeaveCertificate::generateVerificationToken();

    expect($certNumber)->toStartWith('SK/'.date('Ym').'/');
    expect($token)->toStartWith('SKT-');
    expect(strlen($token))->toBeGreaterThanOrEqual(20);
});

test('doctor or staff can view and print official sick leave certificate', function () {
    $doctorUser = User::factory()->create();
    $doctorUser->assignRole('doctor');

    $patient = Patient::factory()->create(['name' => 'Farhan Kamil']);
    $polyclinic = Polyclinic::factory()->create(['name' => 'Poli Penyakit Dalam']);
    $doctor = Doctor::factory()->create([
        'user_id' => $doctorUser->id,
        'name' => 'dr. Andika Pratama, Sp.PD',
        'sip_number' => 'SIP-12345/IDI/2026',
        'polyclinic_id' => $polyclinic->id,
    ]);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
    ]);

    $record = MedicalRecord::factory()->create([
        'visit_id' => $visit->id,
        'doctor_id' => $doctor->id,
        'assessment' => 'K29.7 - Gastritis',
    ]);

    $certificate = SickLeaveCertificate::create([
        'medical_record_id' => $record->id,
        'visit_id' => $visit->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'certificate_number' => SickLeaveCertificate::generateCertificateNumber(),
        'days_count' => 3,
        'start_date' => Carbon::today(),
        'end_date' => Carbon::today()->addDays(2),
        'diagnosis_note' => 'K29.7 - Gastritis',
        'notes' => 'Pasien dianjurkan istirahat tirah baring.',
        'verification_token' => SickLeaveCertificate::generateVerificationToken(),
        'issued_at' => now(),
    ]);

    $response = $this->actingAs($doctorUser)->get(route('sick-leave.print', $certificate));

    $response->assertOk();
    $response->assertSee('SURAT KETERANGAN SAKIT');
    $response->assertSee($certificate->certificate_number);
    $response->assertSee('Farhan Kamil');
    $response->assertSee('3 Hari');
    $response->assertSee('dr. Andika Pratama, Sp.PD');
    $response->assertSee('SIP: SIP-12345/IDI/2026');
    $response->assertSee('E-VERIFIKASI RESMI');
});

test('patient owner can access their own sick leave certificate', function () {
    $patientUser = User::factory()->create();
    $patientUser->assignRole('patient');

    $patient = Patient::factory()->create([
        'user_id' => $patientUser->id,
        'name' => 'Budi Pratama',
    ]);

    $doctor = Doctor::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $record = MedicalRecord::factory()->create([
        'visit_id' => $visit->id,
        'doctor_id' => $doctor->id,
    ]);

    $certificate = SickLeaveCertificate::factory()->create([
        'medical_record_id' => $record->id,
        'visit_id' => $visit->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $response = $this->actingAs($patientUser)->get(route('sick-leave.print', $certificate));

    $response->assertOk();
    $response->assertSee('Budi Pratama');
});

test('unauthorized user cannot view other patient sick leave certificate', function () {
    $strangerUser = User::factory()->create();
    $strangerUser->assignRole('patient');

    $patient = Patient::factory()->create(['name' => 'Pasien Rahasia']);
    $certificate = SickLeaveCertificate::factory()->create([
        'patient_id' => $patient->id,
    ]);

    $response = $this->actingAs($strangerUser)->get(route('sick-leave.print', $certificate));

    $response->assertForbidden();
});

test('public authenticity verification returns valid certificate details', function () {
    $certificate = SickLeaveCertificate::factory()->create([
        'certificate_number' => 'SK/202611/9999',
        'days_count' => 4,
        'start_date' => '2026-11-15',
        'end_date' => '2026-11-18',
        'verification_token' => 'SKT-TEST-VALID-TOKEN-12345',
    ]);

    // Public route: no auth required
    $response = $this->get(route('sick-leave.verify', ['token' => 'SKT-TEST-VALID-TOKEN-12345']));

    $response->assertOk();
    $response->assertSee('SURAT KETERANGAN SAKIT SAH');
    $response->assertSee('SK/202611/9999');
    $response->assertSee('4 Hari Kalender');
    $response->assertSee('Dokumen Resmi Terverifikasi');
});

test('public verification with invalid token shows not found error', function () {
    $response = $this->get(route('sick-leave.verify', ['token' => 'NON-EXISTING-TOKEN-XYZ']));

    $response->assertOk();
    $response->assertSee('Dokumen Medis Tidak Ditemukan / Tidak Valid');
});
