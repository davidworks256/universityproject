<?php

namespace App\Models;

use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'student_number',
        'name',
        'email',
        'phone',
    ];

    /**
     * The account this student record belongs to. A student without an account cannot sign in.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The raw registration rows, so a registration can be looked up or cancelled on its own.
     *
     * @return HasMany<CourseRegistration, $this>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(CourseRegistration::class);
    }

    /**
     * Courses this student is registered for, reading registered_at from the pivot row.
     *
     * @return BelongsToMany<Course, $this>
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_registrations')
            ->withPivot('registered_at')
            ->withTimestamps();
    }

    /**
     * Register the student for a course, stamping the moment the registration happened.
     */
    public function registerForCourse(Course $course): CourseRegistration
    {
        $this->courses()->attach($course, ['registered_at' => now()]);

        return $this->registrations()
            ->where('course_id', $course->getKey())
            ->firstOrFail();
    }

    /**
     * The courses this student has not registered for, for the "available courses" list.
     *
     * @return Builder<Course>
     */
    public function availableCourses(): Builder
    {
        return Course::query()->notRegisteredFor($this);
    }
}
