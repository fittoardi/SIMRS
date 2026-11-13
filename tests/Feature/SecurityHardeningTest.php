<?php

use App\Models\MedicalRecord;
use App\Models\MedicalRecordAccessLog;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Services\MedicalAuditLogger;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
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
