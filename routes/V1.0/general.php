<?php

use App\Http\Controllers\GClassController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function(){
    Route::get('getAllGrades', [GradeController::class, 'getAllGrades']);
    Route::get('getAllClassesOfGrade/{grade}', [GClassController::class, 'getAllClasses']);
});