<?php

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('a student registers for an available course and the time is stamped', function () {
    $student = studentWithLogin();
    $course = Course::factory()->create();

    $this->actingAs($student->user)
        ->post('/registrations', ['course_id' => $course->id])
        ->assertRedirect(route('registrations.index', absolute: false))
        ->assertSessionHas('success');

    $registration = CourseRegistration::sole();

    expect($registration->student_id)->toBe($student->id)
        ->and($registration->course_id)->toBe($course->id)
        ->and($registration->registered_at)->not->toBeNull();
});

test('registering for the same course twice is refused and explained', function () {
    $student = studentWithLogin();
    $course = Course::factory()->create();

    $this->actingAs($student->user)->post('/registrations', ['course_id' => $course->id]);

    $this->actingAs($student->user)
        ->post('/registrations', ['course_id' => $course->id])
        ->assertSessionHas('error');

    expect(CourseRegistration::count())->toBe(1);
});

test('a student cannot register on behalf of another student by changing the form', function () {
    $student = studentWithLogin();
    $other = Student::factory()->create();

    $this->actingAs($student->user)
        ->post('/registrations', [
            'course_id' => Course::factory()->create()->id,
            'student_id' => $other->id,
        ])
        ->assertForbidden();

    expect(CourseRegistration::count())->toBe(0);
});

test('an administrator registers a chosen student for a course', function () {
    $student = Student::factory()->create();

    $this->actingAs(userWithRole('admin'))
        ->post('/registrations', [
            'course_id' => Course::factory()->create()->id,
            'student_id' => $student->id,
        ])
        ->assertSessionHas('success');

    expect(CourseRegistration::count())->toBe(1);
});

test('a lecturer is not allowed to register anyone for a course', function () {
    $this->actingAs(userWithRole('lecturer'))
        ->post('/registrations', ['course_id' => Course::factory()->create()->id])
        ->assertForbidden();
});

test('an account with the student role but no student record is told to wait', function () {
    $this->actingAs(userWithRole('student'))
        ->post('/registrations', ['course_id' => Course::factory()->create()->id])
        ->assertSessionHasErrors('student_id');
});

test('a student cancels their own registration', function () {
    $student = studentWithLogin();
    $course = Course::factory()->create();
    $registration = $student->registerForCourse($course);

    $this->actingAs($student->user)
        ->delete("/registrations/{$registration->id}")
        ->assertRedirect(route('registrations.index', absolute: false))
        ->assertSessionHas('success');

    expect(CourseRegistration::count())->toBe(0);
});

test('a student may not cancel another student registration', function () {
    $other = Student::factory()->create();
    $registration = $other->registerForCourse(Course::factory()->create());

    $this->actingAs(studentWithLogin()->user)
        ->delete("/registrations/{$registration->id}")
        ->assertForbidden();

    expect(CourseRegistration::count())->toBe(1);
});

test('the registration list a student sees holds only their own rows', function () {
    $student = studentWithLogin();
    $other = Student::factory()->create();
    $theirs = Course::factory()->create(['code' => 'BUC7777X']);
    $mine = Course::factory()->create(['code' => 'BUC8888Y']);

    $other->registerForCourse($theirs);
    $student->registerForCourse($mine);

    $this->actingAs($student->user)
        ->get('/registrations')
        ->assertOk()
        ->assertSee('BUC8888Y')
        ->assertDontSee('BUC7777X');
});
