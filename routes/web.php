<?php

use App\Http\Controllers\{
    DashboardController,
    VehicleController,
    ServiceController,
    RoadtaxController,
    FuelController,
    MovementController,
    SamanController,
    ApprovalController,
    ReminderController,
    ReportController,
    AnomalyController,
    UserController,
    SettingController,
    ProfileController,
};
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');

    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');

    Route::get('/roadtax', [RoadtaxController::class, 'index'])->name('roadtax.index');
    Route::post('/roadtax', [RoadtaxController::class, 'store'])->name('roadtax.store');

    Route::get('/fuel', [FuelController::class, 'index'])->name('fuel.index');
    Route::post('/fuel', [FuelController::class, 'store'])->name('fuel.store');

    Route::get('/movements', [MovementController::class, 'index'])->name('movements.index');
    Route::post('/movements', [MovementController::class, 'store'])->name('movements.store');
    Route::put('/movements/{movement}/checkin', [MovementController::class, 'checkin'])->name('movements.checkin');

    Route::get('/saman', [SamanController::class, 'index'])->name('saman.index');
    Route::post('/saman', [SamanController::class, 'store'])->name('saman.store');
    Route::put('/saman/{saman}', [SamanController::class, 'update'])->name('saman.update');

    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');

    Route::get('/reminders', [ReminderController::class, 'index'])->name('reminders.index');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::get('/anomalies', [AnomalyController::class, 'index'])->name('anomalies.index');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
