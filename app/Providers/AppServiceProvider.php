<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use App\Policies\CoursePolicy;
use App\Policies\RegistrationPolicy;
use App\Policies\StudentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registered by hand so the file names stay as the contract in AGENT_INSTRUCTIONS.md names them,
        // including RegistrationPolicy for the CourseRegistration model.
        Gate::policy(Student::class, StudentPolicy::class);
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(CourseRegistration::class, RegistrationPolicy::class);
    }
}
