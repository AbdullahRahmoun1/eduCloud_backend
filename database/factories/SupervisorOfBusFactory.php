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
        return [
            'employee_id'=>Employee::all()->random()->id,
            'bus_id'=>Bus::all()->random()->id,
            'start_date'=>now(),
            'end_date'=>fake()->date()
        ];
    }
}
