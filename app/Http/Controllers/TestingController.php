<?php

namespace App\Http\Controllers;

use App\Models\GClass;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;

class TestingController extends Controller
{
    public function test()
    {
        // $this->authorize('EditClassInfo',[GClass::class,3]);
        return Role::where('name','!=',config('roles.student'))
        ->get()
        ->pluck('name');
    }
}
