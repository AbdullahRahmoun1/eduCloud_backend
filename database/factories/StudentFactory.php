<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\GClass;
use App\Models\Grade;
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
        $g=Grade::all()->random();
        $gc=$g->g_classes;
        $c=$gc->isEmpty()?null:$gc->random()->id;
        // $c=random_int(0,1)?($gc->isEmpty()?null:$gc->random()->id):null;
        return [
            'grade_id' => $g,
            'g_class_id' => $c,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'father_name' => fake()->firstNameMale(),
            'mother_name' => fake()->firstNameFemale(),
            'address_id'=>Address::all()->random()->id
        ];
    }
}
