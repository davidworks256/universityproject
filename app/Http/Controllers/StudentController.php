<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource. Route middleware requires the manage students permission.
     */
    public function index(): View
    {
        return view('students.index', [
            'students' => Student::query()->withCount('courses')->orderBy('student_number')->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('students.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $details = $request->validated();

        $student = DB::transaction(function () use ($details): Student {
            // Every student record needs an account, because the record has a required foreign key to users.
            $account = User::create([
                'name' => $details['name'],
                'email' => $details['email'],
                'password' => 'password',
            ]);

            $account->assignRole('student');

            return Student::create([
                'user_id' => $account->getKey(),
                'student_number' => $details['student_number'],
                'name' => $details['name'],
                'email' => $details['email'],
                'phone' => $details['phone'],
            ]);
        });

        return redirect()->route('students.index')->with(
            'success',
            "Student {$student->student_number} was created and can sign in as {$student->email} with the password \"password\"."
        );
    }

    /**
     * Display the specified resource. The policy decides: an administrator or the student themselves.
     */
    public function show(Student $student): View
    {
        $student->load(['user', 'courses.lecturer']);

        return view('students.show', ['student' => $student]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student): View
    {
        return view('students.edit', ['student' => $student]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $details = $request->validated();

        DB::transaction(function () use ($student, $details): void {
            $student->update($details);

            // The student email is also the sign-in address, so the account follows the record.
            $student->user->update([
                'name' => $details['name'],
                'email' => $details['email'],
            ]);
        });

        return redirect()->route('students.index')
            ->with('success', "Student {$student->student_number} was updated.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student): RedirectResponse
    {
        $studentNumber = $student->student_number;

        // The account is the parent row, so removing it cascades to the student record and its registrations.
        $student->user->delete();

        return redirect()->route('students.index')
            ->with('success', "Student {$studentNumber} and the linked sign-in account were deleted.");
    }
}
