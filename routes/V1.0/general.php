<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\GradeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function(){
    Route::get('getAllGrades', [GradeController::class, 'getAllGrades']);
    Route::get('getAllGradesWithClassesAndSubjects', [GradeController::class, 'getAllGradesWithClassesAndSubjects']);
    Route::post('message/{student}',[ChatController::class,'post']);
});