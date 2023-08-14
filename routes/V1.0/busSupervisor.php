<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusController;
use App\Http\Controllers\BusLeavingTripController;
use App\Http\Controllers\BusReturningTripController;

Route::middleware('auth:sanctum','hasRoles:'.config('roles.busSupervisor'))
->group(function(){
    Route::post(
        'startLeavingTrip/{bus}',
        [BusLeavingTripController::class,'startTrip']
    );
    Route::post(
        'endLeavingTrip/{bus}',
        [BusLeavingTripController::class,'endTrip']
    );
    Route::post(
        'studentBoardedTheBus/{student}',
        [BusLeavingTripController::class,'StudentBoardedTheBus']
    );
    Route::post(
        'startReturningTrip/{bus}',
        [BusReturningTripController::class,'startTrip']
    );
    Route::post(
        'endReturningTrip/{bus}',
        [BusReturningTripController::class,'endTrip']
    );
    Route::post(
        'studentLeftTheBus/{student}',
        [BusReturningTripController::class,'studentLeftTheBus']
    );
});

Route::middleware(
    'auth:sanctum',
    'hasRoles:'.config('roles.busSupervisor').','
    .config('roles.busAdmin')
    )->group(function () {
        Route::get(
            'busesSupervisedBy/{sup_id}',
            [BusController::class,'getBusesSupervisedBy']
        );
        Route::get(
            'studentsInBus/{bus}',
            [BusController::class,'studentsInBus']
        );
    });
