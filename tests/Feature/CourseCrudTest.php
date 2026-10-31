<?php

use App\Models\Course;
use App\Models\Student;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('an administrator sees every course', function () {
    Course::factory()->count(3)->create();

    $this->actingAs(userWithRole('admin'))
        ->get('/courses')
        ->assertOk()
        ->assertSee('Courses');
});

test('a lecturer only sees the courses assigned to them', function () {
    $lecturer = userWithRole('lecturer');
    $assigned = Course::factory()->create(['lecturer_id' => $lecturer->id]);
    $other = Course::factory()->create();

    $this->actingAs($lecturer)
        ->get('/courses')
        ->assertOk()
        ->assertSee($assigned->code)
        ->assertDontSee($other->code);
});

test('a student sees the courses they are not registered for yet', function () {
    $student = studentWithLogin();
    $registered = Course::factory()->create();
    $open = Course::factory()->create();

    $student->registerForCourse($registered);

    $this->actingAs($student->user)
        ->get('/courses')
        ->assertOk()
        ->assertSee($open->code)
        ->assertDontSee($registered->code);
});

test('an administrator creates a course and it is read back with integer credits', function () {
    $lecturer = userWithRole('lecturer');

    $this->actingAs(userWithRole('admin'))
        ->post('/courses', [
            'code' => 'BUC9001',
            'title' => 'Testing and Quality Assurance',
            'description' => 'Writing tests that catch real defects.',
            'credits' => 6,
            'lecturer_id' => $lecturer->id,
        ])
        ->assertSessionHas('success');

    $course = Course::where('code', 'BUC9001')->firstOrFail();

    expect($course->lecturer->is($lecturer))->toBeTrue()
        ->and($course->credits)->toBe(6);
});

test('credits outside the allowed range are rejected', function () {
    $this->actingAs(userWithRole('admin'))
        ->from('/courses/create')
        ->post('/courses', [
            'code' => 'BUC9002',
            'title' => 'Too Many Credits',
            'credits' => 24,
            'lecturer_id' => userWithRole('lecturer')->id,
        ])
        ->assertSessionHasErrors('credits');

    expect(Course::where('code', 'BUC9002')->exists())->toBeFalse();
});

test('an account without the lecturer role cannot be picked as the lecturer', function () {
    $this->actingAs(userWithRole('admin'))
        ->post('/courses', [
            'code' => 'BUC9003',
            'title' => 'Wrong Lecturer',
            'credits' => 3,
            'lecturer_id' => userWithRole('student')->id,
        ])
        ->assertSessionHasErrors('lecturer_id');
});

test('a course code that already exists is rejected', function () {
    $existing = Course::factory()->create();

    $this->actingAs(userWithRole('admin'))
        ->post('/courses', [
            'code' => $existing->code,
            'title' => 'Duplicate Code',
            'credits' => 4,
            'lecturer_id' => userWithRole('lecturer')->id,
        ])
        ->assertSessionHasErrors('code');
});

test('a student cannot open the course creation form', function () {
    $this->actingAs(userWithRole('student'))
        ->get('/courses/create')
        ->assertForbidden();
});

test('a lecturer may open the roster of a course they teach', function () {
    $lecturer = userWithRole('lecturer');
    $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
    $student = Student::factory()->create();
    $course->students()->attach($student);

    $this->actingAs($lecturer)
        ->get("/courses/{$course->id}")
        ->assertOk()
        ->assertSee($student->name);
});

test('a lecturer may not open the roster of somebody else course', function () {
    $this->actingAs(userWithRole('lecturer'))
        ->get('/courses/'.Course::factory()->create()->id)
        ->assertForbidden();
});

test('a course that still has registrations is not deleted and says why', function () {
    $course = Course::factory()->create();
    $student = Student::factory()->create();
    $student->registerForCourse($course);

    $this->actingAs(userWithRole('admin'))
        ->delete("/courses/{$course->id}")
        ->assertRedirect(route('courses.show', $course, absolute: false))
        ->assertSessionHas('error');

    expect(Course::whereKey($course->id)->exists())->toBeTrue();
});

test('a course with no registrations is deleted', function () {
    $course = Course::factory()->create();
    $id = $course->id;

    $this->actingAs(userWithRole('admin'))
        ->delete("/courses/{$id}")
        ->assertRedirect(route('courses.index', absolute: false))
        ->assertSessionHas('success');

    expect(Course::find($id))->toBeNull();
});
