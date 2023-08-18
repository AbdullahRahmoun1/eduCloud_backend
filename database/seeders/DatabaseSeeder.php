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
use App\Models\ProgressCalendar;
use App\Models\Reply;
use App\Models\StudentBus;
use App\Models\SupervisorOfBus;
use Database\Factories\BusAddressFactory;
use Database\Factories\CategoryFactory;
use Database\Factories\GClassFactory;
use Database\Factories\SubjectFactory;
use Illuminate\Database\QueryException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //addresses
        $this->try(
            fn()=>Address::factory(50)->create()
        );
        //grade , subjects , classes
        $this->try(function(){
            for($i=1;$i<=12;$i++){
                //create grade
                $grade=Grade::create([
                    'name'=>"{$i}th grade"
                ]);
                //create classes for it
                foreach(GClassFactory::NAMES as $name){
                    GClass::factory()->create([
                        'grade_id'=>$grade->id,
                        'name'=>$name
                    ]);
                }
                //create subjects for it
                $sub_names=SubjectFactory::pickNames(6);
                foreach($sub_names as $subName){
                    Subject::factory()->create([
                        'grade_id'=>$grade->id,
                        'name'=>$subName
                    ]);
                }
            }
        });
        //employees
        $this->try(
            fn() => Employee::factory(25)->create()
        );
        //students
        $this->try(
            fn() => Student::factory(90)->create()
        );
        //roles
        $this->try(
            fn()=>$this
            ->call(RolesAndPermissionsSeeder::class)
        );
        //accounts
        $this->try(
            fn()=>$this->call(AccountSeeder::class)
        );
        //candidate students
        $this->try(
            fn()=>CandidateStudent::factory(30)->create()
        );

        //add supervisors to  classes and teachers to subjects
        foreach(GClass::all() as $class){
            //first: assign supervisor
            $this->try(function () use($class) {
                $employee=Employee::all()->random();
                ClassSupervisor::create([
                    'employee_id'=>$employee->id,
                    'g_class_id'=>$class->id,
                ]);
                $role=config('roles.supervisor');
                if(!$employee->hasRole($role))
                $employee->assignRole($role);
            });
            //now assign teachers for subjects
            foreach($class->grade->subjects as $subject){
                $this->try(function () use($class,$subject) {
                    $employee=Employee::all()->random();
                    ClassTeacherSubject::create([
                        'employee_id'=>$employee->id,
                        'g_class_id'=>$class->id,
                        'subject_id'=>$subject->id
                    ]);
                    $role=config('roles.teacher');
                    if(!$employee->hasRole($role))
                    $employee->assignRole($role);
                });
            }
        }
        //FIXME base calenders
        $this->try(
            fn() => BaseCalendar::factory(100)->create()
        );
        //FIXME progress calenders
        $this->try(
            fn() => ProgressCalendar::factory(30)->create()
        );
        //complaints
        $this->try(
            fn() => Complaint::factory(60)->create()
        );
        //replies
        $this
        ->try(
            fn() => Reply::factory(60)->create()
        );
        //FIXME types
        Type::create(['name'=>'سبر']);
        Type::create(['name'=>'امتحان']);
        Type::create(['name'=>'تسميع']);
        Type::create(['name'=>'مذاكرة']);
        // absences
        $this->try(
            fn() => Absence::factory(50)->create()
        );
        // ability tests
        $this->try(
            fn() => AbilityTest::factory(50)->create()
        );
        //ability test section
        foreach(AbilityTest::all() as $at){
            $ctr=6;
            while($ctr){
                $this->try(
                    fn()=>AtSection::factory()->create([
                        'ability_test_id'=>$at->id,
                        'name'=>"section($ctr)"
                    ])
                );
                $ctr--;
            }
        }
        //at marks
        $this->try(
            fn() => AtMark::factory(300)->create()
        );
        //at mark sections
        foreach(AtMark::all() as $mark){
            $sections=$mark->abilityTest->sections;
            foreach($sections as $section){
                $max= $section->max_mark;
                $this->try(
                    fn()=>
                    AtMarkSection::create([
                        'at_mark_id'=>$mark->id,
                        'at_section_id'=>$section->id,
                        'mark'=>random_int(0,$max)
                    ])
                );
            }
        }
        //FIXME too little data,tests
        $this->try(
            fn() => Test::factory(60)->create()
        );
        //test marks
        $this->try(
            fn() => Mark::factory(250)->create()
        );
        //notification categories
        foreach(CategoryFactory::$categories as $cat){
            Category::factory()->create([
                'name'=>$cat
            ]);
        }
        //FIXME notifications
        $this->try(
            fn() => Notification::factory(150)->create()
        );
        //bills
        foreach(Student::all() as $student){
            $n=random_int(1,2);
            if($n-->0){
                MoneyRequest::factory()->create([
                    'student_id'=>$student->id,
                    'type'=>MoneyRequest::SCHOOL
                ]);
            }
            if($n-->0){
                MoneyRequest::factory()->create([
                    'student_id'=>$student->id,
                    'type'=>MoneyRequest::BUS    
                ]);
            }
        }
        //bills_sections
        $this->try(
            fn() => MoneySubRequest::factory(300)->create()
        );
        MoneyRequest::whereDoesntHave('moneySubRequests')->delete();
        //student payments
        foreach(Student::all() as $student){
            foreach($student->moneyRequests as $mr){
                foreach($mr->moneySubRequests as $subReq){
                    $payFully=random_int(0,1);
                    $value=$payFully
                    ?$subReq->value
                    :random_int($subReq->value/4,$subReq->value);
                    Income::factory()->create([
                        'student_id'=>$student->id,
                        'type'=>$mr->type,
                        'value'=>$value
                    ]);
                }
            }
        }
        //buses
        $this->try(
            fn() => Bus::factory(15)->create()
        );
        //distribute students to buses
        foreach(Student::all() as $student){
            if($student->transportation_subscriber){
                $this->try(fn () =>
                    StudentBus::create([
                        'student_id'=>$student->id,
                        'bus_id'=>Bus::all()->random()->id,
                    ])
                );
            }
        }
        //assign supervisors of buses..
        foreach(Bus::all() as $bus){
            //assign addresses
            $numOfAddresses=random_int(3,8);
            $addresses=BusAddressFactory::pickNAddress($numOfAddresses);
            foreach($addresses as $address){
                $this->try(
                    fn()=>
                    BusAddress::factory()->create([
                        'bus_id'=>$bus->id,
                        'address_id'=>$address->id,
                    ])
                );
            }
            //assign supervisors
            $employee=Employee::all()->random();
            $role=config('roles.busSupervisor');
            if(!$employee->hasRole($role))
            $employee->assignRole($role);
            $this->try(
                fn()=>
                SupervisorOfBus::factory()->create([
                    'bus_id'=>$bus->id,
                    'employee_id'=>$employee->id
                ])
            );
        }
        //numbers
        $this->try(
            fn() => Number::factory(300)->create()
        );
    }
    private function try($toTry){
        try{
            $toTry();
        }catch(QueryException $e){
            info($e->getMessage().'  in Database seeder'.PHP_EOL
            ."---------------------------------------------");
        }
        catch(Exception $e){
            error_log($e->getMessage().'  in Database seeder'.PHP_EOL
            ."---------------------------------------------");
        }
    }
}
