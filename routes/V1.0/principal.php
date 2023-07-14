<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum','role:'.config('roles.principal'))->group(function () {
 //Employees
     Route::post('addEmployee',[EmployeeController::class,'add']);    
     Route::post('editEmployee/{employee}',[EmployeeController::class,'edit']);    
     Route::post('assignRolesToEmployee/{employee}',[EmployeeController::class,'assignRoles']);    
     Route::post('removeRolesFromEmployee/{employee}',[EmployeeController::class,'removeRoles']);    

     Route::post('assignClassesToSupervisor/{sup}',[EmployeeController::class,'assignClassesToSupervisor']);    
     Route::post('assign_Class_Subject_ToTeacher/{teacher}',[EmployeeController::class,'assign_Class_Subject_ToTeacher']);    
     Route::get('possibleRolesForEmps',[RoleController::class,'rolesForEmployees']);
 //Students
    Route::post('addStudentOrCandidate/{is_direct}', [StudentController::class, 'add']);

});

