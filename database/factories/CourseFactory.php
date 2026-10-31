<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('BUC###??'),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'credits' => fake()->numberBetween(1, 10),
            'lecturer_id' => User::factory(),
        ];
    }

    /**
     * A course that has not been assigned to a lecturer yet.
     */
    public function unassigned(): static
    {
        return $this->state(fn (array $attributes): array => [
            'lecturer_id' => null,
        ]);
    }
}
