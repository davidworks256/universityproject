<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * The roles and permissions fixed by the coursework contract.
     *
     * @var array<string, list<string>>
     */
    private const ROLE_PERMISSIONS = [
        'admin' => [
            'manage students',
            'manage courses',
            'manage registrations',
            'manage users',
            'view assigned courses',
            'view course students',
            'view available courses',
            'view own registrations',
            'register for course',
        ],
        'lecturer' => [
            'view assigned courses',
            'view course students',
        ],
        'student' => [
            'view available courses',
            'view own registrations',
            'register for course',
        ],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::ROLE_PERMISSIONS as $roleName => $permissionNames) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            $role->syncPermissions(
                collect($permissionNames)
                    ->map(fn (string $permissionName): Permission => Permission::firstOrCreate([
                        'name' => $permissionName,
                        'guard_name' => 'web',
                    ]))
                    ->all()
            );
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
