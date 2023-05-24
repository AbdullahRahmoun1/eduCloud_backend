<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GClass>
 */
class GClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'grade_id' => random_int(1,5),
            'name' => fake()->randomElement(['الاولى','السادسة','الخامسة','الرابعة','الثالثة','الثانية']),
            'max_number' => random_int(25,30),
        ];
    }
}
