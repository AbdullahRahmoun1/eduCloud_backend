<?php

namespace Database\Factories;

use App\Models\AbilityTest;
use App\Models\CandidateStudent;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AtMark>
 */
class AtMarkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id'=>Student::all()->random()->id,
            'student_type'=>random_int(1,2)==2?Student::class:CandidateStudent::class,
            'ability_test_id'=>AbilityTest::all()->random()->id,
            'is_entry_mark'=>random_int(0,1),
            'date'=>fake()->date(),
        ];
    }
}
