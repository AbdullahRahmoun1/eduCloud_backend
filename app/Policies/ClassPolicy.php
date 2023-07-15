<?php

namespace App\Policies;

use App\Models\Account;

class ClassPolicy
{
    
    public function viewClassInfo(Account $account, $class_id): bool{
        $owner=$account->owner;
        return $owner->hasRole(config('roles.student'))
        &&$owner->g_class_id==$class_id 
        ||
        $owner->hasRole(config('roles.supervisor'))
        && in_array($class_id,$owner->g_classes_sup->pluck('id')->toArray());
    }

    // /**
    //  * Determine whether the Account can create models.
    //  */
    public function editClassInfo(Account $account,$class_id): bool{
        $owner=$account->owner;
        return $owner->hasRole(config('roles.supervisor'))
        && in_array($class_id
        ,$owner->g_classes_sup->pluck('id')->toArray());
    }
}
