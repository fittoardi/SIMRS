<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'nurse', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admission', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'pharmacist', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
});

test('guests are redirected to login when accessing admin panel', function () {
    $response = $this->get('/admin');

    $response->assertRedirect('/admin/login');
});

test('patients cannot access admin panel', function () {
    $patient = User::factory()->create([
        'is_active' => true,
    ]);
    $patient->assignRole('patient');

    $response = $this->actingAs($patient)->get('/admin');

    $response->assertForbidden();
});

test('super admin can access admin panel', function () {
    $superAdmin = User::factory()->create([
        'is_active' => true,
    ]);
    $superAdmin->assignRole('super_admin');

    $response = $this->actingAs($superAdmin)->get('/admin');

    $response->assertSuccessful();
});

test('doctor can access admin panel', function () {
    $doctor = User::factory()->create([
        'is_active' => true,
    ]);
    $doctor->assignRole('doctor');

    $response = $this->actingAs($doctor)->get('/admin');

    $response->assertSuccessful();
});

test('all hospital staff roles can access admin panel', function (string $role) {
    $staff = User::factory()->create([
        'is_active' => true,
    ]);
    $staff->assignRole($role);

    $response = $this->actingAs($staff)->get('/admin');

    $response->assertSuccessful();
})->with(['nurse', 'admission', 'pharmacist', 'cashier']);

test('inactive staff cannot access admin panel', function () {
    $inactiveDoctor = User::factory()->create([
        'is_active' => false,
    ]);
    $inactiveDoctor->assignRole('doctor');

    $response = $this->actingAs($inactiveDoctor)->get('/admin');

    $response->assertForbidden();
});
