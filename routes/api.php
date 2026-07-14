<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\NoteImageController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});

Route::middleware('auth:sanctum')->group(function(){
    Route::apiResource('folders', FolderController::class)
    ->except(['show']);
    Route::get('folders/{folder}', [FolderController::class, 'show']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('notes', NoteController::class);
    Route::get('images', [NoteImageController::class, 'index']);

    Route::post('notes/{note}/images', [NoteImageController::class, 'store']);
    Route::get('notes/{note}/images/{image}', [NoteImageController::class, 'show'])
        ->name('notes.images.show');
    Route::delete('notes/{note}/images/{image}', [NoteImageController::class, 'destroy']);
});