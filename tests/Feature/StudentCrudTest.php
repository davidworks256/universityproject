<?php

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('a guest is sent to the login page before seeing any student record', function () {
    $this->get('/students')->assertRedirect(route('login', absolute: false));
});

test('a signed-in student is refused the student list', function () {
    $this->actingAs(userWithRole('student'))
        ->get('/students')
        ->assertForbidden();
});

test('an administrator sees the students with their numbers', function () {
    $student = Student::factory()->create();

    $this->actingAs(userWithRole('admin'))
        ->get('/students')
        ->assertOk()
        ->assertSee($student->student_number);
});

test('creating a student also creates the sign-in account with the student role', function () {
    $this->actingAs(userWithRole('admin'))
        ->post('/students', [
            'student_number' => 'BUC/2026/9001',
            'name' => 'Ronald Kibirige',
            'email' => 'ronald.kibirige@student.mubs.ac.ug',
            'phone' => '+256700123456',
        ])
        ->assertRedirect(route('students.index', absolute: false))
        ->assertSessionHas('success');

    $student = Student::where('student_number', 'BUC/2026/9001')->firstOrFail();

    expect($student->user->email)->toBe('ronald.kibirige@student.mubs.ac.ug')
        ->and($student->user->hasRole('student'))->toBeTrue();
});

test('a student number that is already used is rejected', function () {
    $existing = Student::factory()->create();

    $this->actingAs(userWithRole('admin'))
        ->from('/students/create')
        ->post('/students', [
            'student_number' => $existing->student_number,
            'name' => 'Duplicate Number',
            'email' => 'duplicate.number@student.mubs.ac.ug',
        ])
        ->assertSessionHasErrors('student_number');

    expect(Student::count())->toBe(1);
});

test('an email that already signs in cannot be used for a new student', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs(userWithRole('admin'))
        ->post('/students', [
            'student_number' => 'BUC/2026/9002',
            'name' => 'Taken Email',
            'email' => 'taken@example.com',
        ])
        ->assertSessionHasErrors('email');
});

test('updating a student keeps the linked account in step', function () {
    $student = Student::factory()->create();

    $this->actingAs(userWithRole('admin'))
        ->put("/students/{$student->id}", [
            'student_number' => $student->student_number,
            'name' => 'Namukeddde Aisha',
            'email' => 'namukeddde.aisha@student.mubs.ac.ug',
            'phone' => null,
        ])
        ->assertRedirect(route('students.index', absolute: false))
        ->assertSessionHas('success');

    expect($student->fresh()->name)->toBe('Namukeddde Aisha')
        ->and($student->user->fresh()->email)->toBe('namukeddde.aisha@student.mubs.ac.ug');
});

test('deleting a student removes the account and the registrations with it', function () {
    $student = Student::factory()->create();
    $course = Course::factory()->create();
    $student->registerForCourse($course);

    $this->actingAs(userWithRole('admin'))
        ->delete("/students/{$student->id}")
        ->assertRedirect(route('students.index', absolute: false))
        ->assertSessionHas('success');

    expect(User::find($student->user_id))->toBeNull()
        ->and(Student::count())->toBe(0)
        ->and(CourseRegistration::count())->toBe(0);
});
