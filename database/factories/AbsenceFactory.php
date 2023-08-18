<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Absence>
 */
class AbsenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $j=random_int(0,1);
        return [
            'justified'=>$j,
            'justification'=>$j?$this->pick():null,
            'date'=>now()->addDays(random_int(0,120)),
            'student_id'=>Student::all()->random()->id,
        ];
    }
    private function pick() {
        $absenceJustifications = [
            "Doctor visit",
            "Family event",
            "Illness",
            "Medical appt.",
            "Dentist",
            "Religious",
            "Cold/flu",
            "School trip",
            "Headache",
            "Stomachache",
            "Allergy",
            "Fever",
            "Car issue",
            "Emergency"
        ];
        return fake()->randomElement($absenceJustifications);
    }
}
