<?php

namespace App\Policies;

use App\Models\Test;
use App\Models\Account;
use Illuminate\Auth\Access\Response;

class TestPolicy
{
    /**
     * Determine whether the Account can view any models.
     */
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
        $account->hasRole(config('roles.student'))&&$owner->calss_id==$class_id ||
        $account->hasRole(config('roles.supervisor'))&& in_array($class_id,$owner->g_classes->pluck('id'));
    }

    /**
     * Determine whether the Account can create models.
     */
    public function create(Account $account,$class_id): bool
    {
        $owner=$account->owner;
        $account->hasRole(config('roles.supervisor'))
        && in_array($class_id,$owner->g_classes->pluck('id'));
    }

    /**
     * Determine whether the Account can update the model.
     */
    public function update(Account $account, Test $test): bool
    {
        //
    }

    /**
     * Determine whether the Account can delete the model.
     */
    public function delete(Account $account, Test $test): bool
    {
        //
    }

    /**
     * Determine whether the Account can restore the model.
     */
    public function restore(Account $account, Test $test): bool
    {
        //
    }

    /**
     * Determine whether the Account can permanently delete the model.
     */
    public function forceDelete(Account $account, Test $test): bool
    {
        //
    }
}
