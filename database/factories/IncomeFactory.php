<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Support\Str;
use App\Models\MoneySubRequest;
use App\Models\MoneyRequest as mr;
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
        $randomDigits = '';
        for ($i = 0; $i < 10; $i++) {
            $randomDigits .= rand(0, 9);
        }
        return [
            'value'=>random_int(300000,1000000),
            'date'=>now()->addDays(random_int(0,15)),
            'receipt_number'=>$randomDigits,
            'notes'=>fake()->text(60),
            'type'=>random_int(0,1)?mr::SCHOOL:mr::BUS,
            'student_id'=>Student::all()->random()->id,
        ];
    }
}
