<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TypeController;
use App\Models\Student;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function(){

    Route::get('getAllTypes', [TypeController::class, 'getAllTypes']);
    Route::get('getAllCategories', [CategoryController::class, 'getAll']);

    Route::get('getAllGrades', [GradeController::class, 'getAllGrades']);
    Route::get('getAllGradesWithClassesAndSubjects', [GradeController::class, 'getAllGradesWithClassesAndSubjects']);

    Route::post('message/{student_id}',[ChatController::class,'post']);
    Route::get('complaintChat/{student_id}',[ChatController::class,'getChat']);
    Route::get('getSupervisorsConversations',[ChatController::class,'getSupervisorsConversations']);
    Route::get('studentAbsences/{student_id}',[AbsenceController::class,'studentAbsences']);
    
    Route::get('getTypeOfTest/{test}', [TestController::class, 'getTypeOfTest']);
    Route::get('getNameOfType/{id}', [TypeController::class, 'getNameOfType']);
    
    Route::get('getMarksOfStudent/{student_id}', [MarkController::class, 'getMarksOfStudent']);
    Route::get('viewStudent/{student}', [StudentController::class, 'view']);

    
});