<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\RequirementUploadController;
use App\Http\Controllers\CampusController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes([
    'register' => false
]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('regions', RegionController::class);
Route::resource('countries', CountryController::class);
Route::resource('requirements', \App\Http\Controllers\RequirementController::class)->except(['show']);
Route::get('requirements/manage-country', [\App\Http\Controllers\RequirementController::class, 'manageCountryRequirements'])->name('requirements.manage-country');
Route::post('requirements/update-country', [\App\Http\Controllers\RequirementController::class, 'updateCountryRequirements'])->name('requirements.update-country');
Route::resource('requirement-uploads', RequirementUploadController::class)->only(['index', 'show']);
Route::get('requirement-uploads/file/{id}', [RequirementUploadController::class, 'file'])->name('requirement-uploads.file');
Route::get('requirement-uploads/payment-file/{id}', [RequirementUploadController::class, 'paymentFile'])->name('requirement-uploads.payment-file');
Route::post('requirement-uploads/{id}/validate', [RequirementUploadController::class, 'validateUpload'])->name('requirement-uploads.validate');
Route::post('requirement-uploads/{id}/validate-payment', [RequirementUploadController::class, 'validatePayment'])->name('requirement-uploads.validate-payment');
Route::post('/requirement-uploads/{id}/upload-admin-document', [RequirementUploadController::class, 'uploadAdminDocument'])->name('requirement-uploads.upload-admin-document');
Route::resource('campuses', CampusController::class);
