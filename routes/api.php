<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

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
});
