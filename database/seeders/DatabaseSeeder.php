<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\AbilityTest;
use App\Models\Account;
use App\Models\AtMark;
use App\Models\AtMarkSection;
use App\Models\AtSection;
use App\Models\CandidateStudent;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Number;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\GClass;
use App\Models\Mark;
use App\Models\Notification;
use App\Models\Test;
use App\Models\Type;
use App\Models\ClassTeacherSubject;
use App\Models\ClassSupervisor;
use App\Models\Student;
use Exception;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Account::create([
            'password'=>'12345',
            'user_name'=>'admin',
            'owner_id'=>'1',
            'owner_type'=>Employee::class,
        ]);
        
        Employee::factory(5)->create();

        Grade::create(['name' => 'السابع']);
        Grade::create(['name' => 'الثامن']);
        Grade::create(['name' => 'التاسع']);
        Grade::factory(2)->create();
        
        Subject::create(['name' => 'فيزيا', 'grade_id' => 1]);
        Subject::factory(9)->create();
        
        
        GClass::create(['grade_id' => 1, 'name' => 'الاولى', 'max_number' => 26]);
        GClass::create(['grade_id' => 1, 'name' => 'الثانية', 'max_number' => 30]);
        GClass::create(['grade_id' => 3, 'name' => 'الاولى', 'max_number' => 28]);
        GClass::factory(2)->create();
        
        Student::factory(50)->create();
        CandidateStudent::factory(30)->create();
        
        ClassSupervisor::create(['employee_id' => 3, 'g_class_id' => 1]);
        ClassSupervisor::create(['employee_id' => 3, 'g_class_id' => 2]);
        ClassSupervisor::create(['employee_id' => 3, 'g_class_id' => 3]);
        
        ClassTeacherSubject::create(['employee_id' => 1, 'subject_id' => 1, 'g_class_id' => 2]);
        ClassTeacherSubject::create(['employee_id' => 1, 'subject_id' => 1, 'g_class_id' => 1]);
        ClassTeacherSubject::create(['employee_id' => 2, 'subject_id' => 2, 'g_class_id' => 1]);

        Type::create(['name'=>'سبر']);
        Type::create(['name'=>'امتحان']);
        Type::create(['name'=>'تسميع']);
        Type::create(['name'=>'مذاكرة']);

        Number::factory(100)->create();

        AbilityTest::factory(50)->create();

        AtSection::factory(200)->create();

        AtMark::factory(300)->create();

        AtMarkSection::factory(50)->create();

        Test::factory(100)->create();

        try{Mark::factory(100)->create();}
        catch(Exception $e){}
        Category::factory(30)->create();
        Notification::factory(100)->create();
    }
}
