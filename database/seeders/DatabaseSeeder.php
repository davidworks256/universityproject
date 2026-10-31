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
        // Roles and permissions come first because the other seeders assign them to the accounts they create.
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            StudentSeeder::class,
            CourseSeeder::class,
            RegistrationSeeder::class,
        ]);
    }
}
