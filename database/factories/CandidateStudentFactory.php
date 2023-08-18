<?php

namespace Database\Factories;

use App\Models\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CandidateStudent>
 */
class CandidateStudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'grade_id' => Grade::all()->random()->id,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->unique()->lastName(),
            'father_name' => fake()->firstNameMale(),
            'mother_name' => fake()->firstNameFemale(),
        ];
    }
}
