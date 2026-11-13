<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
});

test('cashier can access invoice list in filament', function () {
    $cashierUser = User::factory()->create();
    $cashierUser->assignRole('cashier');

    $response = $this->actingAs($cashierUser)->get('/admin/invoices');

    $response->assertOk();
    $response->assertSee('Kasir & Billing Pasien');
});

test('super admin can access invoice list in filament', function () {
    $adminUser = User::factory()->create();
    $adminUser->assignRole('super_admin');

    $response = $this->actingAs($adminUser)->get('/admin/invoices');

    $response->assertOk();
});

test('patient cannot access filament invoices list', function () {
    $patientUser = User::factory()->create();
    $patientUser->assignRole('patient');

    $response = $this->actingAs($patientUser)->get('/admin/invoices');

    expect($response->status())->toBeIn([403, 302]);
});

test('guest is redirected to filament login when accessing invoices', function () {
    $response = $this->get('/admin/invoices');

    $response->assertRedirect('/admin/login');
});

