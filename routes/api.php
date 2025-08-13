<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TranslationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Translation statistics and metadata (specific routes first)
    Route::get('/translations/stats', [TranslationController::class, 'stats'])->name('translations.stats');
    Route::get('/translations/locales', [TranslationController::class, 'locales'])->name('translations.locales');

    // Translation search and export (specific routes first)
    Route::get('/translations/search', [TranslationController::class, 'search'])->name('translations.search');
    Route::get('/translations/export/{locale}', [TranslationController::class, 'export'])->name('translations.export');

    // Translation CRUD operations (parameterized routes last)
    Route::post('/translations', [TranslationController::class, 'store'])->name('translations.store');
    Route::put('/translations/{id}', [TranslationController::class, 'update'])->name('translations.update');
    Route::get('/translations/{id}', [TranslationController::class, 'show'])->name('translations.show');

    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('user.info');
});
