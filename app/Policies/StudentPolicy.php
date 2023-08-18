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
        $count=0;
        //new method of authorizing..
        //we will say the access is granted
        //now there are some roles that are restricted to view students 
        //but others not.. so we check only those 
        if($owner->hasRole(config('roles.student'))){
            $count+=$owner->id!=$student_id;
        }
        if($owner->hasRole(config('roles.supervisor'))){
            $count+=!in_array(
                $student->g_class_id,
                $owner->g_classes_sup->pluck('id')->toArray()
            );
        }
        return $count<2;
    }
}
