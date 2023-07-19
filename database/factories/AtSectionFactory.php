<?php

namespace Database\Factories;

use App\Models\AbilityTest;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AtSection>
 */
class AtSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'=>Str::random(20),
            'max_mark'=>random_int(30,60),
            'min_mark'=>random_int(30,60),
            'ability_test_id'=>AbilityTest::all()->random()->id,
        ];
    }
}
