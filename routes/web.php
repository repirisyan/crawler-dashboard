<?php

use App\Http\Controllers\ComodityController;
use App\Http\Controllers\CrawlerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndexingController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchListController;
use App\Http\Controllers\SupervisionController;
use App\Http\Controllers\SupervisionListController;
use App\Http\Controllers\TrendingController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::redirect('/', 'login');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notification', [NotificationController::class, 'userNotification'])->name('notification.user');
    Route::patch('/notification/{notification_id}', [NotificationController::class, 'readNotification'])->name('notification.read');
    Route::delete('/notification/delete-all', [NotificationController::class, 'deleteAllNotification'])->name('notification.delete_all');
    Route::delete('/notification/{notification_id}', [NotificationController::class, 'deleteNotification'])->name('notification.delete');

    Route::get('crawler', [CrawlerController::class, 'index'])->name('crawler.index');
    Route::get('crawler/{id}/status', [CrawlerController::class, 'getStatus'])->name('crawler.status');

    Route::prefix('supervision')->group(function () {
        Route::get('/', [SupervisionController::class, 'index'])->name('supervision.index');
    });

    Route::prefix('indexing')->group(function () {
        Route::get('/', [IndexingController::class, 'index'])->name('indexing.index');
    });

    Route::prefix('trending')->group(function () {
        Route::get('/', [TrendingController::class, 'index'])->name('trending.index');
    });

    Route::prefix('marketplace')->group(function () {
        Route::get('/', [MarketplaceController::class, 'index'])->name('marketplace.index');
        Route::patch('maintenance/{id}', [MarketplaceController::class, 'maintenance'])->name('marketplace.maintenance');
    });

    Route::post('comodity/import', [ComodityController::class, 'import'])->name('comodity.import');
    Route::resource('comodity', ComodityController::class)->except(['create', 'show']);

    Route::resource('keyword', KeywordController::class)->except(['create', 'show']);

    Route::patch('search-list/change-status/{id}', [SearchListController::class, 'changeStatus'])->name('search-list.change_status');
    Route::get('search-list', [SearchListController::class, 'index'])->name('search-list.index');

    Route::post('supervision-list/import', [SupervisionListController::class, 'import'])->name('supervision-list.import');
    Route::patch('supervision-list/change-status/{id}', [SupervisionListController::class, 'changeStatus'])->name('supervision-list.change_status');
    Route::resource('supervision-list', SupervisionListController::class)->except(['create', 'show']);

});

require __DIR__.'/auth.php';
