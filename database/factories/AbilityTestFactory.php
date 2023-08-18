<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AbilityTest>
 */
class AbilityTestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $s=Subject::all()->random();
        $n=random_int(0,100);
        return [
            'title' => "$s->name/Ability test ($n)",
            'subject_id'=>$s->id,
        ];
    }
}
