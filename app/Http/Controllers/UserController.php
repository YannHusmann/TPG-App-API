<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    // Inscription d'un nouvel utilisateur
    public function register(Request $request)
    {
        $request->validate([
            'use_username' => 'required|string|max:255',
            'use_email' => 'required|string|email|max:255|unique:users',
            'use_password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'use_username' => $request->use_username,
            'use_email' => $request->use_email,
            'use_password' => Hash::make($request->use_password),
        ]);

        return response()->json(['message' => 'Utilisateur créé avec succès', 'user' => $user], 201);
    }

    // Mise à jour des informations utilisateur (nécessite authentification)
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'use_username' => 'sometimes|string|max:255',
            'use_email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                function ($attribute, $value, $fail) use ($user) {
                    $exists = User::where('use_email', $value)
                                  ->where('use_id', '!=', $user->use_id)
                                  ->exists();
                    if ($exists) {
                        $fail('L\'email est déjà utilisé par un autre utilisateur.');
                    }
                },
            ],
            'use_password' => 'sometimes|string|min:6',
        ]);

        if ($request->has('use_username')) {
            $user->use_username = $request->use_username;
        }
        if ($request->has('use_email')) {
            $user->use_email = $request->use_email;
        }
        if ($request->has('use_password')) {
            $user->use_password = Hash::make($request->use_password);
        }

        $user->save();

        return response()->json(['message' => 'Informations mises à jour avec succès', 'user' => $user]);
    }
}
