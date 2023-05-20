<?php

namespace Database\Factories;

use App\Models\AbilityTest;
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
            'student_id'=>random_int(1,100),
            //TODO: replace with path
            'student_type'=>random_int(1,2)==2?'student':'candidate',
            'ability_test_id'=>random_int(1,50),
            'full_mark'=>random_int(1,100),
            'date'=>fake()->date(),
        ];
    }
}
