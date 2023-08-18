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
        $at=AbilityTest::all()->random();
        $n = random_int(1,100);
        return [
            'name'=>"section($n)",
            'max_mark'=>random_int(3,6)*10,
            'min_mark'=>random_int(1,2)*10,
            'ability_test_id'=>$at->id,
        ];
    }
}
