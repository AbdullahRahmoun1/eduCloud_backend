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

    Route::get('candidateToOfficial/view/{grade}', [CandidateStudentController::class, 'all']);
    Route::post('candidateToOfficial/perform/{grade}', [CandidateStudentController::class, 'candidatesToOfficials']);
    Route::post('automaticStudentsDistribution/{algorithm}', [GClassController::class, 'automaticStudentDistribution']);
    Route::post('addOrMoveStudentsToClasses/{grade}',[GClassController::class,'addOrMoveStudentsToClasses']);
});