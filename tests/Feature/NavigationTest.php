<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

function signedInUserWithRole(string $roleName): User
{
    $user = User::factory()->create();
    $user->assignRole($roleName);

    return $user;
}

test('the dashboard link is shown to all three roles', function (string $roleName) {
    $this->actingAs(signedInUserWithRole($roleName))
        ->get('/dashboard')
        ->assertSee('Dashboard', false);
})->with(['admin', 'lecturer', 'student']);

test('the admin sees the management links and none of the role specific links', function () {
    $this->actingAs(signedInUserWithRole('admin'))
        ->get('/dashboard')
        ->assertSee('Students', false)
        ->assertSee('Registrations', false)
        ->assertDontSee('My Courses', false)
        ->assertDontSee('Available Courses', false)
        ->assertDontSee('My Registrations', false);
});

test('the lecturer only sees the link to the courses assigned to them', function () {
    $this->actingAs(signedInUserWithRole('lecturer'))
        ->get('/dashboard')
        ->assertSee('My Courses', false)
        ->assertDontSee('Students', false)
        ->assertDontSee('Available Courses', false)
        ->assertDontSee('My Registrations', false);
});

test('the student only sees the links for their own registration business', function () {
    $this->actingAs(signedInUserWithRole('student'))
        ->get('/dashboard')
        ->assertSee('Available Courses', false)
        ->assertSee('My Registrations', false)
        ->assertDontSee('Students', false)
        ->assertDontSee('My Courses', false);
});

test('a student with a record is offered the link to it and may open that page', function () {
    $student = studentWithLogin();

    $this->actingAs($student->user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('My Record', false)
        ->assertSee('/students/'.$student->getKey(), false);

    $this->actingAs($student->user)
        ->get("/students/{$student->getKey()}")
        ->assertOk()
        ->assertSee($student->student_number);
});

test('a student account whose record is missing is not offered the link', function () {
    $this->actingAs(signedInUserWithRole('student'))
        ->get('/dashboard')
        ->assertDontSee('My Record', false);
});

test('the other roles are not offered the student links', function (string $roleName) {
    $this->actingAs(signedInUserWithRole($roleName))
        ->get('/dashboard')
        ->assertDontSee('My Record', false);
})->with(['admin', 'lecturer']);

test('the layout renders a success flash message', function () {
    $this->actingAs(signedInUserWithRole('admin'))
        ->withSession(['success' => 'Student record saved.'])
        ->get('/dashboard')
        ->assertSee('Student record saved.', false);
});

test('the layout renders an error flash message', function () {
    $this->actingAs(signedInUserWithRole('admin'))
        ->withSession(['error' => 'That course still has registrations.'])
        ->get('/dashboard')
        ->assertSee('That course still has registrations.', false);
});

test('the layout summarises validation errors', function () {
    $errors = (new ViewErrorBag)->put('default', new MessageBag([
        'student_number' => 'The student number is required.',
        'email' => 'The email has already been taken.',
    ]));

    $this->actingAs(signedInUserWithRole('admin'))
        ->withSession(['errors' => $errors])
        ->get('/dashboard')
        ->assertSee('Please correct the following problems:', false)
        ->assertSee('The student number is required.')
        ->assertSee('The email has already been taken.');
});

test('the layout shows the site header and the footer', function () {
    $this->actingAs(signedInUserWithRole('admin'))
        ->get('/dashboard')
        ->assertSee('Student Management System', false)
        ->assertSee('All rights reserved.', false);
});

test('a page written with @extends still gets the layout around its content section', function () {
    $directory = storage_path('app/testing-views');
    File::ensureDirectoryExists($directory);
    File::put($directory.'/extended-page.blade.php', <<<'BLADE'
@extends('layouts.app')

@section('content')
    Content written by the extending page.
@endsection
BLADE);

    View::addLocation($directory);
    Route::view('/extended-page', 'extended-page')->middleware('web');

    $this->get('/extended-page')
        ->assertSee('Content written by the extending page.', false)
        ->assertSee('All rights reserved.', false);

    File::deleteDirectory($directory);
});

test('a guest is sent to the login page before seeing any navigation', function () {
    $this->get('/dashboard')
        ->assertRedirect(route('login', absolute: false));
});
