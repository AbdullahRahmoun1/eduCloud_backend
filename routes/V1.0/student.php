<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\MoneyRequestController;
use App\Http\Controllers\NotificationController;

//accessed by student + accountant 
Route::middleware([
    'auth:sanctum','hasRoles:'.config('roles.student').','.config('roles.accountant')
])->group(function () {
    Route::get('getPaymentsOf/{student}', [IncomeController::class,'get']);
    Route::get('getStudentsFinanceInformation/{student}', [MoneyRequestController::class,'getStudentsFinanceInformation']);
});

//student & supervisor & secretary & principal
Route::middleware(['auth:sanctum', 'hasRoles:student,supervisor,secretary'])
->group(function () {

    Route::get('getNotificationsOfStudent/{student}', [NotificationController::class, 'getNotificationsOfStudent']);
});

//supervisor & secretary & principal
Route::middleware(['auth:sanctum', 'hasRoles:supervisor,secretary'])
->group(function () {
    
    Route::post('sendNotificationsToStudents/{notify}', [NotificationController::class, 'sendNotificationsToStudents']);
});