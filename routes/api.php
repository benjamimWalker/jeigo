<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
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

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'user/me'], function () {
    Route::get('favorites', [UserController::class, 'favoriteWords'])->name('user.favorites');
    Route::get('history', [UserController::class, 'wordsHistory'])->name('user.history');
});

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'entries/en'], function () {
    Route::get('', [WordController::class, 'index'])->name('entries.en.index');
    Route::get('{word:word}', [WordController::class, 'show'])->name('entries.en.show');
    Route::post('{word:word}/favorite', [UserController::class, 'favoriteAWord'])->name('entries.en.favorite');
    Route::delete('{word:word}/unfavorite', [UserController::class, 'unFavoriteAWord'])->name('entries.en.unfavorite');
});
