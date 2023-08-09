<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\MoneyRequestController;
//accessed by student + accountant 
Route::middleware([
    'auth:sanctum','hasRoles:'.config('roles.student').','.config('roles.accountant')
])->group(function () {
    Route::get('getPaymentsOf/{student}', [IncomeController::class,'get']);
    Route::get('getStudentsFinanceInformation/{student}', [MoneyRequestController::class,'getStudentsFinanceInformation']);
});
