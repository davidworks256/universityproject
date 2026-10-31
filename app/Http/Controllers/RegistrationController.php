<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $registrations = CourseRegistration::query()
            ->with(['student', 'course.lecturer'])
            ->latest('registered_at');

        if ($user->cannot('manage registrations')) {
            if ($user->hasRole('lecturer')) {
                // A lecturer reads the registrations of the courses assigned to them, and nothing else.
                $registrations->whereHas(
                    'course',
                    fn ($course): mixed => $course->where('lecturer_id', $user->getKey())
                );
            } else {
                // A student reads their own rows. An account without a student record sees none.
                $registrations->where('student_id', $user->student?->getKey());
            }
        }

        return view('registrations.index', [
            'registrations' => $registrations->paginate(15),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $user = $request->user();

        if ($user->can('manage registrations')) {
            return view('registrations.create', [
                'students' => Student::query()->orderBy('student_number')->get(),
                'courses' => Course::query()->orderBy('code')->get(),
                'ownStudent' => null,
            ]);
        }

        return view('registrations.create', [
            'students' => null,
            'courses' => $user->student?->availableCourses()->orderBy('code')->get() ?? collect(),
            'ownStudent' => $user->student,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRegistrationRequest $request): RedirectResponse
    {
        $student = Student::query()->findOrFail($request->integer('student_id'));
        $course = Course::query()->findOrFail($request->integer('course_id'));

        if ($student->registrations()->where('course_id', $course->getKey())->exists()) {
            return redirect()->route('registrations.create')
                ->with('error', "{$student->name} is already registered for {$course->code}.");
        }

        try {
            $student->registerForCourse($course);
        } catch (QueryException) {
            // The unique student and course pair is the last word on duplicates, so two submits at once
            // still leave only one registration.
            return redirect()->route('registrations.create')
                ->with('error', "{$student->name} is already registered for {$course->code}.");
        }

        return redirect()->route('registrations.index')->with(
            'success',
            "{$student->name} was registered for {$course->code} ({$course->title})."
        );
    }

    /**
     * Remove the specified resource from storage. The policy allows the own student and an administrator.
     */
    public function destroy(CourseRegistration $registration): RedirectResponse
    {
        $student = $registration->student;
        $code = $registration->course->code;

        $registration->delete();

        return redirect()->route('registrations.index')->with(
            'success',
            "The registration of {$student->name} for {$code} was cancelled."
        );
    }
}
