<?php

namespace Database\Factories;

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
        return [
            'body'=>fake()->realTextBetween(20,100),
            'owner_id'=>random_int(1,100),
            'owner_type'=>random_int(1,2)==1?Student::class:Employee::class,
            'category_id'=>random_int(1,30)
        ];
    }
}
