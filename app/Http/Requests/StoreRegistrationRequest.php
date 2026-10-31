<?php

namespace App\Http\Requests;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRegistrationRequest extends FormRequest
{
    /**
     * A student may register themselves. Registering somebody else needs the manage registrations permission.
     */
    public function authorize(): bool
    {
        $ownStudentRecordId = $this->user()->student?->getKey();
        $submittedStudentId = (int) $this->input('student_id');

        if ($submittedStudentId > 0 && $submittedStudentId !== $ownStudentRecordId) {
            return $this->user()->can('manage registrations');
        }

        return $this->user()->can('register for course');
    }

    /**
     * A student's own record is filled in here instead of being taken from the form, so nobody can
     * register on behalf of another student by changing a hidden field.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->filled('student_id')) {
            $this->merge(['student_id' => $this->user()->student?->getKey()]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_id' => ['required', 'integer', Rule::exists(Course::class, 'id')],
            'student_id' => ['required', 'integer', Rule::exists(Student::class, 'id')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'This account has no student record yet, so an administrator must create one first.',
        ];
    }
}
