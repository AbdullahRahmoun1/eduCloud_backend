<?php

use App\Models\Test;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\GClassController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AbilityTestController;
use App\Http\Controllers\BaseCalendarController;
use App\Http\Controllers\ProgressCalendarController;

Route::middleware('auth:sanctum', 'hasRoles:supervisor,secretary')->group(function(){
    Route::get('studentSearch', [StudentController::class, 'search']);
    
    Route::post('addTestType', [TypeController::class, 'add']);
    Route::post('editTestType/{type}', [TypeController::class, 'edit']);
    Route::post('addTest', [TestController::class, 'add']);
    Route::post('editTest/{test}', [TestController::class, 'edit']);
    Route::get('getTest/{test_id}', [TestController::class, 'getTest']);
    
    Route::get('getGClass/{g_class}', [GClassController::class, 'getGClass']);
    
    Route::post('addTestMarks/{test}', [MarkController::class, 'addTestMarks']);
    Route::post('editMark/{mark_id}', [MarkController::class, 'editMark']);
    Route::get('testMarks/{test}',[TestController::class,'getTestMarks']);
    Route::get('searchTests', [TestController::class, 'searchTests']);
    Route::get('getRemainingStudentsForTest/{test}', [MarkController::class, 'getRemainingStudents']);

    Route::post('addAchievement', [ProgressCalendarController::class, 'addAchievement']);
    Route::get('getCalendarOfSubject/{subject_id}', [BaseCalendarController::class, 'getCalendarOfSubject']);
    Route::get('getProgressOfClass/{class_id}', [ProgressCalendarController::class, 'getProgressOfClass']);

    Route::post('todaysAbsences',[AbsenceController::class,'addAbsences']);
    Route::post('justifyAbsence/{absence}',[AbsenceController::class,'justifyAbsence']);
    Route::post('editAbsenceJustification/{absence}',[AbsenceController::class,'editJustification']);

    Route::post('addAbilityTestForm/{subject}',[AbilityTestController::class,'add']);
    Route::get('viewAbilityTest/{abilityTest}',[AbilityTestController::class,'get']);
    Route::get('getAbilityTestsOf/{subject}',[AbilityTestController::class,'viewSubjectsAbilityTests']);
    Route::post('addAbilityTestMark',[AtMarkController::class,'add']);
    

    Route::get('getUnsentNotifications', [NotificationController::class, 'getUnsentNotifications']);
});