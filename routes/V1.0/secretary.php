<?php

use App\Http\Controllers\CandidateStudentController;
use App\Http\Controllers\GClassController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum', 'hasRoles:'.config('roles.secretary'))->group(function(){
    Route::post('addStudentOrCandidate/{is_direct}', [StudentController::class, 'add']);
    Route::post('editStudentOrCandidate/{id}/{is_candidate}', [StudentController::class, 'edit']);
    Route::get('regeneratePassword/{student}', [StudentController::class, 'regeneratePassword']);
    Route::get('studentSearch', [StudentController::class, 'search']);
    Route::get('candidateStudents/{grade}', [CandidateStudentController::class, 'all']);

});