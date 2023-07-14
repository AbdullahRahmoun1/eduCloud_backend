<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum','role:'.config('roles.principal'))->group(function () {
    Route::post('addEmployee',[EmployeeController::class,'add']);    
    Route::get('possibleRolesForEmps',[RoleController::class,'rolesForEmployees']);
    Route::post('addStudentOrCandidate/{is_direct}', [StudentController::class, 'add']);
});

