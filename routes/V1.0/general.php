<?php

use App\Http\Controllers\GradeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function(){
    Route::get('getAllGrades', [GradeController::class, 'getAllGrades']);
    Route::get('getAllClassesAndSubjectsOfGrade/{grade}', [GradeController::class, 'getAllClassesAndSubjects']);
});