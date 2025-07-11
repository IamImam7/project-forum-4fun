<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
     Route::post('/posts', [PostController::class, 'store']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user/posts', [UserController::class, 'posts']);
      Route::put('/user/profile', [UserController::class, 'update']);
     Route::post('/posts/{post}/vote', [PostController::class, 'vote']);
     Route::post('/comments/{comment}/vote', [CommentController::class, 'vote']);
      Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
      Route::delete('/posts/{post}', [PostController::class, 'destroy']);
});

Route::get('/posts', [PostController::class, 'index']);
Route::get('/categories/{category:slug}/posts', [CategoryController::class, 'posts']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('/users/{user:name}/posts', [UserController::class, 'publicPosts']);
Route::get('/posts/{post}', [PostController::class, 'show']);
Route::get('/search', [PostController::class, 'search']);
Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
