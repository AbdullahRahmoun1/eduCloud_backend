<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Helpers\ResponseFormatter as res;
use App\Models\Employee;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;


class RoleController extends Controller
{
    public function rolesForEmployees() {
        return Role::where('name','!=',config('roles.student'))
        ->get()
        ->pluck('name');
    }
    public function assignRoles(Employee $employee) {
        $data=request()->validate([
            'roles'=>['min:1','required','array']
        ]);
        Helper::assignRoles($employee,$data['roles']);
        res::success();
    }
    public function removeRoles(Employee $employee) {
        $data=request()->validate([
            'roles'=>['min:1','required','array']
        ]);
        Helper::removeRoles($employee,$data['roles']);
        res::success();
        //TODO: apply results of revoking the role
    }
}
