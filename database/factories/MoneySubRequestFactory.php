<?php

namespace Database\Factories;

use App\Models\MoneyRequest;
use App\Models\Student;
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
        $mr=MoneyRequest::all()->random();
        $subsCount=$mr->moneySubRequests()->count()+1;
        $newPrice = intval($mr->value / $subsCount);
        $newPrice += $mr->value % ($subsCount);
        $mr->moneySubRequests()->update(['value'=>$newPrice]);
        return [
            'value'=>$newPrice,
            'final_date'=>now()->addDays(random_int(1,100)),
            'money_request_id'=>$mr->id,          
        ];
    }
}
