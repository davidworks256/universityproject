<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

/**
 * Courses are public information inside the system, but the list of students on a course is not.
 */
class CoursePolicy
{
    /**
     * Every role is allowed a course list; the controller decides which rows each role sees.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('manage courses')
            || $user->can('view assigned courses')
            || $user->can('view available courses');
    }

    /**
     * An administrator opens any course, a lecturer only a course assigned to them, a student any
     * course, because a student has to read the course before registering for it.
     */
    public function view(User $user, Course $course): bool
    {
        return $user->can('manage courses')
            || ($user->can('view assigned courses') && $course->lecturer_id === $user->getKey())
            || $user->can('view available courses');
    }

    /**
     * The registered students of a course are shown only to the person who teaches it.
     */
    public function viewRoster(User $user, Course $course): bool
    {
        return $user->can('manage courses') || $course->lecturer_id === $user->getKey();
    }

    public function create(User $user): bool
    {
        return $user->can('manage courses');
    }

    public function update(User $user, Course $course): bool
    {
        return $user->can('manage courses');
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->can('manage courses');
    }
}
