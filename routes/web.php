<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\CountryController;

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
    return view('auth.login');
});

Auth::routes([
    'register' => false
]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('regions', RegionController::class);
Route::resource('countries', CountryController::class);
