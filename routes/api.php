<?php

use App\Http\Controllers\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/login', [AuthenticationController::class, 'login']);

Route::middleware('jwt.auth')->group(function () {
    Route::post('/trigger-email-to-confirm', [AuthenticationController::class, 'triggerEmailToConfirm']);
    Route::post('/confirm-email', [AuthenticationController::class, 'confirmEmail']);
});
