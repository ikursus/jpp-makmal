<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\LoanApplicationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // SEMENTARA: rate-limit login API dimatikan untuk ujian.
    // Pulihkan sebelum produksi: ->middleware('throttle:5,15')
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'me']);
        Route::get('items', [ItemController::class, 'index']);
        Route::get('items/{item}', [ItemController::class, 'show']);
        Route::get('loan-applications', [LoanApplicationController::class, 'index']);
        Route::get('loan-applications/{loanApplication}', [LoanApplicationController::class, 'show']);
        Route::post('loan-applications', [LoanApplicationController::class, 'store']);
    });
});
