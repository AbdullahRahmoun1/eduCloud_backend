<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter as res;
use App\Models\Bus;

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
        $students=$bus->students;
        res::success(data:$students);
    }
}
