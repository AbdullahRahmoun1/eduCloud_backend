<?php

namespace Database\Factories;

use App\Http\Controllers\BusController;
use App\Models\Bus;
use App\Models\CandidateStudent;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Number>
 */
class NumberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array{
        $type=random_int(1,3);
        return [
            'number'=>fake()->phoneNumber,
            'owner_id'=>$type==1?Student::all()->random()->id:
            ($type==2?CandidateStudent::all()->random()->id:Bus::all()->random()->id),
            'owner_type'=>$type==1?Student::class:($type==2?CandidateStudent::class:Bus::class),
        ];
    }
}
