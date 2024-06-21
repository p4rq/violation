<?php

use App\Http\Controllers\ViolationPlaceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/violations', [ViolationPlaceController::class, 'store']);
Route::get('/violations', [ViolationPlaceController::class, 'index']);
Route::put('/violations/{violation}', [ViolationPlaceController::class, 'update']);
Route::delete('/violations/{violation}', [ViolationPlaceController::class, 'destroy']);//Route::get('/violations', [App\Http\Controllers\ViolationPlaceController::class, 'index'])->name('violations.index');
