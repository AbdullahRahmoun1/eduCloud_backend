<?php

use App\Http\Controllers\BusController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;
Route::middleware(
    'auth:sanctum',
    'hasRoles:'.config('roles.busSupervisor').','
    .config('roles.busAdmin')
    )->group(function () {
        Route::get('busesSupervisedBy/{sup_id}',
        [BusController::class,'getBusesSupervisedBy']);
    });