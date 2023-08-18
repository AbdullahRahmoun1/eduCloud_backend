<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Grade;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GClass>
 */
class GClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public const NAMES=[
        'A','B','C','D','E'
    ];
    public function definition(): array
    {
        $ctr=0;
        do{
            $g=Grade::all()->random();
        }while($g->g_classes()->count()>5 && $ctr++>10);

        // $l=Str::upper(fake()->unique()->randomLetter());
        // $n=random_int(0,10);
        return [
            'grade_id' => $g->id,
            // 'name' => "$l$n",
            'max_number' => random_int(25,30),
        ];
    }

}
