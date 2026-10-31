<?php

namespace App\Policies;

use App\Models\CourseRegistration;
use App\Models\User;

/**
 * A registration belongs to one student, so only that student and an administrator may change it.
 */
class RegistrationPolicy
{
    /**
     * Every role opens the registration page; the controller limits the rows to the ones they own.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('manage registrations')
            || $user->can('view course students')
            || $user->can('view own registrations');
    }

    /**
     * An administrator sees any registration, a student sees the row that carries their own record.
     */
    public function view(User $user, CourseRegistration $registration): bool
    {
        return $user->can('manage registrations') || $this->belongs($user, $registration);
    }

    /**
     * A student registers for themselves, an administrator registers on behalf of a chosen student.
     * Which student id is allowed is settled by the form request, so an account whose record is still
     * missing gets an explanation instead of a refusal.
     */
    public function create(User $user): bool
    {
        return $user->can('manage registrations') || $user->can('register for course');
    }

    /**
     * Cancelling follows the same rule as creating: own rows, or any row for an administrator.
     */
    public function delete(User $user, CourseRegistration $registration): bool
    {
        return $user->can('manage registrations') || $this->belongs($user, $registration);
    }

    /**
     * True when the registration belongs to the student record linked to this account.
     */
    private function belongs(User $user, CourseRegistration $registration): bool
    {
        return $user->student?->getKey() === $registration->student_id;
    }
}
