<?php

namespace Database\Factories;

use App\Models\GClass;
use App\Models\ProgressCalendar;
use App\Models\Subject;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Test>
 */
class TestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tries=0;
        do{
            $subj=Subject::all()->random();
            $classes=$subj->grade->g_classes;
        }while(count($classes)==0 && $tries++<50);
        $class=$classes->random();
        $n=random_int(1,100);
        return [
            'title'=>"$subj->name/$class->name/Test($n)",
            'image_url'=>fake()->imageUrl(40),
            'min_mark'=>random_int(1,3)*10,
            'max_mark'=>random_int(5,10)*10,
            'date'=>now()->addDays(random_int(5,50)),
            'subject_id'=>$subj->id,
            'g_class_id'=>$class->id,
            'type_id'=>Type::all()->random()->id,
        ];
    }
}
