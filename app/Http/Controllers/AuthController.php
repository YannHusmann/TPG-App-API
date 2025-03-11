<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

use App\Models\User;

class AuthController extends Controller
{
    // Connexion
    public function login(Request $request)
    {
        $request->validate([
            'use_email' => 'required|email',
            'use_password' => 'required'
        ]);

        $user = User::where('use_email', $request->use_email)->first();

        if (!$user || !Hash::check($request->use_password, $user->use_password)) {
            return response()->json(['message' => 'Identifiants incorrects'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    // Déconnexion
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Déconnecté']);
    }

    // Récupérer l'utilisateur connecté
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
