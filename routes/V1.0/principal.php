<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum','hasRoles:'.config('roles.principal'))->group(function () {
 //Employees
     Route::post('addEmployee',[EmployeeController::class,'add']);    
     Route::post('editEmployee/{employee}',[EmployeeController::class,'edit']);    
     Route::post('assignRolesToEmployee/{employee}',[RoleController::class,'assignRoles']);    
     Route::post('removeRolesFromEmployee/{employee}',[RoleController::class,'removeRoles']);    

     Route::post('assignClassesToSupervisor/{sup}',[EmployeeController::class,'assignClassesToSupervisor']);    
     Route::post('assign_Class_Subject_ToTeacher/{teacher}',[EmployeeController::class,'assign_Class_Subject_ToTeacher']);    
     Route::get('possibleRolesForEmps',[RoleController::class,'rolesForEmployees']);
     Route::get('employeesWithRole/{role}',[EmployeeController::class,'employeesWithRole']);
     Route::get('employeeSearch/{query}',[EmployeeController::class,'search']);
     Route::get('viewEmployee/{employee}',[EmployeeController::class,'viewEmployee']);







 //Students
    Route::post('addStudentOrCandidate/{is_direct}', [StudentController::class, 'add']);

});

