<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Stop;

class StopController extends Controller
{
        public function getNearbyStops(Request $request)
    {
        \Log::info('getNearbyStops appelé');
        \Log::info('Données reçues :', $request->all());

        $latitude = $request->query('lat');
        $longitude = $request->query('lon');

        if (!$latitude || !$longitude) {
            \Log::error('Erreur : Latitude et longitude sont manquantes');
            return response()->json(['error' => 'Latitude et longitude sont requises'], 400);
        }

        try {
            // Correction des noms de colonnes dans la requête
            $stops = DB::table('stops')
                ->select('*', DB::raw("(6371 * acos(cos(radians(?)) * cos(radians(sto_latitude)) * cos(radians(sto_longitude) - radians(?)) + sin(radians(?)) * sin(radians(sto_latitude)))) AS distance"))
                ->orderBy('distance')
                ->limit(5)
                ->setBindings([$latitude, $longitude, $latitude])
                ->get();

            \Log::info('Arrêts trouvés :', $stops->toArray());

            return response()->json(['data' => $stops], 200);

        } catch (\Exception $e) {
            \Log::error('Erreur SQL:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur interne du serveur'], 500);
        }
    }

    public function getAllStop(Request $request)
    {
        \Log::info('getAllStop appelé');
        \Log::info('Données reçues :', $request->all());

        try {
            $stops = DB::table('stops')->select('*')->get();

            \Log::info('Arrêts trouvés :', $stops->toArray());

            return response()->json(['data' => $stops], 200);

        } catch (\Exception $e) {
            \Log::error('Erreur SQL:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur interne du serveur'], 500);
        }
    }

    // 🔹 Récupérer un arrêt par ID
    public function getStopById(Request $request, $id)
    {
        \Log::info('getStopById appelé');
        \Log::info('Données reçues :', $request->all());

        try {
            $stop = DB::table('stops')->where('sto_id', $id)->first();

            if (!$stop) {
                \Log::error('Erreur : Arrêt non trouvé');
                return response()->json(['error' => 'Arrêt non trouvé'], 404);
            }

            \Log::info('Arrêt trouvé :', (array) $stop);

            return response()->json($stop);
        } catch (\Exception $e) {
            \Log::error('Erreur SQL:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur interne du serveur'], 500);
        }
    }
}
