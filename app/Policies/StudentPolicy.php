<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

/**
 * Student records belong to one person, so only an administrator or that person may open them.
 */
class StudentPolicy
{
    /**
     * Only an administrator opens the list of all students.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('manage students');
    }

    /**
     * An administrator opens any record, a student opens only their own.
     */
    public function view(User $user, Student $student): bool
    {
        return $user->can('manage students') || $user->getKey() === $student->user_id;
    }

    /**
     * New students are created by an administrator, because the record also creates a sign-in account.
     */
    public function create(User $user): bool
    {
        return $user->can('manage students');
    }

    public function update(User $user, Student $student): bool
    {
        return $user->can('manage students');
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->can('manage students');
    }
}
