<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/posts', [BlogController::class, 'index'])->name('post.index');
Route::get('/posts/{id}', [BlogController::class, 'show'])->name('post.show');
Route::get('/categories', [BlogController::class, 'getCategories'])->name('post.categories');
Route::get('/posts/{postId}/comments', [CommentController::class, 'index'])->name('post.comments.index');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/posts', [BlogController::class, 'store'])->name('post.store');
    Route::post('/posts/{postId}/comments', [CommentController::class, 'store'])->name('post.comments.store');
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
});
