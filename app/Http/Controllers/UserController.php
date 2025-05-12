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
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        // Interdiction de modifier le rôle
        if ($request->has('use_role')) {
            return response()->json([
                'message' => 'Vous ne pouvez pas modifier votre rôle.'
            ], 403);
        }

        $validated = $request->validate([
            'use_username' => ['sometimes', 'string', 'max:255'],
            'use_email' => ['sometimes', 'email', 'max:255', 'unique:users,use_email,' . $user->use_id . ',use_id'],
            'use_password' => ['sometimes', 'string', 'min:8'],
        ]);

        if (isset($validated['use_password'])) {
            $validated['use_password'] = Hash::make($validated['use_password']);
        }

        // Ne rien faire si aucune donnée modifiée
        if (empty($validated)) {
            return response()->json(['message' => 'Aucune donnée à mettre à jour.'], 200);
        }

        $user->update($validated);

        return response()->json($user, 200);
    }


    // Suppression de l'utilisateur (nécessite authentification)
    public function delete(Request $request)
    {
        $user = Auth::user();
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }

    
}
