<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Exception;
use App\Models\Mark;
use App\Models\Test;
use App\Models\Type;
use App\Models\Grade;
use App\Models\AtMark;
use App\Models\GClass;
use App\Models\Number;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Category;
use App\Models\Employee;
use App\Models\AtSection;
use App\Models\AbilityTest;
use App\Models\Absence;
use App\Models\Notification;
use App\Models\AtMarkSection;
use App\Models\ClassSupervisor;
use Illuminate\Database\Seeder;
use App\Models\CandidateStudent;
use App\Models\ClassTeacherSubject;
use App\Models\BaseCalendar;
use App\Models\Complaint;
use App\Models\Income;
use App\Models\MoneyRequest;
use App\Models\MoneySubRequest;
use App\Models\Address;
use App\Models\Bus;
use App\Models\BusAddress;
use App\Models\StudentBus;
use App\Models\SupervisorOfBus;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this
        ->try(fn()=>Address::factory(50)->create());
        
        Grade::create(['name' => 'السابع']);
        Grade::create(['name' => 'الثامن']);
        Grade::create(['name' => 'التاسع']);
        Grade::factory(2)->create();        
        GClass::create(['grade_id' => 1, 'name' => 'الاولى', 'max_number' => 26]);
        GClass::create(['grade_id' => 1, 'name' => 'الثانية', 'max_number' => 30]);
        GClass::create(['grade_id' => 3, 'name' => 'الاولى', 'max_number' => 28]);
        Employee::factory(5)->create();
        Student::factory(50)->create();
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(AccountSeeder::class);
        Subject::create(['name' => 'فيزيا', 'grade_id' => 1]);
        Subject::factory(9)->create();
        $this
        ->try(fn()=>GClass::factory(10)->create());
        CandidateStudent::factory(30)->create();
        ClassSupervisor::create(['employee_id' => 3, 'g_class_id' => 1]);
        ClassSupervisor::create(['employee_id' => 3, 'g_class_id' => 2]);
        ClassSupervisor::create(['employee_id' => 3, 'g_class_id' => 3]);
        ClassTeacherSubject::create(['employee_id' => 1, 'subject_id' => 1, 'g_class_id' => 2]);
        ClassTeacherSubject::create(['employee_id' => 1, 'subject_id' => 1, 'g_class_id' => 1]);
        ClassTeacherSubject::create(['employee_id' => 2, 'subject_id' => 2, 'g_class_id' => 1]);

        BaseCalendar::create(['subject_id' => 1, 'grade_id' => 1, 'title' => 'first chapter', 'is_test' => 0, 'date' => now()]);
        BaseCalendar::create(['subject_id' => 1, 'grade_id' => 1, 'title' => 'second chapter', 'is_test' => 0, 'date' => now()->addDay(4)]);
        BaseCalendar::factory(5)->create();

        Complaint::factory(30)->create();
        
        Type::create(['name'=>'سبر']);
        Type::create(['name'=>'امتحان']);
        Type::create(['name'=>'تسميع']);
        Type::create(['name'=>'مذاكرة']);


        $this
        ->try(fn()=>Absence::factory(50)->create());
        
        AbilityTest::factory(50)->create();

        AtSection::factory(200)->create();

        AtMark::factory(300)->create();

        AtMarkSection::factory(50)->create();

        Test::factory(100)->create();

        $this
        ->try(fn()=>Mark::factory(100)->create());

        Category::factory(30)->create();
        Notification::factory(100)->create();
        Notification::factory(3)->create(['owner_id' => 1, 'owner_type' => Student::class]);

        MoneyRequest::factory(100)->create();
        MoneySubRequest::factory(300)->create();
        Income::factory(500)->create();

        $this
        ->try(fn()=>Bus::factory(50)->create());
        
        $this
        ->try(fn()=>BusAddress::factory(40)->create());

        $this
        ->try(fn()=>StudentBus::factory(50)->create());

        $this
        ->try(fn()=>SupervisorOfBus::factory(50)->create());
        
        Number::factory(100)->create();
        

    }
    private function try($toTry){
        try{
            $toTry();
        }catch(Exception $e){
            logger($e->getMessage().'  in Database seeder');
        }
    }
}
