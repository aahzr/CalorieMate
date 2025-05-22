<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FoodLogController;
use App\Http\Controllers\WeightTrackingController;
use App\Http\Controllers\DietJournalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Rute autentikasi
Auth::routes(['verify' => false]);

// Rute publik
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Rute yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/food-log', [FoodLogController::class, 'index'])->name('food-log');
    Route::post('/food-log', [FoodLogController::class, 'store'])->name('food-log.store');
    Route::get('/weight-tracking', [WeightTrackingController::class, 'index'])->name('weight-tracking');
    Route::post('/weight-tracking', [WeightTrackingController::class, 'store'])->name('weight-tracking.store');
    Route::get('/diet-journal', [DietJournalController::class, 'index'])->name('diet-journal');
    Route::post('/diet-journal', [DietJournalController::class, 'store'])->name('diet-journal.store');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
});