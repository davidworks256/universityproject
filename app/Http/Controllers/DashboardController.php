<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show each role the records it is allowed to see.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user->can('manage students')) {
            return view('dashboard.admin', [
                'studentCount' => Student::query()->count(),
                'courseCount' => Course::query()->count(),
                'registrationCount' => CourseRegistration::query()->count(),
                'userCount' => User::query()->count(),
                'recentRegistrations' => CourseRegistration::query()
                    ->with(['student', 'course'])
                    ->latest('registered_at')
                    ->limit(5)
                    ->get(),
            ]);
        }

        if ($user->hasRole('lecturer')) {
            return view('dashboard.lecturer', [
                'courses' => $user->taughtCourses()->withCount('students')->orderBy('code')->get(),
            ]);
        }

        return view('dashboard.student', [
            'student' => $user->student,
            'registrations' => $user->student?->courses()->with('lecturer')->orderBy('courses.code')->get() ?? collect(),
            'availableCourses' => $user->student?->availableCourses()->with('lecturer')->orderBy('courses.code')->get() ?? collect(),
        ]);
    }
}
