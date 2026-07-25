<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\CmmsController;
use App\Http\Controllers\Api\Admin\DispatchController;
use App\Http\Controllers\Api\Admin\TechnicianController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FakeCmmsController;
use App\Http\Controllers\Api\InspectionFormController;
use App\Http\Controllers\Api\InspectionRecordController;
use App\Http\Controllers\Api\Mobile\MobileJobController;
use App\Http\Controllers\Api\Mobile\MobileProfileController;
use App\Http\Controllers\Api\WorkOrderController;
use Illuminate\Support\Facades\Route;

// Auth (public)
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

// Fake CMMS (demo-only external simulator; intentionally public in local demo)
Route::prefix('fake-cmms')->group(function () {
    Route::get('health', [FakeCmmsController::class, 'health']);
    Route::get('work-orders', [FakeCmmsController::class, 'workOrders']);
    Route::get('work-orders/{externalId}', [FakeCmmsController::class, 'workOrder']);
    Route::post('work-orders/{externalId}/status', [FakeCmmsController::class, 'updateStatus']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // Dashboard
    Route::get('dashboard/kpis', [DashboardController::class, 'kpis']);

    // Assets
    Route::get('assets/by-barcode/{barcode}', [AssetController::class, 'findByBarcode']);
    Route::apiResource('assets', AssetController::class);

    // Work Orders
    Route::get('work-orders/{workOrder}/timeline', [WorkOrderController::class, 'timeline']);
    Route::patch('work-orders/{workOrder}/status', [WorkOrderController::class, 'updateStatus']);
    Route::apiResource('work-orders', WorkOrderController::class);

    // Inspection Forms
    Route::apiResource('inspection-forms', InspectionFormController::class);

    // Inspection Records
    Route::post('inspection-records/{inspectionRecord}/evidence', [InspectionRecordController::class, 'uploadEvidence']);
    Route::apiResource('inspection-records', InspectionRecordController::class)->only(['index', 'store', 'show']);

    // Mobile technician API
    Route::prefix('mobile')->group(function () {
        Route::get('me', [MobileProfileController::class, 'me']);
        Route::get('jobs', [MobileJobController::class, 'index']);
        Route::get('jobs/{assignment}', [MobileJobController::class, 'show']);
        Route::post('jobs/{assignment}/accept', [MobileJobController::class, 'accept']);
        Route::post('jobs/{assignment}/status', [MobileJobController::class, 'status']);
        Route::post('jobs/{assignment}/location', [MobileJobController::class, 'location']);
        Route::post('jobs/{assignment}/complete', [MobileJobController::class, 'complete']);
        Route::post('jobs/{assignment}/evidence', [MobileJobController::class, 'evidence']);
    });

    // Admin Dispatch
    Route::prefix('admin')->group(function () {
        Route::post('cmms/sync-now', [CmmsController::class, 'syncNow']);
        Route::get('cmms/sync-logs', [CmmsController::class, 'syncLogs']);
        Route::get('cmms/health', [CmmsController::class, 'health']);
        Route::get('technicians', [TechnicianController::class, 'index']);
        Route::get('technicians/availability', [TechnicianController::class, 'availability']);
        Route::get('technicians/status', [TechnicianController::class, 'status']);
        Route::get('assignments', [DispatchController::class, 'assignments']);
        Route::get('work-orders/{workOrder}/recommendations', [DispatchController::class, 'recommendations']);
        Route::post('work-orders/{workOrder}/assign', [DispatchController::class, 'assign']);
        Route::post('assignments/{assignment}/cancel', [DispatchController::class, 'cancel']);
    });
});
