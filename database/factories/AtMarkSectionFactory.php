<?php

namespace Database\Factories;

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
        return [
            'mark'=>random_int(1,100),
            'at_mark_id'=>random_int(1,90),
            'at_section_id'=>random_int(1,40),
        ];
    }
}
