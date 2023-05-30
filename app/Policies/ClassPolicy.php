<?php

namespace App\Policies;

use App\Models\Account;

class ClassPolicy
{
    
    public function canViewClassInfo(Account $account, $class_id): bool
    {
        $owner=$account->owner;
        return $account->hasRole(config('roles.student'))
        &&$owner->g_class_id==$class_id 
        ||
        $account->hasRole(config('roles.supervisor'))
        && in_array($class_id,$owner->g_classes_sup->pluck('id')->toArray());
    }

    // /**
    //  * Determine whether the Account can create models.
    //  */
    public function canEditClassInfo(Account $account,$class_id): bool
    {
        $owner=$account->owner;
        return $account->hasRole(config('roles.supervisor'))
        && in_array($class_id
        ,$owner->g_classes_sup->pluck('id')->toArray());
    }
}
