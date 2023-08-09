<?php

use App\Http\Controllers\IncomeController;
use App\Http\Controllers\MoneyRequestController;
use App\Models\MoneyRequest;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum','hasRoles:'.config('roles.accountant'))->group(function () {
    Route::post('addBill/{student}', [MoneyRequestController::class,'add']);
    Route::post('editBill/{bill}', [MoneyRequestController::class,'edit']);
    Route::post('addPayment/{student}', [IncomeController::class,'add']);
    Route::post('editPayment/{student}', [IncomeController::class,'edit']);
    
});
Route::get('getPaymentsOf/{student}', [IncomeController::class,'get']);
    Route::get('getStudentsFinanceInformation/{student}', [MoneyRequestController::class,'getStudentsFinanceInformation']);