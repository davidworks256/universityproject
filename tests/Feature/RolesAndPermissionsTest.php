<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('it seeds the three roles fixed by the coursework contract', function () {
    expect(Role::pluck('name')->sort()->values()->all())
        ->toBe(['admin', 'lecturer', 'student']);
});

test('it seeds the nine permissions fixed by the coursework contract', function () {
    expect(Permission::count())->toBe(9);
});

test('the admin role holds every permission', function () {
    expect(Role::findByName('admin')->permissions->pluck('name')->sort()->values()->all())
        ->toBe(Permission::pluck('name')->sort()->values()->all());
});

test('the lecturer role only holds its two viewing permissions', function () {
    expect(Role::findByName('lecturer')->permissions->pluck('name')->sort()->values()->all())
        ->toBe(['view assigned courses', 'view course students']);
});

test('the student role only holds its three self-service permissions', function () {
    expect(Role::findByName('student')->permissions->pluck('name')->sort()->values()->all())
        ->toBe(['register for course', 'view available courses', 'view own registrations']);
});

test('seeding twice does not duplicate roles, permissions or links', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    expect(Role::count())->toBe(3)
        ->and(Permission::count())->toBe(9)
        ->and(DB::table('role_has_permissions')->count())->toBe(14);
});

test('a newly registered user is given the student role', function () {
    $response = $this->post('/register', [
        'name' => 'Aisha Bwire',
        'email' => 'aisha@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));

    expect(User::where('email', 'aisha@example.com')->firstOrFail()->hasRole('student'))->toBeTrue();
});
