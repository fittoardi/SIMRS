<?php

use App\Enums\BedStatus;
use App\Enums\QueueStatus;
use App\Filament\Widgets\DoctorTodayQueueWidget;
use App\Filament\Widgets\HospitalStatsOverview;
use App\Filament\Widgets\LowStockMedicinesWidget;
use App\Filament\Widgets\TopIcd10DiagnosisWidget;
use App\Models\Bed;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Models\Queue;
use App\Models\Room;
use App\Models\User;
use App\Models\Visit;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'nurse', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'pharmacist', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
});

test('hospital stats overview calculates bor and patient visits', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('super_admin');

    $room = Room::create([
        'code' => 'ROOM-WIDGET-'.uniqid(),
        'name' => 'Kamar Dahlia',
        'ward_name' => 'Bangsal Dahlia',
        'class_type' => 'Kelas 2',
        'price_per_day' => 150000,
        'is_active' => true,
    ]);

    Bed::create(['room_id' => $room->id, 'bed_number' => 'B-101', 'status' => BedStatus::Occupied]);
    Bed::create(['room_id' => $room->id, 'bed_number' => 'B-102', 'status' => BedStatus::Available]);

    $patient = Patient::factory()->create();
    Visit::factory()->create(['patient_id' => $patient->id, 'visit_date' => today()]);

    Livewire::actingAs($superAdmin)
        ->test(HospitalStatsOverview::class)
        ->assertSuccessful()
        ->assertSee('Bed Occupancy Rate (BOR)')
        ->assertSee('50%');
});

test('low stock medicines widget renders medicines with low stock or approaching expiration', function () {
    $pharmacist = User::factory()->create();
    $pharmacist->assignRole('pharmacist');

    $urgentMed = Medicine::factory()->create([
        'name' => 'Paracetamol Drop Bayi',
        'sku' => 'MED-DROP-01',
        'stock' => 2,
        'min_stock' => 10,
    ]);

    Livewire::actingAs($pharmacist)
        ->test(LowStockMedicinesWidget::class)
        ->assertSuccessful()
        ->assertSee($urgentMed->name);
});

test('top icd10 diagnosis widget renders most frequent diagnoses', function () {
    $doctorUser = User::factory()->create();
    $doctorUser->assignRole('doctor');

    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    MedicalRecord::factory()->create([
        'visit_id' => $visit->id,
        'icd10_code' => 'J06.9',
        'icd10_description' => 'Infeksi Saluran Pernapasan Akut (ISPA)',
    ]);

    Livewire::actingAs($doctorUser)
        ->test(TopIcd10DiagnosisWidget::class)
        ->assertSuccessful()
        ->assertSee('J06.9')
        ->assertSee('Infeksi Saluran Pernapasan Akut (ISPA)');
});

test('doctor today queue widget renders assigned patient queue for logged in doctor', function () {
    $doctorUser = User::factory()->create();
    $doctorUser->assignRole('doctor');

    $polyclinic = Polyclinic::factory()->create();
    $doctor = Doctor::factory()->create([
        'user_id' => $doctorUser->id,
        'polyclinic_id' => $polyclinic->id,
    ]);

    $patient = Patient::factory()->create(['name' => 'Siti Nurhaliza']);
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
        'visit_date' => today(),
    ]);

    Queue::factory()->create([
        'visit_id' => $visit->id,
        'queue_code' => 'U-007',
        'status' => QueueStatus::Waiting,
    ]);

    Livewire::actingAs($doctorUser)
        ->test(DoctorTodayQueueWidget::class)
        ->assertSuccessful()
        ->assertSee('U-007')
        ->assertSee('Siti Nurhaliza');
});
