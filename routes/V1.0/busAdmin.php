<?php

use App\Http\Controllers\BusController;
use Illuminate\Support\Facades\Route;
Route::middleware('auth:sanctum','hasRoles:'.config('roles.busAdmin'))
->group(function(){
    Route::get('getBuses',[BusController::class,'getBuses']);
    Route::get('getTransportationSubscribers',[BusController::class,'getTransportationSubscribers']);
    Route::get('getBusInformation/{bus}',[BusController::class,'get']);
    Route::post('setBusStudents',[BusController::class,'setBusStudents']);
});
