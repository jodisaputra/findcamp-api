<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\GoogleController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RequirementUploadController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\CampusController;
use App\Http\Controllers\Api\TaskController;

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']);

// Google OAuth routes
Route::get('auth/google', [GoogleController::class, 'redirectToProvider']);
Route::get('auth/google/callback', [GoogleController::class, 'handleProviderCallback']);
Route::post('auth/google/token', [GoogleController::class, 'handleToken']);

Route::apiResource('regions', RegionController::class, ['as' => 'api']);
Route::get('/regions/{region}/countries', [RegionController::class, 'getCountries']);

Route::apiResource('countries', CountryController::class, ['as' => 'api']);
Route::get('/countries/{country}/requirements', [CountryController::class, 'getRequirements']);

// Protected routes
Route::middleware('auth:api')->group(function () {

    Route::post('/user/profile', [ProfileController::class, 'update']);

    Route::get('/user', function () {
        return request()->user();
    });

    // File download routes - moved before the show route
    Route::get('requirement-uploads/{id}/file', [RequirementUploadController::class, 'downloadFile']);
    Route::get('requirement-uploads/{id}/payment-file', [RequirementUploadController::class, 'downloadPaymentFile']);

    Route::get('requirement-uploads', [RequirementUploadController::class, 'index']);
    Route::post('requirement-uploads', [RequirementUploadController::class, 'store']);
    Route::get('requirement-uploads/{country_id}/{requirement_id}', [RequirementUploadController::class, 'show']);
    Route::post('requirement-uploads/{id}/payment', [RequirementUploadController::class, 'uploadPayment']);
    
    // Admin routes
    Route::middleware('admin')->group(function () {
        Route::post('requirement-uploads/{id}/validate', [RequirementUploadController::class, 'validateUpload']);
        Route::post('requirement-uploads/{id}/validate-payment', [RequirementUploadController::class, 'validatePayment']);
    });
});

// In routes/api.php
Route::get('/test', function() {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working!'
    ]);
});

Route::apiResource('requirements', \App\Http\Controllers\Api\RequirementController::class, ['as' => 'api']);
Route::post('requirements/{requirement}/attach-country', [\App\Http\Controllers\Api\RequirementController::class, 'attachToCountry']);
Route::post('requirements/{requirement}/detach-country', [\App\Http\Controllers\Api\RequirementController::class, 'detachFromCountry']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Campus API Routes
Route::get('/campuses', [CampusController::class, 'index']);
Route::get('/campuses/{id}', [CampusController::class, 'show']);
Route::post('/campuses', [CampusController::class, 'store']);
Route::put('/campuses/{id}', [CampusController::class, 'update']);
Route::delete('/campuses/{id}', [CampusController::class, 'destroy']);
Route::get('/campuses/country/{country}', [CampusController::class, 'getByCountry']);
Route::get('/campuses/country-id/{id}', [CampusController::class, 'getByCountryId']);

// Task Routes
Route::middleware('auth:api')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::post('/tasks/{task}/payment', [TaskController::class, 'uploadPayment']);
    Route::get('/tasks/{id}/file', [TaskController::class, 'file']);
    Route::get('/tasks/{id}/payment-file', [TaskController::class, 'paymentFile']);
    
    // Admin routes
    Route::middleware('admin')->group(function () {
        Route::post('/tasks/{task}/validate', [TaskController::class, 'validateTask']);
        Route::post('/tasks/{task}/validate-payment', [TaskController::class, 'validatePayment']);
    });
});
