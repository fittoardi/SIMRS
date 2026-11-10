<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'super_admin',
            'doctor',
            'nurse',
            'admission',
            'pharmacist',
            'cashier',
            'patient',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // Akun-akun staf dan pasien contoh
        $accounts = [
            [
                'name' => 'Super Administrator (CONTOH)',
                'email' => 'superadmin@aisyahmedika.id',
                'phone' => '081200000001',
                'role' => 'super_admin',
            ],
            [
                'name' => 'dr. Siti Aisyah, Sp.PD (CONTOH)',
                'email' => 'dokter@aisyahmedika.id',
                'phone' => '081200000002',
                'role' => 'doctor',
            ],
            [
                'name' => 'Ns. Rahmat Hidayat, S.Kep (CONTOH)',
                'email' => 'perawat@aisyahmedika.id',
                'phone' => '081200000003',
                'role' => 'nurse',
            ],
            [
                'name' => 'Dina Admisi (CONTOH)',
                'email' => 'admisi@aisyahmedika.id',
                'phone' => '081200000004',
                'role' => 'admission',
            ],
            [
                'name' => 'apt. Farhan Pratama, S.Farm (CONTOH)',
                'email' => 'farmasi@aisyahmedika.id',
                'phone' => '081200000005',
                'role' => 'pharmacist',
            ],
            [
                'name' => 'Kurnia Kasir (CONTOH)',
                'email' => 'kasir@aisyahmedika.id',
                'phone' => '081200000006',
                'role' => 'cashier',
            ],
            [
                'name' => 'Budi Santoso (CONTOH)',
                'email' => 'pasien@aisyahmedika.id',
                'phone' => '081200000007',
                'role' => 'patient',
            ],
        ];

        foreach ($accounts as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$data['role']]);
        }
    }
}
