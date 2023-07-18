<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\GClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'g_class_id' => GClass::all()->random()->id,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'father_name' => fake()->firstNameMale(),
            'mother_name' => fake()->firstNameFemale(),
            'address_id'=>Address::all()->random()->id
        ];
    }
}
