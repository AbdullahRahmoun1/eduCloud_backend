<?php

namespace Database\Factories;

use App\Models\CandidateStudent;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Number>
 */
class NumberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array{
        $type=random_int(1,2);
        return [
            'number'=>fake()->phoneNumber,
            'owner_id'=>$type==1?Student::all()->random()->id:CandidateStudent::all()->random()->id,
            'owner_type'=>$type==1?Student::class:CandidateStudent::class,
        ];
    }
}
