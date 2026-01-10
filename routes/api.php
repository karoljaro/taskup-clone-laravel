<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ==========================[ PUBLIC AUTH ROUTES ] ==========================

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// ==========================[ PROTECTED ROUTES ] ==========================

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
