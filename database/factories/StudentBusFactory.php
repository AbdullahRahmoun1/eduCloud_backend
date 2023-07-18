<?php

namespace Database\Factories;

use App\Models\Bus;
use App\Models\Student;
use PHPUnit\Framework\MockObject\Builder\Stub;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentBus>
 */
class StudentBusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id'=>Student::all()->random()->id,
            'bus_id'=>Bus::all()->random()->id,
        ];
    }
}
