<?php

namespace Database\Factories;

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
        return [
            'number'=>fake()->phoneNumber,
            'owner_id'=>random_int(1,100),
            'owner_type'=>random_int(1,2)==2?Student::class:CandidateStudent::class,
        ];
    }
}
