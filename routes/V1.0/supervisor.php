<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TypeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum', 'hasRoles:'.config('roles.supervisor'))->group(function(){
    Route::get('studentSearch', [StudentController::class, 'search']);
    Route::get('getAllTypes', [TypeController::class, 'getAllTypes']);
    Route::post('addTestType', [TypeController::class, 'add']);
    Route::post('editTestType/{type}', [TypeController::class, 'edit']);
    Route::post('addTest', [TestController::class, 'add']);
    Route::post('editTest/{test}', [TestController::class, 'edit']);
    Route::post('addTestMarks/{test}', [MarkController::class, 'addTestMarks']);
    Route::get('testMarks/{test}',[TestController::class,'getTestMarks']);
    Route::post('todaysAbsences',[AbsenceController::class,'addAbsences']);
    Route::post('justifyAbsence/{absence}',[AbsenceController::class,'justifyAbsence']);
    Route::post('editAbsenceJustification/{absence}',[AbsenceController::class,'editJustification']);
    Route::get('getRemainingStudentsForTest/{test}', [MarkController::class, 'getRemainingStudents']);
});