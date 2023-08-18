<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\GClass;
use App\Models\Account;
use App\Models\Bus;
use App\Models\Employee;
use App\Models\Student;
use App\Models\Test;
use App\Policies\BusPolicy;
use App\Policies\ClassPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\StudentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        GClass::class=>ClassPolicy::class,
        Student::class=>StudentPolicy::class,
        Bus::class=>BusPolicy::class,
        Employee::class=>EmployeePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Gate::before(function(Account $account){
            if($account->owner->hasRole(config('roles.principal'))||
                $account->owner->hasRole(config('roles.admin')))
            return true;
        });
        

    }
}
