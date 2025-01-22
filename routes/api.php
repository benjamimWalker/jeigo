<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::post('signup', [AuthController::class, 'register'])->name('signup');
    Route::post('signin', [AuthController::class, 'login'])
        ->name('signin')
        ->middleware('throttle:7,1');
});

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'entries/en'], function () {
    Route::get('', [WordController::class, 'index'])->name('entries.en.index');
});
