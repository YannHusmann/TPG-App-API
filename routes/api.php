<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StopController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\RouteController;

Route::get('/test', function () {
    return response()->json(['message' => 'API routes are working!']);
});

// Routes publiques avec throttling
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/register', [UserController::class, 'register'])->middleware('throttle:5,1');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('throttle:5,1');

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/reports/stats', [ReportController::class, 'getStatsPerStop']);
    Route::get('/reports/filter', [ReportController::class, 'filterReports']);
    Route::put('/reports/{id}/status', [ReportController::class, 'changeStatus']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/user/update', [UserController::class, 'update']);
    Route::delete('/user', [UserController::class, 'delete']);

    Route::get('/stops', [StopController::class, 'getNearbyStops']);
    Route::get('/stops/all', [StopController::class, 'getAllStop']);
    Route::get('/stops/name', [StopController::class, 'getStopByName']);
    Route::get('/stops/{stop}/routes', [StopController::class, 'getRoutes']);
    Route::get('/routes/all', [RouteController::class, 'getAllRoutes']);

    // Signalements avec throttling
    Route::post('/reports', [ReportController::class, 'createReport'])->middleware('throttle:5,1');
    Route::put('/reports/{id}', [ReportController::class, 'updateReport'])->middleware('throttle:5,1');
    Route::delete('/reports/{id}', [ReportController::class, 'deleteReport'])->middleware('throttle:5,1');
    Route::get('/reports/types', [ReportController::class, 'getTypes']);
    Route::get('/reports', [ReportController::class, 'getMyReports']);
    Route::get('/reports/all', [ReportController::class, 'getAllReports']);
    Route::get('/reports/{id}', [ReportController::class, 'getReportById']);

});

