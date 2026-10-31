<?php

namespace App\Http\Requests;

use App\Models\Course;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
{
    /**
     * Only an administrator may change courses.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage courses');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $course = $this->route('course');

        return [
            'code' => ['required', 'string', 'max:20', Rule::unique(Course::class)->ignore($course)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'credits' => ['required', 'integer', 'between:1,10'],
            'lecturer_id' => [
                'required',
                'integer',
                Rule::exists(User::class, 'id'),
                // The drop-down only lists lecturer accounts, so an id from outside that list is refused.
                function (string $attribute, $value, Closure $fail): void {
                    if (! User::query()->role('lecturer')->whereKey($value)->exists()) {
                        $fail('The selected lecturer must be an account with the lecturer role.');
                    }
                },
            ],
        ];
    }
}
