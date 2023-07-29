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
        return 
        $owner->hasRole(config('roles.student'))&&$owner->id==$student_id 
        ||
        $owner->hasRole(config('roles.supervisor'))
        && in_array($student->g_class_id,$owner->g_classes_sup->pluck('id')->toArray());
    }
}
