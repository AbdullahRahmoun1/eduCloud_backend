<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Bus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BusAddress>
 */
class BusAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bus_id'=>Bus::all()->random()->id,
            'address_id'=>Address::all()->random()->id,
            'price'=>random_int(40000,100000)
        ];
    }
}
