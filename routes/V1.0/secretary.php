<?php

use App\Http\Controllers\GClassController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum', 'role:'.config('roles.secretary'))->group(function(){
    Route::post('addStudentOrCandidate/{is_direct}', [StudentController::class, 'add']);
});