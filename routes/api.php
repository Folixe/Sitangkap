<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NelayanApiController;

Route::get('/reference-data', [NelayanApiController::class, 'referenceData']);
Route::post('/nelayan/register', [NelayanApiController::class, 'register']);
Route::post('/nelayan/login', [NelayanApiController::class, 'login']);
Route::get('/nelayan/profile', [NelayanApiController::class, 'profile']);
Route::put('/nelayan/profile/update', [NelayanApiController::class, 'updateProfile']);
Route::get('/nelayan/catches', [NelayanApiController::class, 'catches']);
Route::post('/nelayan/catches/store', [NelayanApiController::class, 'storeCatch']);
