<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\GoogleController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']);

// Google OAuth routes
Route::get('auth/google', [GoogleController::class, 'redirectToProvider']);
Route::get('auth/google/callback', [GoogleController::class, 'handleProviderCallback']);
Route::post('auth/google/token', [GoogleController::class, 'handleToken']);

Route::resource('regions', RegionController::class);
Route::get('/regions/{region}/countries', [RegionController::class, 'getCountries']);

Route::resource('countries', CountryController::class);

// Protected routes
Route::middleware('auth:api')->group(function () {

    Route::post('/user/profile', [ProfileController::class, 'update']);

    Route::get('/user', function () {
        return request()->user();
    });
});

// In routes/api.php
Route::get('/test', function() {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working!'
    ]);
});
