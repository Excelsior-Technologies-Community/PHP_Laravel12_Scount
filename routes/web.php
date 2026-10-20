<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchAnalyticsController;
use App\Http\Controllers\ScoutStudioController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home Page (Search + Filter + Pagination + Typo Tolerance)
Route::get('/', [PostController::class, 'index'])->name('posts.index');

// Feature 1: Instant Live Search & Typeahead Autocomplete (AJAX)
Route::get('/posts/live-search', [PostController::class, 'liveSearch'])->name('posts.live.search');

// Feature 3: Global Federated Search (Ctrl+K Modal AJAX)
Route::get('/search/federated', [PostController::class, 'federatedSearch'])->name('search.federated');

// Create & Store Post
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

// Export Posts to CSV
Route::get('/posts/export', [PostController::class, 'export'])->name('posts.export');

// Search Analytics Dashboard
Route::get('/search-analytics', [SearchAnalyticsController::class, 'index'])->name('search.analytics');
Route::get('/search-analytics/export', [SearchAnalyticsController::class, 'export'])->name('search.analytics.export');
Route::delete('/search-analytics/clear', [SearchAnalyticsController::class, 'clear'])->name('search.analytics.clear');

// Feature 6: Scout Index Management & Sync Studio
Route::prefix('scout')->name('scout.')->group(function () {
    Route::get('/studio', [ScoutStudioController::class, 'index'])->name('studio');
    Route::post('/import', [ScoutStudioController::class, 'import'])->name('import');
    Route::post('/flush', [ScoutStudioController::class, 'flush'])->name('flush');
    Route::post('/sync-all', [ScoutStudioController::class, 'syncAll'])->name('sync.all');
});
