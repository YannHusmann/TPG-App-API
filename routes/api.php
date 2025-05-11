<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StopController;
use App\Http\Controllers\ReportController;

Route::get('/test', function () {
    return response()->json(['message' => 'API routes are working!']);
});

// Route de connexion
Route::post('/login', [AuthController::class, 'login']);

// Route d'inscription
Route::post('/register', [UserController::class, 'register']);

// Routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout']);
    // Récupérer l'utilisateur connecté
    Route::get('/me', [AuthController::class, 'me']);
    // Mise à jour des informations utilisateur
    Route::put('/user/update', [UserController::class, 'update']);
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
});
