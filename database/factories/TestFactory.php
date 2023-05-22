<?php

namespace Database\Factories;

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
            'subject_id'=>random_int(1,10),
            'g_class_id'=>random_int(1,3),
            'type_id'=>random_int(1,4),
            'progress_calendar_id'=>fake()->unique()->numberBetween(1,100),
        ];
    }
}
