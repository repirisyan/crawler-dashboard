<?php

use App\Http\Controllers\ComodityController;
use App\Http\Controllers\CrawlerController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchListController;
use App\Http\Controllers\SupervisionController;
use App\Http\Controllers\SupervisionListController;
use App\Http\Controllers\TempItemController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::redirect('/', 'login');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notification', [NotificationController::class, 'userNotification'])->name('notification.user');
    Route::patch('/notification/{notification_id}', [NotificationController::class, 'readNotification'])->name('notification.read');
    Route::delete('/notification/delete-all', [NotificationController::class, 'deleteAllNotification'])->name('notification.delete_all');
    Route::delete('/notification/{notification_id}', [NotificationController::class, 'deleteNotification'])->name('notification.delete');

    Route::get('crawler', [CrawlerController::class, 'index'])->name('crawler.index');
    Route::post('crawler', [CrawlerController::class, 'crawlerData'])->name('crawler.run');
    Route::get('crawler/{id}/status', [CrawlerController::class, 'getStatus'])->name('crawler.status');

    Route::prefix('supervision')->group(function () {
        Route::get('/', [SupervisionController::class, 'index'])->name('supervision.index');
        Route::get('data', [SupervisionController::class, 'data'])->name('supervision.data');
        Route::post('/', [SupervisionController::class, 'store'])->name('supervision.store');
        Route::patch('solved', [SupervisionController::class, 'solved'])->name('supervision.solved');
        Route::patch('check-link/{id}', [SupervisionController::class, 'checkLink'])->name('supervision.check_link');
        Route::delete('/', [SupervisionController::class, 'destroy'])->name('supervision.destroy');
    });

    Route::prefix('marketplace')->group(function () {
        Route::get('/', [MarketplaceController::class, 'index'])->name('marketplace.index');
        Route::patch('maintenance/{id}', [MarketplaceController::class, 'maintenance'])->name('marketplace.maintenance');
    });

    Route::resource('comodity', ComodityController::class)->except(['create', 'show']);

    Route::resource('keyword', KeywordController::class)->except(['create', 'show']);

    Route::resource('search-list', SearchListController::class)->except(['create', 'show']);

    Route::resource('supervision-list', SupervisionListController::class)->except(['create', 'show']);

    Route::get('temp-item', [TempItemController::class, 'tempItemData'])->name('temp-item.data');
    Route::delete('temp-item/delete-item', [TempItemController::class, 'deleteItem'])->name('temp-item.delete');
});

require __DIR__.'/auth.php';
