<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\GoogleController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RequirementUploadController;
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
Route::get('/countries/{country}/requirements', [CountryController::class, 'getRequirements']);

// Protected routes
Route::middleware('auth:api')->group(function () {

    Route::post('/user/profile', [ProfileController::class, 'update']);

    Route::get('/user', function () {
        return request()->user();
    });

    Route::post('requirement-uploads', [RequirementUploadController::class, 'store']);
    Route::get('requirement-uploads/{country_id}/{requirement_id}', [RequirementUploadController::class, 'show']);
    Route::post('requirement-uploads/{id}/validate', [RequirementUploadController::class, 'validateUpload']);
});

Route::get('requirement-uploads/file/{id}', [RequirementUploadController::class, 'file']);

// In routes/api.php
Route::get('/test', function() {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working!'
    ]);
});

Route::apiResource('requirements', \App\Http\Controllers\Api\RequirementController::class);
Route::post('requirements/{requirement}/attach-country', [\App\Http\Controllers\Api\RequirementController::class, 'attachToCountry']);
Route::post('requirements/{requirement}/detach-country', [\App\Http\Controllers\Api\RequirementController::class, 'detachFromCountry']);
