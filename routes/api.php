<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StopController;

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
    // Récupérer un arrêt par son ID
    Route::get('/stops/{id}', [StopController::class, 'getStopById']);
});
