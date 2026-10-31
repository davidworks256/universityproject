<?php

namespace App\Http\Requests;

use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    /**
     * Only an administrator may change students.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage students');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $student = $this->route('student');

        return [
            'student_number' => ['required', 'string', 'max:255', Rule::unique(Student::class)->ignore($student)],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(Student::class)->ignore($student),
                Rule::unique(User::class)->ignore($student->user_id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'student_number' => 'student number',
        ];
    }
}
