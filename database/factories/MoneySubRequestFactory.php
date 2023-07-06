<?php

namespace Database\Factories;

use App\Models\MoneyRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomError;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MoneySubRequest>
 */
class MoneySubRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'value'=>random_int(500000,1500000),
            'final_date'=>now()->addDays(random_int(1,100)),
            'money_request_id'=>MoneyRequest::all()->random()->id,          
        ];
    }
}
