<?php

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;

test('a student record and its account point at each other', function () {
    $user = User::factory()->create();
    $student = Student::factory()->create(['user_id' => $user->getKey()]);

    expect($student->user->is($user))->toBeTrue()
        ->and($user->student->is($student))->toBeTrue();
});

test('a lecturer owns the courses assigned to them', function () {
    $lecturer = User::factory()->create();
    $courses = Course::factory()->count(3)->create(['lecturer_id' => $lecturer->getKey()]);

    expect($lecturer->taughtCourses)->toHaveCount(3)
        ->and($courses->first()->lecturer->is($lecturer))->toBeTrue();
});

test('a course without a lecturer keeps a null relation', function () {
    $course = Course::factory()->unassigned()->create();

    expect($course->lecturer)->toBeNull();
});

test('a student reads their registered courses through the belongsToMany relation', function () {
    $student = Student::factory()->create();
    $courses = Course::factory()->count(3)->create();

    $student->courses()->attach($courses->pluck('id'), ['registered_at' => '2026-10-01 09:00:00']);

    expect($student->courses)->toHaveCount(3)
        ->and($student->registrations)->toHaveCount(3)
        ->and(Carbon::parse($student->courses->first()->pivot->registered_at)->toDateTimeString())
        ->toBe('2026-10-01 09:00:00');
});

test('a course reads the students registered for it', function () {
    $course = Course::factory()->create();
    $students = Student::factory()->count(4)->create();

    $course->students()->attach($students->pluck('id'));

    expect($course->students)->toHaveCount(4)
        ->and($course->students->first()->name)->toBe($students->first()->name);
});

test('available courses exclude the ones the student is already registered for', function () {
    $student = Student::factory()->create();
    $registered = Course::factory()->create();
    Course::factory()->count(2)->create();

    $student->registerForCourse($registered);

    $available = $student->availableCourses()->pluck('id');

    expect($available)->toHaveCount(2)
        ->and($available)->not->toContain($registered->getKey());
});

test('the unique pair rejects a second registration for the same course', function () {
    $student = Student::factory()->create();
    $course = Course::factory()->create();

    $student->registerForCourse($course);

    expect(fn () => CourseRegistration::create([
        'student_id' => $student->getKey(),
        'course_id' => $course->getKey(),
    ]))->toThrow(QueryException::class);
});

test('deleting a student cascades to their registrations', function () {
    $student = Student::factory()->create();
    $course = Course::factory()->create();
    $student->registerForCourse($course);

    $student->delete();

    expect(CourseRegistration::count())->toBe(0)
        ->and($course->fresh()->students)->toHaveCount(0);
});

test('deleting a lecturer nulls the lecturer on their courses', function () {
    $lecturer = User::factory()->create();
    $course = Course::factory()->create(['lecturer_id' => $lecturer->getKey()]);

    $lecturer->delete();

    expect($course->fresh()->lecturer_id)->toBeNull();
});
