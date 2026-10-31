<?php

use App\Models\Course;
use App\Models\Student;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('the administrator dashboard counts the records and lists the latest registrations', function () {
    $student = Student::factory()->create();
    $course = Course::factory()->create(['code' => 'BUC4001A', 'title' => 'Cloud Fundamentals']);
    $student->registerForCourse($course);

    $this->actingAs(userWithRole('admin'))
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Administrator dashboard')
        ->assertSee('BUC4001A')
        ->assertSee($student->name);
});

test('the lecturer dashboard lists only the assigned courses with their student counts', function () {
    $lecturer = userWithRole('lecturer');
    $assigned = Course::factory()->create([
        'code' => 'BUC4002B',
        'title' => 'Data Structures',
        'lecturer_id' => $lecturer->id,
    ]);
    Course::factory()->create(['code' => 'BUC4003C', 'title' => 'Operating Systems']);

    Student::factory()->count(2)->create()->each(
        fn (Student $student): mixed => $assigned->students()->attach($student)
    );

    $this->actingAs($lecturer)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Lecturer dashboard')
        ->assertSee('BUC4002B')
        ->assertDontSee('BUC4003C');
});

test('the student dashboard shows the own registrations and the open courses', function () {
    $student = Student::factory()->create();
    $registered = Course::factory()->create(['code' => 'BUC4004D', 'title' => 'Web Programming']);
    $open = Course::factory()->create(['code' => 'BUC4005E', 'title' => 'Human Computer Interaction']);

    $student->registerForCourse($registered);

    $this->actingAs($student->user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Student dashboard')
        ->assertSee('Web Programming')
        ->assertSee('Human Computer Interaction');
});

test('a student account without a record is told an administrator must create one', function () {
    $this->actingAs(userWithRole('student'))
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('an administrator must create one');
});
