<?php

use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DischargeController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/patients', [PatientController::class, 'index'])
        ->name('patients.index');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])
        ->name('patients.show');

    Route::get('/admissions/create', [AdmissionController::class, 'create'])
        ->middleware('role:ADMIN,SENIOR,JUNIOR,INTERN,NURSE')
        ->name('admissions.create');
    Route::post('/admissions', [AdmissionController::class, 'store'])
        ->middleware('role:ADMIN,SENIOR,JUNIOR,INTERN,NURSE')
        ->name('admissions.store');

    Route::get('/hospitalizations/{hospitalization}/discharge', [DischargeController::class, 'edit'])
        ->middleware('role:ADMIN,SENIOR,JUNIOR')
        ->name('discharges.edit');
    Route::put('/hospitalizations/{hospitalization}/discharge', [DischargeController::class, 'update'])
        ->middleware('role:ADMIN,SENIOR,JUNIOR')
        ->name('discharges.update');

    Route::middleware('role:ADMIN')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::get('/audit', [AdminAuditLogController::class, 'index'])->name('audit.index');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
