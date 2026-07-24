<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\NoteChecklistController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\NoteImageController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth.api_token')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::get('/profile/photo', [AuthController::class, 'photo'])->name('auth.profile.photo');
    });
});

Route::middleware('auth.api_token')->group(function () {
    Route::apiResource('folders', FolderController::class);
    Route::apiResource('notes', NoteController::class);
    Route::apiResource('notes.images', NoteImageController::class)
        ->only(['index','store', 'show', 'destroy']);
    Route::apiResource('notes.checklists', NoteChecklistController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});