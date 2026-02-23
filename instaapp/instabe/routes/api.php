<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TimelineController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Posts
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

    // Likes
    Route::post('/posts/{post}/like', [LikeController::class, 'store']);

    // Comments
    Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);

    // Timeline
    Route::get('/timeline', [TimelineController::class, 'index']);
});
