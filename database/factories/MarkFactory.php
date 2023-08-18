<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\Test;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mark>
 */
class MarkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $test=Test::all()->random();
        return [
            'mark'=>random_int(1,$test->max_mark),
            'student_id'=>Student::all()->random()->id,
            'test_id'=>$test->id,            
        ];
    }
}
