<?php

namespace Database\Factories;

use App\Models\MoneySubRequest;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Income>
 */
class IncomeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'value'=>random_int(300000,1000000),
            'date'=>now()->addDays(random_int(0,15)),
            'notes'=>fake()->text(60),
            'student_id'=>Student::all()->random()->id,
            'money_sub_request_id'=>MoneySubRequest::all()->random()->id,
        ];
    }
}
