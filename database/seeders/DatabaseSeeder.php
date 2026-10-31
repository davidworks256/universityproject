<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // UserSeeder, StudentSeeder, CourseSeeder and RegistrationSeeder are appended here by the Q2 branch.
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);
    }
}
