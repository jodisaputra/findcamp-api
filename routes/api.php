<?php

use App\Http\Controllers\Api\Auth\GoogleController;
use Illuminate\Support\Facades\Route;

// Google OAuth routes
Route::get('auth/google', [GoogleController::class, 'redirectToProvider']);
Route::get('auth/google/callback', [GoogleController::class, 'handleProviderCallback']);
Route::post('auth/google/token', [GoogleController::class, 'handleToken']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    Route::get('/user', function () {
        return request()->user();
    });
});
