<?php

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('a guest is sent to the login page before reaching any list', function (string $path) {
    $this->get($path)->assertRedirect(route('login', absolute: false));
})->with(['/students', '/courses', '/registrations']);

test('an administrator opens every management page', function (string $path) {
    $this->actingAs(userWithRole('admin'))->get($path)->assertOk();
})->with([
    'the student list' => '/students',
    'the student form' => '/students/create',
    'the course list' => '/courses',
    'the course form' => '/courses/create',
    'the registration list' => '/registrations',
    'the registration form' => '/registrations/create',
]);

test('the permission middleware refuses the pages a role is not given', function (string $roleName, string $path) {
    $this->actingAs(userWithRole($roleName))->get($path)->assertForbidden();
})->with([
    'a lecturer is refused the student list' => ['lecturer', '/students'],
    'a student is refused the student list' => ['student', '/students'],
    'a student is refused the student form' => ['student', '/students/create'],
    'a lecturer is refused the student form' => ['lecturer', '/students/create'],
    'a student is refused the course form' => ['student', '/courses/create'],
    'a lecturer is refused the course form' => ['lecturer', '/courses/create'],
    'a lecturer is refused the registration form' => ['lecturer', '/registrations/create'],
]);

test('a student opens their own record but never another student record', function () {
    $own = studentWithLogin();
    $other = Student::factory()->create();

    $this->actingAs($own->user)
        ->get("/students/{$own->getKey()}")
        ->assertOk()
        ->assertSee($own->student_number);

    $this->actingAs($own->user)
        ->get("/students/{$other->getKey()}")
        ->assertForbidden();
});

test('a lecturer opens only the courses assigned to them', function () {
    $lecturer = userWithRole('lecturer');
    $colleague = userWithRole('lecturer');
    $assigned = Course::factory()->create(['lecturer_id' => $lecturer->getKey()]);
    $notAssigned = Course::factory()->create(['lecturer_id' => $colleague->getKey()]);

    $this->actingAs($lecturer)->get("/courses/{$assigned->getKey()}")->assertOk();

    $this->actingAs($lecturer)
        ->get("/courses/{$notAssigned->getKey()}")
        ->assertForbidden();
});

test('a student reading a course page does not see the students on it', function () {
    $course = Course::factory()->create();
    $classmate = Student::factory()->create([
        'name' => 'Hidden Classmate',
        'email' => 'hidden.classmate@example.com',
        'student_number' => 'BUC/2026/9999',
    ]);
    $classmate->registerForCourse($course);

    $this->actingAs(studentWithLogin()->user)
        ->get("/courses/{$course->getKey()}")
        ->assertOk()
        ->assertSee($course->title)
        ->assertDontSee('Hidden Classmate');
});

test('a lecturer is refused the register action', function () {
    $this->actingAs(userWithRole('lecturer'))
        ->post('/registrations', ['course_id' => Course::factory()->create()->getKey()])
        ->assertForbidden();
});

test('a student cancels their own registration but not another student registration', function () {
    $own = studentWithLogin();
    $other = studentWithLogin();
    $mine = $own->registerForCourse(Course::factory()->create());
    $theirs = $other->registerForCourse(Course::factory()->create());

    $this->actingAs($own->user)
        ->delete("/registrations/{$theirs->getKey()}")
        ->assertForbidden();

    expect(CourseRegistration::whereKey($theirs->getKey())->exists())->toBeTrue();

    $this->actingAs($own->user)
        ->delete("/registrations/{$mine->getKey()}")
        ->assertRedirect(route('registrations.index', absolute: false));

    expect(CourseRegistration::whereKey($mine->getKey())->exists())->toBeFalse();
});

test('an administrator cancels any registration', function () {
    $registration = studentWithLogin()->registerForCourse(Course::factory()->create());

    $this->actingAs(userWithRole('admin'))
        ->delete("/registrations/{$registration->getKey()}")
        ->assertRedirect(route('registrations.index', absolute: false));

    expect(CourseRegistration::whereKey($registration->getKey())->exists())->toBeFalse();
});
