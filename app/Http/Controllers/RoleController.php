<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;


class RoleController extends Controller
{
    public function rolesForEmployees() {
        return Role::where('name','!=',config('roles.student'))
        ->get()
        ->pluck('name');
    }
}
