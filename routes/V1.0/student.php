<?php

use Illuminate\Support\Facades\Route;

Route::middleware(
    [
        'auth:sanctum',
        'hasRoles:' . config('roles.student')
    ]
)->group(function () {
    
});
