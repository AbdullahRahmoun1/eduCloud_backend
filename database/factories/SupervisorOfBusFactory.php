<?php

namespace Database\Factories;

use App\Models\Bus;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupervisorOfBus>
 */
class SupervisorOfBusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $maxDate = date('Y-m-d', strtotime('+8 months'));
        $minDate = date('Y-m-d', strtotime('+1 months'));
        $endDate = fake()
        ->dateTimeBetween($minDate, $maxDate)
        ->format('Y-m-d');
        return [
            'start_date'=>now(),
            'end_date'=>$endDate
        ];
    }
}
