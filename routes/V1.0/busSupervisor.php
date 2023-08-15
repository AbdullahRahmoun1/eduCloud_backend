<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusController;
use App\Http\Controllers\BusLeavingTripController;
use App\Http\Controllers\BusReturningTripController;

Route::middleware('auth:sanctum','hasRoles:'.config('roles.busSupervisor'))
->group(function(){
    //Leaving trip
    Route::group([
        'prefix'=>'leavingTrip'
    ],function () {
        Route::post(
            'start/{bus}',
            [BusLeavingTripController::class,'startTrip']
        );
        Route::post(
            'busWillArriveSoon/{student}',
            [BusLeavingTripController::class,'busWillArriveSoon']
        );
        Route::post(
            'busWillSkipStudent/{student}',
            [BusLeavingTripController::class,'busWillSkipStudent']
        );
        Route::post(
            'busBrokeDown/{bus}',
            [BusLeavingTripController::class,'busBrokeDown']
        );
        Route::post(
            'studentBoardedTheBus/{student}',
            [BusLeavingTripController::class,'StudentBoardedTheBus']
        );
        Route::post(
            'end/{bus}',
            [BusLeavingTripController::class,'endTrip']
        );    
    });
    
    //Returning trip
    Route::group([
        'prefix'=>'returningTrip'
    ],function () {
        Route::post(
            'start/{bus}',
            [BusReturningTripController::class,'startTrip']
        );
        Route::post(
            'busWillArriveSoon/{student}',
            [BusReturningTripController::class,'busWillArriveSoon']
        );
        Route::post(
            'busBrokeDown/{bus}',
            [BusReturningTripController::class,'busBrokeDown']
        );
        Route::post(
            'studentLeftTheBus/{student}',
            [BusReturningTripController::class,'studentLeftTheBus']
        );
        Route::post(
            'end/{bus}',
            [BusReturningTripController::class,'endTrip']
        );
    
    });
    
    
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
