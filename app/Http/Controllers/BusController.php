<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter as res;
use App\Models\Bus;
use App\Models\Student;
use App\Models\StudentBus;

class BusController extends Controller
{
    public function getBusesSupervisedBy($sup_id){
        if($sup_id==-1)
        $sup_id=request()->user()->owner->id;
        $supervisor=Employee::find($sup_id);
        //does this employee have the required role
        if(!$supervisor->hasRole(config('roles.busSupervisor'))){
            res::error(
                "This employee isn't a bus supervisor",
                code:422
            );
        }
        // good now return his busses
        $result=$supervisor->buses;
        res::success(data:$result);
    }
    public function studentsInBus(Bus $bus) {
        Helper::tryToReadBus($bus->id);
        $bus->load([
            'students:id,first_name,last_name,grade_id,g_class_id',
            'students.grade:id,name',
            'students.g_class:id,name',
            'students.numbers'
            ,
        ]);
        $students=$bus->students
        ->map(
            fn($s)=>$s->append('isAbsentToday')
        );
        res::success(data:$students);
    }
    public function getBuses() {
        $buses=Bus::with([
            'supervisors'
        ])->get();
        res::success(data:$buses);
    }
    public function get(Bus $bus) {
        $bus->load([
            'supervisors',
            'students:id,first_name,last_name,grade_id,g_class_id',
            'students.grade:id,name',
            'students.g_class:id,name',
            'driver_numbers',
            'addresses'
        ]);
    
        res::success(data:$bus);
    }
    public function setBusStudents() {
        $data = request()->validate([
            'allow_moving'=>['required','boolean'],
            'bus_id'=>['required','exists:buses,id'],
            'students_ids'=>['required','min:1','array'],
            'students_ids.*'=>['required','exists:students,id']
        ]);
        $students=Student::with('bus')
        ->whereIn('id',$data['students_ids'])
        ->select(['id','first_name','last_name','transportation_subscriber'])
        ->get();
        $bus=Bus::find($data['bus_id']);
        $alreadyInBus=collect();
        foreach($students as $student){
            if(!$student->transportation_subscriber)
            res::error(
                "Student $student->full_name isn't a transportation subscriber!",
                code:422
            );
            if(count($student->bus)>0 && !$data['allow_moving']){
                $alreadyInBus[]=$student;
            }
        }
        if($alreadyInBus->count()>0){
            res::error(
                "There are some students who are already in a bus. "
                ."please press allow moving to continue.",
                data:$alreadyInBus,
                code:409
            );
        }
        foreach($students as $student){
            $newData=[
                'student_id'=>$student->id,
                'bus_id'=>$bus->id
            ];
            Helper::lazyQueryTry(
                fn()=> StudentBus::updateOrCreate(
                    ['student_id'=>$student->id],
                    $newData
                    )
                );
        }
        res::success();
    }
}
