<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Technician\AuthController;
use App\Http\Controllers\Technician\JobController;
use App\Http\Controllers\Technician\DashboardController;
use App\Http\Controllers\Technician\WithdrawalController;
use App\Http\Controllers\Technician\ReportController;
use App\Http\Controllers\Technician\MaintenanceController;
use App\Http\Controllers\Common\JobController as CommonJobController;

// Auth
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:technician')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Dashboard
    Route::get('top-widgets', [DashboardController::class, 'topWidgets']);

    // Job
    Route::get('jobs', [JobController::class, 'index']);
    Route::post('jobs/pay/{job}', [CommonJobController::class, 'pay']);
    Route::post('jobs/tip/{job}', [CommonJobController::class, 'tip']);

    // Withdrawal
    Route::apiResource('withdrawals', WithdrawalController::class)->only(['index', 'store']);

    // Report
    Route::get('reports', [ReportController::class, 'index']);
    Route::get('reports/jobs', [ReportController::class, 'jobs']);

    // Maintenance
    Route::get('trucks', [MaintenanceController::class, 'trucks']);
    Route::get('machines', [MaintenanceController::class, 'machines']);
    Route::post('maintenances', [MaintenanceController::class, 'store']);
});
