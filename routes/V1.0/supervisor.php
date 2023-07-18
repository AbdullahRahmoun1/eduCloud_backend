<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum', 'hasRoles:'.config('roles.supervisor'))->group(function(){
    Route::get('studentSearch', [StudentController::class, 'search']);
});