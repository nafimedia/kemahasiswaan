<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions list
        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'activity_logs.view',
            'settings.view',
            'settings.edit',
            'branding.view',
            'branding.edit',
            'modules.view',
            'modules.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 1. Super Admin Role
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // 2. Admin Kemahasiswaan Role
        $adminKemahasiswaanRole = Role::firstOrCreate(['name' => 'Admin Kemahasiswaan']);
        $adminKemahasiswaanRole->givePermissionTo(['activity_logs.view', 'settings.view']);

        // 3. Admin Prestasi Role
        $adminPrestasiRole = Role::firstOrCreate(['name' => 'Admin Prestasi']);

        // 4. Admin Alumni Role
        $adminAlumniRole = Role::firstOrCreate(['name' => 'Admin Alumni']);

        // 5. Operator Fakultas Role
        $operatorFakultasRole = Role::firstOrCreate(['name' => 'Operator Fakultas']);

        // Standard User Role
        $userRole = Role::firstOrCreate(['name' => 'User']);

        // Create Super Admin User
        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@unupurwokerto.ac.id'],
            [
                'name' => 'Super Admin Kemahasiswaan',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $superAdminUser->assignRole($superAdminRole);

        // Create Admin Kemahasiswaan User
        $adminKemahasiswaanUser = User::firstOrCreate(
            ['email' => 'kemahasiswaan@unupurwokerto.ac.id'],
            [
                'name' => 'Admin Kemahasiswaan UNUPU',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $adminKemahasiswaanUser->assignRole($adminKemahasiswaanRole);
    }
}

