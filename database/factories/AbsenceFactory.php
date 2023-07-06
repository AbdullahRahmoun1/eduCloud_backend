<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Absence>
 */
class AbsenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'justified'=>random_int(0,1),
            'justification'=>fake()->text(50),
            'date'=>now()->addDays(random_int(0,50)),
            'student_id'=>Student::all()->random()->id,
        ];
    }
}
