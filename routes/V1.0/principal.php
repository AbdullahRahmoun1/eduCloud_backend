<?php

use App\Http\Controllers\AbilityTestController;
use App\Http\Controllers\AtMarkController;
use App\Http\Controllers\BaseCalendarController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\GClassController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Models\AtMark;
use App\Models\Notification;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum','hasRoles:'.config('roles.principal'))->group(function () {
 //Employees
    Route::post('addEmployee',[EmployeeController::class,'add']);    
    Route::post('editEmployee/{employee}',[EmployeeController::class,'edit']);    
    Route::post('assignRolesToEmployee/{employee}',[RoleController::class,'assignRoles']);    
    Route::post('removeRolesFromEmployee/{employee}',[RoleController::class,'removeRoles']);    

    Route::post('assignClassesToSupervisor/{sup}',[EmployeeController::class,'assignClassesToSupervisor']);    
    Route::post('assign_Class_Subject_ToTeacher/{teacher}',[EmployeeController::class,'assign_Class_Subject_ToTeacher']);    
    Route::post('addGrade',[GradeController::class,'add']);    
    Route::post('editGrade/{grade}',[GradeController::class,'edit']);    
    Route::post('addClassesToGrade/{grade}',[GClassController::class,'addClassesToGrade']);    
    Route::post('editClass/{class}',[GClassController::class,'edit']);    
    Route::post('addSubjectsToGrade/{grade}',[SubjectController::class,'addSubjectsToGrade']);    
    Route::post('editSubject/{subject}',[SubjectController::class,'edit']);
    
   Route::post('addBaseCalendar', [BaseCalendarController::class, 'add']);
   Route::post('editBaseCalendar/{calendar_id}', [BaseCalendarController::class, 'edit']);
   

    Route::get('viewSubject/{subject}',[SubjectController::class,'view']);
    Route::get('viewGrade/{grade}',[GradeController::class,'view']);    
    Route::get('possibleRolesForEmps',[RoleController::class,'rolesForEmployees']);
    Route::get('employeesWithRole/{role}',[EmployeeController::class,'employeesWithRole']);
    Route::get('employeeSearch/{query}',[EmployeeController::class,'search']);
    Route::get('regenerateEmployeePassword/{emp}', [EmployeeController::class, 'regeneratePassword']);

    Route::post('addCategory', [CategoryController::class, 'add']);
    Route::post('editCategory/{id}', [CategoryController::class, 'edit']);
    Route::post('sendGlobalNotification', [NotificationController::class, 'global']);
    Route::post('approveNotifications', [NotificationController::class, 'approveNotifications']);
});

Route::get('viewEmployee/{employee}',[EmployeeController::class,'viewEmployee'])
->middleware('auth:sanctum');

