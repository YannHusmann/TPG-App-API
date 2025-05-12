<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StopController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

Route::get('/test', function () {
    return response()->json(['message' => 'API routes are working!']);
});

// Route de connexion
Route::post('/login', [AuthController::class, 'login']);

// Route d'inscription
Route::post('/register', [UserController::class, 'register']);

// Route forgot password
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);

// Route reset password
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);

// Routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/reports/stats', [ReportController::class, 'getStatsPerStop']);
    Route::get('/reports/filter', [ReportController::class, 'filterReports']);
    Route::put('/reports/{id}/status', [ReportController::class, 'changeStatus']);

    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout']);
    // Récupérer l'utilisateur connecté
    Route::get('/me', [AuthController::class, 'me']);
    // Mise à jour des informations utilisateur
    Route::put('/user/update', [UserController::class, 'update']);
    // supprimer le compte utilisateur
    Route::delete('/user', [UserController::class, 'delete']);

    // Récupérer les arrêts à proximité
    Route::get('/stops', [StopController::class, 'getNearbyStops']);
    // Récupérer tous les arrêts
    Route::get('/stops/all', [StopController::class, 'getAllStop']);
    // Récupérer un arrêt par son nom
    Route::get('/stops/name', [StopController::class, 'getStopByName']);
    // Gestion des signalements
    Route::post('/reports', [ReportController::class, 'createReport']);
    Route::get('/reports', [ReportController::class, 'getMyReports']);
    Route::put('/reports/{id}', [ReportController::class, 'updateReport']);
    Route::delete('/reports/{id}', [ReportController::class, 'deleteReport']);
    Route::get('/reports/all', [ReportController::class, 'getAllReports']);
    // Récupérer les arrêts d'une ligne de bus
    Route::get('/stops/{stop}/routes', [StopController::class, 'getRoutes']);

});
