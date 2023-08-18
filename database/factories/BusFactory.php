<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bus>
 */
class BusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $n=random_int(1,100);
        return [
            'name'=>fake()->city.".Bus($n)",
            'driver_name'=>fake()->name('male'),
            'max_load'=>random_int(20,35),
        ];
    }
}
