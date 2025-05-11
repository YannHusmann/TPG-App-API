<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use JsonMachine\JsonMachine\JsonMachine;

Route::get('/test-reset-mail', function () {
    $status = Password::sendResetLink(['use_email' => 'yann.husmann@gmail.com']);

    return $status === Password::RESET_LINK_SENT
        ? 'Lien de réinitialisation envoyé !'
        : 'Erreur : ' . __($status);
});
