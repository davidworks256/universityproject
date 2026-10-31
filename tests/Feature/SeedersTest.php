<?php

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

function seededUserWithRole(string $email, string $roleName): void
{
    expect(User::where('email', $email)->firstOrFail()->hasRole($roleName))->toBeTrue();
}

test('the seeded staff accounts carry the roles the brief describes', function () {
    seededUserWithRole('admin@example.com', 'admin');
    seededUserWithRole('lecturer@example.com', 'lecturer');
    seededUserWithRole('lecturer2@example.com', 'lecturer');
    seededUserWithRole('susan.achan@student.mubs.ac.ug', 'student');
});

test('the seeders meet the minimum numbers asked for in the brief', function () {
    expect(Role::count())->toBe(3)
        ->and(Student::count())->toBeGreaterThanOrEqual(5)
        ->and(Course::count())->toBeGreaterThanOrEqual(4)
        ->and(CourseRegistration::count())->toBeGreaterThan(0);
});

test('every seeded student record is linked to a sign-in account', function () {
    expect(Student::whereNull('user_id')->count())->toBe(0)
        ->and(Student::with('user')->get()->every(fn (Student $student): bool => $student->user->email === $student->email))
        ->toBeTrue();
});

test('every seeded course has a lecturer and the registration stamp defaults to the current time', function () {
    expect(Course::whereNull('lecturer_id')->count())->toBe(0)
        ->and(CourseRegistration::whereNull('registered_at')->count())->toBe(0);
});

test('each student is registered for two or three courses without repeating a course', function () {
    Student::query()->with('courses')->get()->each(function (Student $student): void {
        $codes = $student->courses->pluck('code');

        expect($codes->unique())->toHaveCount($codes->count())
            ->and($codes->count())->toBeBetween(2, 3);
    });
});

test('the seeded accounts sign in with the password documented for the demonstration', function (string $email) {
    $this->post('/login', ['email' => $email, 'password' => 'password'])
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
    $this->post('/logout');
})->with([
    'admin' => 'admin@example.com',
    'lecturer' => 'lecturer@example.com',
    'student' => 'susan.achan@student.mubs.ac.ug',
]);

test('running the seeders a second time leaves every table unchanged', function () {
    $counts = [
        'users' => User::count(),
        'students' => Student::count(),
        'courses' => Course::count(),
        'registrations' => CourseRegistration::count(),
        'roles' => Role::count(),
    ];

    $this->seed(DatabaseSeeder::class);

    expect($counts)->toBe([
        'users' => User::count(),
        'students' => Student::count(),
        'courses' => Course::count(),
        'registrations' => CourseRegistration::count(),
        'roles' => Role::count(),
    ]);
});
