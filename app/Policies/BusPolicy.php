<?php

namespace App\Policies;

use App\Models\Account;

class BusPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewBus(Account $account,$bus_id) {
        $owner=$account->owner;
        $result=false;
        $result|=$owner->hasRole(config('roles.admin'));
        $result|=$owner->hasRole(config('roles.principal'));
        $result|=$owner->hasRole(config('roles.busAdmin'));
        $result|=
        $owner->hasRole(config('roles.busSupervisor')) 
        &&
        in_array($bus_id,$owner->buses->pluck('id'));
        return $result;
    }
    public function editBus(Account $account,$bus_id) {
        $owner=$account->owner;
        $result=false;
        $result|=$owner->hasRole(config('roles.admin'));
        $result|=$owner->hasRole(config('roles.principal'));
        $result|=$owner->hasRole(config('roles.busAdmin'));
        return $result;
    }
    public function controlBusTrips(Account $account,$bus_id){
        $owner=request()->user()->owner;
        return $owner->hasRole(config('roles.busSupervisor')) 
        &&
        in_array($bus_id,$owner->buses->pluck('id'));
        
    }
}
