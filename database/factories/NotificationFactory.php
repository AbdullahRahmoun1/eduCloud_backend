<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Employee;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type=random_int(0,1);
        $to=$type
        ?Student::all()->random()
        :Employee::all()->random();
        return [
            'body'=>fake()->realTextBetween(20,45),
            'owner_id'=>$to,
            'owner_type'=>$to::class,
            'date'=>now()->addDay(random_int(10,50)),
            'category_id'=>Category::all()->random()->id,
            'approved' => fake()->boolean(),
        ];
    }
}
