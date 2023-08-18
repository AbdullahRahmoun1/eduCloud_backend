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
    private const PROFESSIONS = [
        "Teacher","Doctor","Engineer","Artist","Chef",
        "Designer","Writer","Musician","Lawyer","Accountant",
        "Photographer","Scientist","Architect","Athlete","Journalist",
        "Police Officer","Firefighter","Veterinarian","Electrician",
        "Dentist"
    ];
    
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
            'last_name' => fake()->unique()->lastName(),
            'father_name' => fake()->firstNameMale(),
            'mother_name' => fake()->firstNameFemale(),
            'address_id'=>Address::all()->random()->id,
            'transportation_subscriber'=>random_int(0,1),
            'grand_father_name'=>fake()->firstNameMale(),
            'mother_last_name'=>fake()->lastName(),
            'father_alive'=>random_int(0,1),
            'mother_alive'=>random_int(0,1),
            'father_profession'=>fake()->optional()->randomElement(self::PROFESSIONS),
            'registration_place'=>fake()->state,
            'birth_place'=>fake()->state,
            'place_of_living'=>fake()->state,
            'birth_date'=>fake()->dateTimeBetween('-18 years','-6 years'),
            'registration_number'=>fake()->numberBetween(1,222000),
            'registration_date'=>fake()->date()
        ];
    }
}
