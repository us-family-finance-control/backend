<?php

use App\Http\Controllers\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthenticationController::class, 'register']);
