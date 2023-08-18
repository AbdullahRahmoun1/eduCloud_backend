<?php

namespace Database\Factories;

use App\Models\Grade;
use Faker\Provider\ar_EG\Text;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public static $names = [
        "Mathematics",
        "Science",
        "English Language Arts",
        "Social Studies",
        "History",
        "Geography",
        "Physical Education",
        "Health Education",
        "Art",
        "Music",
        "Technology Education",
        "Home Economics",
        "Foreign Language",
        "Computer Science",
        "Life Skills",
        "Civics",
        "Economics",
        "Drama/Theater",
        "Environmental Science",
        "Astronomy",
        "Physical Science",
        "Biology",
        "Chemistry",
        "Physics",
        "Earth Science",
        "Literature",
        "Writing",
        "Algebra",
        "Geometry",
        "Calculus",
        "Social Psychology",
        "Anthropology"
    ];
    public function definition(): array
    {
        return [
            'name' => self::pick(),
            'min_mark' => random_int(20,40),
            'max_mark' => random_int(50,100),
            'grade_id' => Grade::all()->random()->id
        ];
    }
    public function pick(){
        $n=fake()->numberBetween(0,count(self::$names)-1);
        return self::$names[$n];
    }
    public static function pickNames($count){
        shuffle(self::$names);
        while($count--){
            $result[]=self::$names[$count];;
        }
        return $result;
    }
}
