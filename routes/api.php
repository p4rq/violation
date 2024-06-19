<?php

use App\Http\Controllers\ViolationPlaceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('violations', ViolationPlaceController::class);
