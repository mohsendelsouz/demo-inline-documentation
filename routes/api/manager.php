<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\AuthController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Manager\UserController;
use App\Http\Controllers\Manager\CompanyController;
use App\Http\Controllers\Manager\MachineController;
use App\Http\Controllers\Manager\TruckController;
use App\Http\Controllers\Manager\CharityController;
use App\Http\Controllers\Manager\JobController;
use App\Http\Controllers\Manager\WithdrawalController;
use App\Http\Controllers\Common\JobController as CommonJobController;
use App\Http\Controllers\Manager\TruckMaintenanceController;
use App\Http\Controllers\Manager\MachineMaintenanceController;
use App\Http\Controllers\Manager\ReportController;
use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\CashDepositController;
use App\Http\Controllers\Manager\CommissionController;
use App\Http\Controllers\Manager\ReferralController;
use App\Http\Controllers\Manager\SettingController;
use App\Http\Controllers\Manager\InspectionController;

// Auth
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:manager')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Manager
    Route::apiResource('managers', ManagerController::class);

    // User
    Route::apiResource('users', UserController::class);
    Route::get('technicians', [UserController::class, 'technicians']);
    Route::get('sales-persons', [UserController::class, 'salesPersons']);
    Route::get('operation-managers', [UserController::class, 'operationManagers']);
    Route::get('general-managers', [UserController::class, 'generalManagers']);

    // Company
    Route::apiResource('companies', CompanyController::class);

    // Machine
    Route::apiResource('machines', MachineController::class);

    // Truck
    Route::apiResource('trucks', TruckController::class);

    // Job
    Route::apiResource('jobs', JobController::class);
    Route::post('jobs/pay/{job}', [CommonJobController::class, 'pay']);
    Route::post('jobs/tip/{job}', [CommonJobController::class, 'tip']);
    Route::post('jobs/sc/{job}', [CommonJobController::class, 'sc']);
    Route::post('jobs/wow/{job}', [CommonJobController::class, 'wow']);
    Route::post('jobs/manager-received/{job}', [CommonJobController::class, 'managerReceived']);

    // Dashboard
    Route::get('top-widgets', [DashboardController::class, 'topWidgets']);
    Route::get('sales-chart', [DashboardController::class, 'salesChart']);
    Route::get('job-count-chart', [DashboardController::class, 'jobCountChart']);

    // Truck Maintenance
    Route::apiResource('trucks_maintenances', TruckMaintenanceController::class);

    // Machine Maintenance
    Route::apiResource('machines_maintenances', MachineMaintenanceController::class);

    // Report
    Route::get('reports', [ReportController::class, 'index']);
    Route::get('reports/trucks', [ReportController::class, 'trucks']);
    Route::get('reports/machines', [ReportController::class, 'machines']);
    Route::get('reports/companies', [ReportController::class, 'companies']);
    Route::get('reports/cash', [ReportController::class, 'cash']);
    Route::get('reports/charities', [ReportController::class, 'charities']);
    Route::get('reports/technician-wallet', [ReportController::class, 'technicianWallet']);
    Route::get('reports/technician-goal/{technician}', [ReportController::class, 'technicianGoalReport']);
    Route::get('reports/companies-goal', [ReportController::class, 'companiesGoal']);
    Route::get('reports/truck/details', [ReportController::class, 'truckDetails']);
    Route::get('reports/jobs', [ReportController::class, 'jobs']);

    // Withdrawal
    Route::get('withdrawals', [WithdrawalController::class, 'index']);
    Route::post('withdrawals', [WithdrawalController::class, 'store']);
    Route::post('withdrawals/{withdrawal}/reject', [WithdrawalController::class, 'reject']);
    Route::post('withdrawals/{withdrawal}/accept', [WithdrawalController::class, 'accept']);

    // Charity
    Route::apiResource('charities', CharityController::class);
    Route::post('donation', [CharityController::class, 'addDonation']);

    // Cash Deposit
    Route::apiResource('cash-deposits', CashDepositController::class);

    // Commissions
    Route::apiResource('commissions', CommissionController::class);

    // Referrals
    Route::get('referrals', [ReferralController::class, 'index']);
    Route::post('referrals/send/{referral}', [ReferralController::class, 'sendBonus']);

    // Settings
    Route::get('settings', [SettingController::class, 'index']);
    Route::post('settings/trucks', [SettingController::class, 'truckSettings']);

    // Inspection
    Route::post('inspection/trucks', [InspectionController::class, 'truckInspection']);
});
