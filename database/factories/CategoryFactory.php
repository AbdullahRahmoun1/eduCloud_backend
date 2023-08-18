<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public static $categories= [
        "General",
        "Announcement",
        "Alert",
        "Reminder",
        "Important",
        "Notification",
        "Event",
        "Information",
        "Emergency",
        "News",
        "Suggestion",
    ];
    public function definition(): array
    {
        return [
            'name'=>self::pick(),
            'send_directly'=>fake()->boolean(),
        ];
    }
    public function pick() {
        $n=random_int(0,count(self::$categories)-1);
        return self::$categories[$n];
    }
}
