<?php

use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('an administrator with no students is told the list is empty', function () {
    $this->actingAs(userWithRole('admin'))
        ->get('/students')
        ->assertOk()
        ->assertSee('No students yet.');
});

test('a lecturer with no assigned courses is told the list is empty', function () {
    $this->actingAs(userWithRole('lecturer'))
        ->get('/courses')
        ->assertOk()
        ->assertSee('No courses yet.');
});

test('a student with no registrations is told the list is empty', function () {
    $this->actingAs(userWithRole('student'))
        ->get('/registrations')
        ->assertOk()
        ->assertSee('Nothing registered yet.');
});
