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
        $type=random_int(1,2);
        return [
            'body'=>fake()->realTextBetween(20,100),
            'owner_id'=>$type==1?Student::all()->random()->id:Employee::all()->random()->id,
            'owner_type'=>$type==1?Student::class:Employee::class,
            'date'=>now(),
            'category_id'=>Category::all()->random()->id,
            'approved' => fake()->boolean(),
        ];
    }
}
