<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reply>
 */
class ReplyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::all()->random()->id,
            'employee_id' => Employee::all()->random()->id,
            'body' => fake()->realText(35),
            'date_time' => now()
        ];
    }
}
