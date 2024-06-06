<?php

use App\Http\Controllers\CrawlerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TempItemController;
use Illuminate\Foundation\Application;
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

Route::redirect('/','login');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('crawler',[CrawlerController::class,'index'])->name('crawler.index');
    Route::post('crawler',[CrawlerController::class,'crawlerData'])->name('crawler.run');
    Route::get('crawler/{id}/status',[CrawlerController::class,'getStatus'])->name('crawler.status');

    Route::get('temp-item',[TempItemController::class,'tempItemData'])->name('temp-item.data');
    Route::delete('temp-item/truncate',[TempItemController::class,'truncateData'])->name('temp-item.truncate');
});

require __DIR__.'/auth.php';
