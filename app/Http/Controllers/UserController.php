<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Log;

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

    // Mise à jour des informations utilisateur (avec image)
    public function update(Request $request)
    {
        Log::info('Début update()', ['user_id' => auth()->id()]);

        $user = auth()->user();

        if (!$user) {
            Log::warning('Utilisateur non authentifié');
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        // Log les données brutes
        Log::info('Request all()', $request->all());
        Log::info('Request files', $request->allFiles());

        // Validation
        $validated = $request->validate([
            'use_username' => ['nullable', 'string', 'max:255'],
            'use_email'    => ['nullable', 'email', 'max:255', 'unique:users,use_email,' . $user->use_id . ',use_id'],
            'use_password' => ['nullable', 'string', 'min:6'],
            'use_avatar'   => ['nullable', 'file', 'mimes:jpeg,jpg,png,gif', 'max:2048'],
        ]);

        // Mot de passe
        if (isset($validated['use_password'])) {
            $validated['use_password'] = Hash::make($validated['use_password']);
        }

        // Avatar
        if ($request->hasFile('use_avatar')) {
            $old = $user->use_avatar;
            if ($old && Storage::disk('public')->exists(str_replace('/storage/', '', $old))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $old));
            }

            $path = $request->file('use_avatar')->store('avatars', 'public');
            $validated['use_avatar'] = '/storage/' . $path;
            Log::info('Nouvel avatar enregistré', ['path' => $validated['use_avatar']]);
        }

        if (empty($validated)) {
            return response()->json(['message' => 'Aucune donnée à modifier'], 200);
        }

        $user->update($validated);

        return response()->json(['message' => 'Mise à jour réussie', 'user' => $user], 200);
    }

    // Suppression de l'utilisateur
    public function delete(Request $request)
    {
        $user = Auth::user();
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }
}
