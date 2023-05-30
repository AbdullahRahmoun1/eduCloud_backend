<?php

namespace App\Policies;

use App\Models\Account;


class TestPolicy
{
     //TODO: add teachers access to this table in future
    public function viewAny(Account $account): bool
    {
        $allowedRoles=[
            config('roles.admin'),
            config('roles.secretary'),
        ];
        return $account->hasAnyRole($allowedRoles);
    }
    /**
     * Determine whether the Account can view the model.
     */
    public function viewClassTests(Account $account, $class_id): bool
    {
        $owner=$account->owner;
        return $this->viewAny($account)||
        $account->hasRole(config('roles.student'))&&$owner->g_class_id==$class_id ||
        $account->hasRole(config('roles.supervisor'))&& in_array($class_id,$owner->g_classes_sup->pluck('id')->toArray());
    }

    // /**
    //  * Determine whether the Account can create models.
    //  */
    public function editTestsOfClass(Account $account,$class_id): bool
    {
        $owner=$account->owner;
        return $this->viewAny($account)||
        $account->hasRole(config('roles.supervisor'))
        && in_array($class_id,$owner->g_classes_sup->pluck('id')->toArray());
    }
    
}
