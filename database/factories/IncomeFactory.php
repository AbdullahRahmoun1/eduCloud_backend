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
    public const NOTES = [
        "Tuition fees for September 2023.",
        "Cafeteria charges for the week.",
        "Library fines for overdue books.",
        "Extra-curricular activity fees.",
        "Science lab equipment replacement costs.",
        "Sports uniform fee.",
        "Computer lab usage fee.",
        "Arts and crafts supplies expense.",
        "Exam re-evaluation fee.",
        "Field trip contribution."
    ];
    public function definition(): array
    {
        $randomDigits = '';
        for ($i = 0; $i < 10; $i++) {
            $randomDigits .= rand(0, 9);
        }
        return [
            'date'=>now()->addDays(random_int(0,15)),
            'receipt_number'=>$randomDigits,
            'notes'=>fake()->optional()->randomElement(self::NOTES),
        ];
    }
}
