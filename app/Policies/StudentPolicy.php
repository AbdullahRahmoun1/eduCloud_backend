<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\Student;

class StudentPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewStudent(Account $account,$student_id): bool{
        $owner=$account->owner;
        $student=Student::findOrFail($student_id);  
        $result=true;
        //new method of authorizing..
        //we will say the access is granted
        //now there are some roles that are restricted to view students 
        //but others not.. so we check only those 
        if($owner->hasRole(config('roles.student'))){
            $result&=$owner->id==$student_id;
        }
        if($owner->hasRole(config('roles.busSupervisor'))){
            //TODO: complete this
        }
        if($owner->hasRole(config('roles.supervisor'))){
            $result&=in_array(
                $student->g_class_id,
                $owner->g_classes_sup->pluck('id')->toArray()
            );
        }
        
        return $result;
    }
}
