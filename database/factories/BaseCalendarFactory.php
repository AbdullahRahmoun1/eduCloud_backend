<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BaseCalendar>
 */
class BaseCalendarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subject = Subject::all()->random();
        return [
            'subject_id' => $subject->id,
            'grade_id' => $subject->grade_id,
            'title' => fake()->text(10),
            'is_test' => random_int(0,1),
            'date' => now()->addDays(random_int(5,20)),
        ];
    }
}
