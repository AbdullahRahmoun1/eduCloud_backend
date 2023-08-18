<?php

namespace Database\Factories;

use App\Models\MoneyRequest;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MoneyRequest>
 */
class MoneyRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $student=Student::all()->random();
        $requests=$student->moneyRequests;
        $sc=random_int(0,1);
        return [
            'value'=>random_int(min:$sc?1000000:500000,max:$sc?5000000:2000000),
            'notes'=>fake()->optional()->randomElement(IncomeFactory::NOTES),
            'type'=>$sc?MoneyRequest::SCHOOL:MoneyRequest::BUS,
            'student_id'=>$student->id,
        ];
    }
}
