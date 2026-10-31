<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Seeder;

class RegistrationSeeder extends Seeder
{
    /**
     * Two or three courses per student, keyed by the student numbers used by the StudentSeeder.
     *
     * @var array<string, list<string>>
     */
    private const REGISTRATIONS = [
        'BUC/2024/0001' => ['BUC1101', 'BUC1202', 'BUC2303'],
        'BUC/2024/0002' => ['BUC1101', 'BUC3505'],
        'BUC/2024/0003' => ['BUC1202', 'BUC2404', 'BUC3505'],
        'BUC/2024/0004' => ['BUC2303', 'BUC2404'],
        'BUC/2024/0005' => ['BUC1101', 'BUC2303'],
        'BUC/2024/0006' => ['BUC1202', 'BUC2404', 'BUC3505'],
    ];

    public function run(): void
    {
        $studentsByNumber = Student::query()->get()->keyBy('student_number');
        $coursesByCode = Course::query()->get()->keyBy('code');

        foreach (self::REGISTRATIONS as $studentNumber => $courseCodes) {
            $student = $studentsByNumber->get($studentNumber);

            // Attaches only the pairs that are missing, so running the seeder again leaves the table unchanged.
            $student->courses()->syncWithoutDetaching(
                collect($courseCodes)->map(fn (string $code): int => $coursesByCode->get($code)->getKey())
            );
        }
    }
}
