<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Six students. Each one gets a sign-in account with the student role, because the student record
     * has a required foreign key to users.
     *
     * @var list<array{student_number: string, name: string, email: string, phone: string|null}>
     */
    private const STUDENTS = [
        ['student_number' => 'BUC/2024/0001', 'name' => 'Susan Achan', 'email' => 'susan.achan@student.mubs.ac.ug', 'phone' => '+256700000001'],
        ['student_number' => 'BUC/2024/0002', 'name' => 'Peter Okello', 'email' => 'peter.okello@student.mubs.ac.ug', 'phone' => '+256700000002'],
        ['student_number' => 'BUC/2024/0003', 'name' => 'Grace Namutebi', 'email' => 'grace.namutebi@student.mubs.ac.ug', 'phone' => null],
        ['student_number' => 'BUC/2024/0004', 'name' => 'Daniel Ssekandi', 'email' => 'daniel.ssekandi@student.mubs.ac.ug', 'phone' => '+256700000004'],
        ['student_number' => 'BUC/2024/0005', 'name' => 'Aisha Balikemwa', 'email' => 'aisha.balikemwa@student.mubs.ac.ug', 'phone' => null],
        ['student_number' => 'BUC/2024/0006', 'name' => 'Emmanuel Turyahikayo', 'email' => 'emmanuel.turyahikayo@student.mubs.ac.ug', 'phone' => '+256700000006'],
    ];

    public function run(): void
    {
        foreach (self::STUDENTS as $student) {
            $user = User::firstOrCreate(
                ['email' => $student['email']],
                ['name' => $student['name'], 'password' => 'password'],
            );

            $user->syncRoles(['student']);

            Student::firstOrCreate(
                ['student_number' => $student['student_number']],
                [
                    'user_id' => $user->getKey(),
                    'name' => $student['name'],
                    'email' => $student['email'],
                    'phone' => $student['phone'],
                ],
            );
        }
    }
}
