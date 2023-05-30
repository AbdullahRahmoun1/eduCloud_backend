<?php

namespace Database\Factories;

use App\Models\Grade;
use Faker\Provider\ar_EG\Text;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => Str::random(10),
            'min_mark' => random_int(20,40),
            'max_mark' => random_int(50,100),
            'grade_id' => Grade::all()->random()->id
        ];
    }
}
