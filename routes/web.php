<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\ResetPasswordController;


use JsonMachine\JsonMachine\JsonMachine;


Route::get('/reset-password', function () {
    return view('auth.reset-password');
});

Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->middleware(['throttle:5,1', 'api']);

Route::get('/reset-success', function () {
    return view('auth.reset-success');
});

Route::view('/mentions-legales', 'legal.mentions-legales');

Route::view('/protection-donnees', 'legal.protection-donnees');


