<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home Page (Search + Filter + Pagination + Statistics)
Route::get('/', [PostController::class, 'index'])->name('posts.index');

// Create Post
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');

// Store Post
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

// Export Posts to CSV
Route::get('/posts/export', [PostController::class, 'export'])->name('posts.export');
