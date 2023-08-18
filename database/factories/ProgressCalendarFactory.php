<?php

namespace Database\Factories;

use App\Http\Controllers\ProgressCalendarController;
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
        $class = GClass::all()->random();
        $controller = new ProgressCalendarController();
        $plan = $controller->getProgressOfClass($class->id, false)
        ->where('done',false)->random();
        return [
            'g_class_id' => $class->id,
            'base_calendar_id' => $plan->id
        ];
    }
}
