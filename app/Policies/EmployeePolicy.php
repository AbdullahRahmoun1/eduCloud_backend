<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\Employee;

class EmployeePolicy
{
    /**
     * Create a new policy instance.
     */
    public function view(Account $account,$employee_id) {
        $owner=request()->user()->owner;
        $result=false;
        $result|=$owner->hasRole(config('roles.principal'));
        $result|=$owner->hasRole(config('roles.admin'));
        $result|=$owner instanceof Employee && $owner->id=$employee_id;
    }
}
