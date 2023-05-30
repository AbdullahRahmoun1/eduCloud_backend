<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Number;
use App\Models\Test;
use App\Models\Type;
use App\Policies\NumberPolicy;
use App\Policies\TestPolicy;
use App\Policies\TypePolicy;
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
        Test::class=>TestPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        // Gate::before(fn($user)=>$user->hasRole('admin'));

    }
}
