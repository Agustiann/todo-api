<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\NoteChecklistController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\NoteImageController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api_token');

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:api_token')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:api_token')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::apiResource('folders', FolderController::class);
    Route::apiResource('notes', NoteController::class);
    Route::apiResource('notes.images', NoteImageController::class)
        ->only(['store', 'show', 'destroy']);
    Route::apiResource('notes.checklists', NoteChecklistController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});