<?php

namespace Database\Seeders;

use Exception;
use App\Models\Grade;
use App\Models\GClass;
use App\Models\Address;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Employee;
use App\Models\BaseCalendar;
use Illuminate\Database\Seeder;
use App\Models\CandidateStudent;
use Illuminate\Database\QueryException;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this
        ->try(fn() => Address::factory(30)->create());

        $this
        ->try(function(){
            for($i=1;$i<=12;$i++){
                Grade::create([
                    'name'=>"{$i}th grade"
                ]);
            }
        });

        $this
        ->try(fn() => GClass::factory(30)->create());

        $this
        ->try(fn() => Employee::factory(20)->create());

        $this
        ->try(fn() => Student::factory(90)->create());

        $this->try(
            fn()=>$this
            ->call(RolesAndPermissionsSeeder::class)
        );

        $this
        ->try(
            fn()=>$this->call(AccountSeeder::class)
        );

        $this
        ->try(fn() => Subject::factory(45)->create());

        $this
        ->try(fn()=>CandidateStudent::factory(30)->create());

        $this
        ->try(fn() => BaseCalendar::factory(100)->create());

    }
    private function try($toTry){
        try{
            $toTry();
        }catch(QueryException $e){
            error_log($e->getMessage().'  in Database seeder'.PHP_EOL
            ."---------------------------------------------");
        }catch(Exception $e){
            error_log($e->getMessage().'  in Database seeder'.PHP_EOL
            ."---------------------------------------------");
        }
    }
}
