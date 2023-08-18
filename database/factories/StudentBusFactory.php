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
        $student=Student::all()->random();
        $hasBus=$student->bus()->count()>=1;
        if($hasBus || !$student->transportation_subscriber)
        return [];
        return [
            'student_id'=>$student->id,
            'bus_id'=>Bus::all()->random()->id,
        ];
    }
}
