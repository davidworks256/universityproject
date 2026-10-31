<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $canManageCourses = $user->can('manage courses');

        $courses = Course::query()->with('lecturer')->withCount('students')->orderBy('code');

        if (! $canManageCourses && $user->hasRole('lecturer')) {
            $courses->where('lecturer_id', $user->getKey());
        }

        if (! $canManageCourses && $user->student !== null) {
            $courses->notRegisteredFor($user->student);
        }

        return view('courses.index', [
            'courses' => $courses->paginate(10),
            'scope' => match (true) {
                $canManageCourses => 'all',
                $user->hasRole('lecturer') => 'assigned',
                default => 'available',
            },
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        abort_unless($request->user()->can('manage courses'), 403, 'Only an administrator can add courses.');

        return view('courses.create', ['lecturers' => $this->lecturerAccounts()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $course = Course::create($request->validated());

        return redirect()->route('courses.show', $course)
            ->with('success', "Course {$course->code} was created.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Course $course): View
    {
        $user = $request->user();

        // A lecturer may open the registered students of a course they teach, and no other.
        abort_unless(
            $user->cannot('view course students') || $user->can('manage courses') || $course->lecturer_id === $user->getKey(),
            403,
            'You may only open the roster of a course assigned to you.'
        );

        $course->load(['lecturer', 'students.user']);

        return view('courses.show', ['course' => $course]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Course $course): View
    {
        abort_unless($request->user()->can('manage courses'), 403, 'Only an administrator can change courses.');

        return view('courses.edit', [
            'course' => $course,
            'lecturers' => $this->lecturerAccounts(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $course->update($request->validated());

        return redirect()->route('courses.show', $course)
            ->with('success', "Course {$course->code} was updated.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Course $course): RedirectResponse
    {
        abort_unless($request->user()->can('manage courses'), 403, 'Only an administrator can delete courses.');

        if ($course->registrations()->exists()) {
            return redirect()->route('courses.show', $course)->with(
                'error',
                "Course {$course->code} still has registered students, so it cannot be deleted. Cancel those registrations first."
            );
        }

        $code = $course->code;
        $course->delete();

        return redirect()->route('courses.index')
            ->with('success', "Course {$code} was deleted.");
    }

    /**
     * The accounts a course can be assigned to.
     *
     * @return Collection<int, User>
     */
    private function lecturerAccounts(): Collection
    {
        return User::query()->role('lecturer')->orderBy('name')->get();
    }
}
