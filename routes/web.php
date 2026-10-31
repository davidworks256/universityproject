<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Email verification is not part of this system, so the dashboard only requires a signed-in account.
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Student records are managed by an administrator only.
    Route::resource('students', StudentController::class)
        ->except(['show'])
        ->middleware('permission:manage students');

    // One record is decided by the policy instead, because a student may open their own.
    Route::get('/students/{student}', [StudentController::class, 'show'])
        ->middleware('can:view,student')
        ->name('students.show');

    // Every role reads courses, but only an administrator writes them.
    Route::resource('courses', CourseController::class)
        ->except(['index', 'show'])
        ->middleware('permission:manage courses');

    Route::get('/courses', [CourseController::class, 'index'])
        ->middleware('permission:manage courses|view assigned courses|view available courses')
        ->name('courses.index');

    Route::get('/courses/{course}', [CourseController::class, 'show'])
        ->middleware('can:view,course')
        ->name('courses.show');

    // The registration page reads different rows per role, so only the create and cancel steps are gated.
    Route::get('/registrations', [RegistrationController::class, 'index'])->name('registrations.index');

    Route::middleware('permission:manage registrations|register for course')->group(function () {
        Route::get('/registrations/create', [RegistrationController::class, 'create'])->name('registrations.create');
        Route::post('/registrations', [RegistrationController::class, 'store'])->name('registrations.store');
    });

    Route::delete('/registrations/{registration}', [RegistrationController::class, 'destroy'])
        ->middleware('can:delete,registration')
        ->name('registrations.destroy');
});

require __DIR__.'/auth.php';
