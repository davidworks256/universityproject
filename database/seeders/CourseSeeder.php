<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Five courses, each taught by one of the two lecturers created by the UserSeeder.
     *
     * @var list<array{code: string, title: string, description: string, credits: int, lecturer_email: string}>
     */
    private const COURSES = [
        [
            'code' => 'BUC1101',
            'title' => 'Introduction to Database Systems',
            'description' => 'Relational modelling, normalisation and structured query language.',
            'credits' => 4,
            'lecturer_email' => 'lecturer@example.com',
        ],
        [
            'code' => 'BUC1202',
            'title' => 'Web Application Development',
            'description' => 'Building server rendered web applications with the Laravel framework.',
            'credits' => 6,
            'lecturer_email' => 'lecturer@example.com',
        ],
        [
            'code' => 'BUC2303',
            'title' => 'Object Oriented Programming',
            'description' => 'Classes, inheritance, interfaces and design principles.',
            'credits' => 5,
            'lecturer_email' => 'lecturer2@example.com',
        ],
        [
            'code' => 'BUC2404',
            'title' => 'Computer Networks',
            'description' => 'Network layers, addressing, routing and secure transport.',
            'credits' => 4,
            'lecturer_email' => 'lecturer2@example.com',
        ],
        [
            'code' => 'BUC3505',
            'title' => 'Software Engineering Practice',
            'description' => 'Requirements, version control, testing and team delivery.',
            'credits' => 8,
            'lecturer_email' => 'lecturer@example.com',
        ],
    ];

    public function run(): void
    {
        $lecturersByEmail = User::role('lecturer')->get()->keyBy('email');

        foreach (self::COURSES as $course) {
            Course::firstOrCreate(
                ['code' => $course['code']],
                [
                    'title' => $course['title'],
                    'description' => $course['description'],
                    'credits' => $course['credits'],
                    'lecturer_id' => $lecturersByEmail->get($course['lecturer_email'])->getKey(),
                ],
            );
        }
    }
}
