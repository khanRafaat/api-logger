<?php

use Illuminate\Support\Facades\Route;
use RafaatAbtahe\ApiResponseLogger\Http\Controllers\MonitorController;

// Dashboard route (replaces welcome page)
Route::get('/', [MonitorController::class, 'dashboard'])->name('api-response-logger.dashboard');

// API Status and Stats routes
Route::get('/api-status', [MonitorController::class, 'status'])->name('api-response-logger.status');
Route::get('/api-stats', [MonitorController::class, 'stats'])->name('api-response-logger.stats');
