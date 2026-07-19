<?php

use App\Http\Controllers\Backend\FileController;
use App\Http\Controllers\Frontend\AgendaController;
use App\Http\Controllers\Frontend\GalleryController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\NewsController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\Frontend\UmkmController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API Routes
Route::middleware(['throttle:api'])->prefix('v1')->group(function () {
    // Statistics & Data
    Route::get('/statistics', [HomeController::class, 'getStatistics']);
    Route::get('/village-profile', [HomeController::class, 'getVillageProfile']);
    Route::get('/population-stats', [HomeController::class, 'getPopulationStats']);

    // News & Announcements
    Route::get('/news', [NewsController::class, 'getRecentNews']);
    Route::get('/news/{slug}', [NewsController::class, 'getNewsDetail']);
    Route::get('/news/category/{category}', [NewsController::class, 'getNewsByCategory']);
    Route::get('/announcements', [NewsController::class, 'getAnnouncements']);
    Route::get('/search/news', [NewsController::class, 'search']);

    // UMKM
    Route::get('/umkm', [UmkmController::class, 'getUmkm']);
    Route::get('/umkm/{slug}', [UmkmController::class, 'getUmkmDetail']);
    Route::get('/umkm/category/{category}', [UmkmController::class, 'getUmkmByCategory']);
    Route::get('/search/umkm', [UmkmController::class, 'search']);

    // Gallery
    Route::get('/gallery', [GalleryController::class, 'getRecentGallery']);
    Route::get('/gallery/{id}', [GalleryController::class, 'getGalleryDetail']);
    Route::get('/gallery/category/{category}', [GalleryController::class, 'getGalleryByCategory']);

    // Agenda
    Route::get('/agenda', [AgendaController::class, 'getUpcomingAgenda']);
    Route::get('/agenda/{id}', [AgendaController::class, 'getAgendaDetail']);
    Route::get('/agenda/calendar/{year}/{month}', [AgendaController::class, 'getCalendar']);

    // Budget (Public)
    Route::get('/budget/summary', [HomeController::class, 'getBudgetSummary']);
});

// Protected API Routes (require authentication)
Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('v1')->group(function () {
    // User specific data
    Route::get('/user/profile', function (Request $request) {
        return $request->user();
    });

    // File uploads - stricter limit for uploads
    Route::middleware(['throttle:uploads'])->group(function () {
        Route::post('/upload/image', [FileController::class, 'uploadImage']);
        Route::post('/upload/document', [FileController::class, 'uploadDocument']);

        // Letter requests (often involve uploads or are sensitive)
        Route::post('/letter-requests', [ServiceController::class, 'submitLetterRequest']);
    });

    Route::get('/my-letter-requests', function (Request $request) {
        return $request->user()->letterRequests()->latest()->paginate(10);
    });

    // Contact submissions
    Route::post('/contact', [ProfileController::class, 'submitContact']);
});
