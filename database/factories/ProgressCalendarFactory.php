<?php

namespace Database\Factories;

use App\Models\BaseCalendar;
use App\Models\GClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProgressCalendar>
 */
class ProgressCalendarFactory extends Factory
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
            'base_calendar_id' => BaseCalendar::all()->random()->id
        ];
    }
}
