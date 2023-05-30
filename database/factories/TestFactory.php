<?php

namespace Database\Factories;

use App\Models\GClass;
use App\Models\Subject;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Test>
 */
class TestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'=>'someTitle',
            'image_url'=>'someImageUrl',
            'min_mark'=>random_int(1,30),
            'max_mark'=>random_int(31,200),
            'date'=>now()->addDays(random_int(5,50)),
            'subject_id'=>Subject::all()->random()->id,
            'g_class_id'=>GClass::all()->random()->id,
            'type_id'=>Type::all()->random()->id,
            'progress_calendar_id'=>fake()->unique()->numberBetween(1,100),
        ];
    }
}
