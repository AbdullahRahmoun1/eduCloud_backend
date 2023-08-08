<?php

use App\Http\Controllers\MoneyRequestController;
use App\Models\MoneyRequest;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum','hasRoles:'.config('roles.accountant'))->group(function () {
    Route::post('addBill/{student}', [MoneyRequestController::class,'add']);
    Route::post('editBill/{bill}', [MoneyRequestController::class,'edit']);
});