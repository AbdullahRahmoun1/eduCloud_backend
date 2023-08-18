<?php

namespace Database\Factories;

use App\Models\AtMark;
use App\Models\AtSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AtMarkSection>
 */
class AtMarkSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        do{
            $mark=AtMark::all()->random();
            $sections=$mark->abilityTest->sections;
        }while(count($sections)==0);
        $atSection=$mark->abilityTest->sections->random();
        return [
            'mark'=>random_int(0,$atSection->max_mark),
            'at_mark_id'=>$mark->id,
            'at_section_id'=>$atSection->id,
        ];
    }
}
