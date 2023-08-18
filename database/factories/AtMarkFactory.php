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
        $at=AbilityTest::all()->random();
        $type=random_int(0,1);
        $student=$type
        ?Student::all()->random()
        :CandidateStudent::all()->random();
        return [
            'student_id'=>$student->id,
            'student_type'=>$student::class,
            'ability_test_id'=>$at->id,
            'is_entry_mark'=>random_int(0,1),
            'date'=>fake()->date(),
        ];
    }
}
